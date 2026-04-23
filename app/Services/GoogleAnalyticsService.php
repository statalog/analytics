<?php
/**
 * Statalog - Open source web analytics.
 * https://statalog.com
 * @license AGPL-3.0
 *
 * Wraps Google OAuth + Google Analytics Data API (GA4) for historical data
 * import. See config/services.php → google for credentials.
 */

namespace App\Services;

use App\Models\User;
use Google\Client as GoogleClient;
use Google\Service\AnalyticsAdmin;
use Google\Service\AnalyticsData;
use Google\Service\AnalyticsData\DateRange;
use Google\Service\AnalyticsData\Dimension;
use Google\Service\AnalyticsData\Metric;
use Google\Service\AnalyticsData\OrderBy;
use Google\Service\AnalyticsData\OrderByDimensionOrderBy;
use Google\Service\AnalyticsData\OrderByMetricOrderBy;
use Google\Service\AnalyticsData\RunReportRequest;

class GoogleAnalyticsService
{
    /** Build a Google Client with the user's tokens loaded (if any). */
    public function clientForUser(User $user): GoogleClient
    {
        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect_uri'));
        $client->setScopes([
            'https://www.googleapis.com/auth/analytics.readonly',
            'email',
        ]);
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        if ($user->ga_access_token) {
            $client->setAccessToken([
                'access_token'  => $user->ga_access_token,
                'refresh_token' => $user->ga_refresh_token,
                'expires_in'    => $user->ga_token_expires_at?->diffInSeconds(now(), false) * -1 ?? 3600,
                'created'       => $user->ga_token_expires_at?->subHour()->getTimestamp() ?? 0,
            ]);

            // Refresh if expired.
            if ($client->isAccessTokenExpired() && $user->ga_refresh_token) {
                $client->fetchAccessTokenWithRefreshToken($user->ga_refresh_token);
                $this->persistTokens($user, $client->getAccessToken());
            }
        }

        return $client;
    }

    /** Get the OAuth authorization URL for the user to grant access. */
    public function authUrl(User $user): string
    {
        return $this->clientForUser($user)->createAuthUrl();
    }

    /** Exchange the OAuth code for tokens and persist on the user. */
    public function exchangeCode(User $user, string $code): void
    {
        $client = $this->clientForUser($user);
        $token  = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            throw new \RuntimeException('Google OAuth error: ' . ($token['error_description'] ?? $token['error']));
        }

        $this->persistTokens($user, $token);
    }

    /** Forget stored tokens (user disconnecting). */
    public function disconnect(User $user): void
    {
        $user->forceFill([
            'ga_access_token'     => null,
            'ga_refresh_token'    => null,
            'ga_token_expires_at' => null,
        ])->save();
    }

    /** Return [property_id => display_name] for every GA4 property the user can access. */
    public function listProperties(User $user): array
    {
        $client = $this->clientForUser($user);
        $admin  = new AnalyticsAdmin($client);

        $properties = [];

        // Walk accounts, then properties under each.
        $accounts = $admin->accountSummaries->listAccountSummaries();
        foreach ($accounts->getAccountSummaries() ?? [] as $account) {
            foreach ($account->getPropertySummaries() ?? [] as $prop) {
                // property name comes as "properties/12345"
                $id = str_replace('properties/', '', $prop->getProperty());
                $properties[$id] = $prop->getDisplayName() . ' (' . $account->getDisplayName() . ')';
            }
        }

        return $properties;
    }

    /** Return daily totals for a single date within a GA4 property. */
    public function dailyTotals(User $user, string $propertyId, string $date): array
    {
        $client = $this->clientForUser($user);
        $data   = new AnalyticsData($client);

        $request = new RunReportRequest([
            'dateRanges' => [new DateRange(['startDate' => $date, 'endDate' => $date])],
            'metrics'    => [
                new Metric(['name' => 'totalUsers']),
                new Metric(['name' => 'screenPageViews']),
                new Metric(['name' => 'sessions']),
                new Metric(['name' => 'bounceRate']),
                new Metric(['name' => 'averageSessionDuration']),
            ],
        ]);

        $response = $data->properties->runReport('properties/' . $propertyId, $request);
        $row      = $response->getRows()[0] ?? null;

        if (!$row) {
            return ['visitors' => 0, 'pageviews' => 0, 'sessions' => 0, 'bounce_rate' => 0, 'avg_duration' => 0];
        }

        $values = array_map(fn ($v) => $v->getValue(), $row->getMetricValues() ?? []);
        return [
            'visitors'     => (int) ($values[0] ?? 0),
            'pageviews'    => (int) ($values[1] ?? 0),
            'sessions'     => (int) ($values[2] ?? 0),
            'bounce_rate'  => round((float) ($values[3] ?? 0) * 100, 2), // GA returns 0..1
            'avg_duration' => (int) round((float) ($values[4] ?? 0)),
        ];
    }

    /** Top N pages by pageviews across a date range. */
    public function topPages(User $user, string $propertyId, string $from, string $to, int $limit = 50): array
    {
        $client = $this->clientForUser($user);
        $data   = new AnalyticsData($client);

        $request = new RunReportRequest([
            'dateRanges' => [new DateRange(['startDate' => $from, 'endDate' => $to])],
            'dimensions' => [new Dimension(['name' => 'pagePath'])],
            'metrics'    => [new Metric(['name' => 'screenPageViews'])],
            'orderBys'   => [new OrderBy(['metric' => new OrderByMetricOrderBy(['metricName' => 'screenPageViews']), 'desc' => true])],
            'limit'      => $limit,
        ]);

        $response = $data->properties->runReport('properties/' . $propertyId, $request);
        $rows = [];
        foreach ($response->getRows() ?? [] as $row) {
            $rows[] = [
                'page_path' => $row->getDimensionValues()[0]?->getValue() ?? '',
                'pageviews' => (int) ($row->getMetricValues()[0]?->getValue() ?? 0),
            ];
        }
        return $rows;
    }

    /** Top N traffic sources by users across a date range. */
    public function topSources(User $user, string $propertyId, string $from, string $to, int $limit = 20): array
    {
        $client = $this->clientForUser($user);
        $data   = new AnalyticsData($client);

        $request = new RunReportRequest([
            'dateRanges' => [new DateRange(['startDate' => $from, 'endDate' => $to])],
            'dimensions' => [new Dimension(['name' => 'sessionSource'])],
            'metrics'    => [new Metric(['name' => 'totalUsers'])],
            'orderBys'   => [new OrderBy(['metric' => new OrderByMetricOrderBy(['metricName' => 'totalUsers']), 'desc' => true])],
            'limit'      => $limit,
        ]);

        $response = $data->properties->runReport('properties/' . $propertyId, $request);
        $rows = [];
        foreach ($response->getRows() ?? [] as $row) {
            $rows[] = [
                'source'   => $row->getDimensionValues()[0]?->getValue() ?? '(direct)',
                'visitors' => (int) ($row->getMetricValues()[0]?->getValue() ?? 0),
            ];
        }
        return $rows;
    }

    /** Top N countries by users across a date range. */
    public function topCountries(User $user, string $propertyId, string $from, string $to, int $limit = 20): array
    {
        $client = $this->clientForUser($user);
        $data   = new AnalyticsData($client);

        $request = new RunReportRequest([
            'dateRanges' => [new DateRange(['startDate' => $from, 'endDate' => $to])],
            'dimensions' => [new Dimension(['name' => 'country'])],
            'metrics'    => [new Metric(['name' => 'totalUsers'])],
            'orderBys'   => [new OrderBy(['metric' => new OrderByMetricOrderBy(['metricName' => 'totalUsers']), 'desc' => true])],
            'limit'      => $limit,
        ]);

        $response = $data->properties->runReport('properties/' . $propertyId, $request);
        $rows = [];
        foreach ($response->getRows() ?? [] as $row) {
            $rows[] = [
                'country'  => $row->getDimensionValues()[0]?->getValue() ?? 'Unknown',
                'visitors' => (int) ($row->getMetricValues()[0]?->getValue() ?? 0),
            ];
        }
        return $rows;
    }

    protected function persistTokens(User $user, array $token): void
    {
        $user->forceFill([
            'ga_access_token'     => $token['access_token'] ?? null,
            'ga_refresh_token'    => $token['refresh_token'] ?? $user->ga_refresh_token,
            'ga_token_expires_at' => isset($token['expires_in']) ? now()->addSeconds((int) $token['expires_in']) : null,
        ])->save();
    }
}
