<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Concerns\HasDateRange;
use App\Repositories\AnalyticsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SeoToolsController extends Controller
{
    use HasDateRange;

    public function __construct(protected AnalyticsRepository $analytics) {}

    // -------------------------------------------------------------------------
    // Sitemap Checker
    // -------------------------------------------------------------------------

    public function sitemap(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return redirect()->route('user.sites.create');
        return view('user.seo.sitemap', ['site' => $site, 'breadcrumbs' => [['label' => 'SEO Tools'], ['label' => 'Sitemap']]]);
    }

    public function sitemapCheck(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return response()->json(['error' => 'No site selected.']);

        $domain = 'https://' . ltrim($site->domain, '/');
        $result = $this->fetchAndParseSitemap($domain . '/sitemap.xml');

        // Also check robots.txt for Sitemap: directive
        if (empty($result['urls'])) {
            try {
                $robots = Http::timeout(8)->get($domain . '/robots.txt');
                if ($robots->successful()) {
                    preg_match_all('/^Sitemap:\s*(.+)$/mi', $robots->body(), $m);
                    foreach ($m[1] as $sitemapUrl) {
                        $result = $this->fetchAndParseSitemap(trim($sitemapUrl));
                        if (!empty($result['urls'])) break;
                    }
                }
            } catch (\Throwable) {}
        }

        return response()->json($result);
    }

    private function fetchAndParseSitemap(string $url): array
    {
        try {
            $response = Http::timeout(10)->withHeaders(['User-Agent' => 'Statalog/1.0'])->get($url);
        } catch (\Throwable $e) {
            return ['error' => 'Could not fetch sitemap: ' . $e->getMessage(), 'urls' => [], 'url' => $url];
        }

        if ($response->failed()) {
            return ['error' => 'HTTP ' . $response->status() . ' — sitemap not found at ' . $url, 'urls' => [], 'url' => $url];
        }

        $xml = @simplexml_load_string($response->body());
        if ($xml === false) {
            return ['error' => 'Could not parse XML. The sitemap may be malformed.', 'urls' => [], 'url' => $url];
        }

        $urls = [];
        $issues = [];

        // Sitemap index
        if (isset($xml->sitemap)) {
            $childUrls = [];
            foreach ($xml->sitemap as $s) {
                $childUrls[] = (string) $s->loc;
            }
            return [
                'url'        => $url,
                'type'       => 'index',
                'sitemaps'   => $childUrls,
                'count'      => count($childUrls),
                'urls'       => [],
                'issues'     => [],
            ];
        }

        // Regular sitemap
        foreach ($xml->url as $u) {
            $loc     = (string) ($u->loc ?? '');
            $lastmod = (string) ($u->lastmod ?? '');
            $changefreq = (string) ($u->changefreq ?? '');
            $priority   = (string) ($u->priority ?? '');
            if ($loc) {
                $urls[] = compact('loc', 'lastmod', 'changefreq', 'priority');
            }
        }

        if (empty($urls)) {
            $issues[] = 'Sitemap contains no <url> entries.';
        }
        if (count($urls) > 0 && count(array_filter($urls, fn($u) => $u['lastmod'])) === 0) {
            $issues[] = 'No <lastmod> dates found — Google recommends including them.';
        }

        return [
            'url'    => $url,
            'type'   => 'urlset',
            'count'  => count($urls),
            'urls'   => array_slice($urls, 0, 200),
            'issues' => $issues,
        ];
    }

    // -------------------------------------------------------------------------
    // Robots.txt Viewer
    // -------------------------------------------------------------------------

    public function robots(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return redirect()->route('user.sites.create');
        return view('user.seo.robots', ['site' => $site, 'breadcrumbs' => [['label' => 'SEO Tools'], ['label' => 'Robots.txt']]]);
    }

    public function robotsCheck(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return response()->json(['error' => 'No site selected.']);

        $domain = 'https://' . ltrim($site->domain, '/');

        try {
            $response = Http::timeout(8)->withHeaders(['User-Agent' => 'Statalog/1.0'])->get($domain . '/robots.txt');
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Could not fetch robots.txt: ' . $e->getMessage()]);
        }

        if ($response->status() === 404) {
            return response()->json(['error' => '404 — No robots.txt found. This is not critical but recommended.', 'content' => '']);
        }
        if ($response->failed()) {
            return response()->json(['error' => 'HTTP ' . $response->status()]);
        }

        $content = $response->body();
        $lines   = explode("\n", $content);
        $issues  = [];
        $hasSitemap = false;

        foreach ($lines as $line) {
            $line = trim($line);
            if (stripos($line, 'Disallow: /') === 0 && strlen($line) === 11) {
                $issues[] = ['level' => 'error', 'message' => '"Disallow: /" blocks all search engines from indexing your entire site.'];
            }
            if (stripos($line, 'Sitemap:') === 0) {
                $hasSitemap = true;
            }
            if (preg_match('/Disallow:.*\.(css|js)$/i', $line)) {
                $issues[] = ['level' => 'warning', 'message' => 'Blocking CSS or JS files can prevent Google from rendering your pages correctly: ' . $line];
            }
        }

        if (!$hasSitemap) {
            $issues[] = ['level' => 'info', 'message' => 'No Sitemap: directive found. Adding one helps search engines discover your content faster.'];
        }

        return response()->json([
            'content' => $content,
            'issues'  => $issues,
            'size'    => strlen($content),
            'lines'   => count($lines),
        ]);
    }

    // -------------------------------------------------------------------------
    // Broken Links
    // -------------------------------------------------------------------------

    public function brokenLinks(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return redirect()->route('user.sites.create');
        return view('user.seo.broken-links', ['site' => $site, 'breadcrumbs' => [['label' => 'SEO Tools'], ['label' => 'Broken Links']]]);
    }

    public function brokenLinksScan(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return response()->json(['error' => 'No site selected.']);

        $repo = $this->analyticsFor($site);
        $urls = $repo->getTopPageUrls($site->site_id, 80);

        if (empty($urls)) {
            return response()->json(['results' => [], 'message' => 'No URLs found in analytics data yet.']);
        }

        $results = [];
        foreach ($urls as $url) {
            try {
                $response = Http::timeout(6)
                    ->withHeaders(['User-Agent' => 'Statalog-LinkChecker/1.0'])
                    ->withOptions(['allow_redirects' => ['max' => 5, 'strict' => false, 'track_redirects' => true]])
                    ->head($url);

                $status = $response->status();
                if ($status >= 400) {
                    $results[] = ['url' => $url, 'status' => $status, 'type' => $status >= 500 ? 'error' : 'broken'];
                }
            } catch (\Throwable) {
                $results[] = ['url' => $url, 'status' => 0, 'type' => 'unreachable'];
            }
        }

        return response()->json([
            'results'  => $results,
            'scanned'  => count($urls),
            'broken'   => count($results),
        ]);
    }

    // -------------------------------------------------------------------------
    // Redirect Checker
    // -------------------------------------------------------------------------

    public function redirectChecker(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return redirect()->route('user.sites.create');
        return view('user.seo.redirect-checker', ['site' => $site, 'breadcrumbs' => [['label' => 'SEO Tools'], ['label' => 'Redirect Checker']]]);
    }

    public function redirectCheckerCheck(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return response()->json(['error' => 'No site selected.']);

        $url = trim($request->input('url', ''));
        if (!$url) return response()->json(['error' => 'No URL provided.']);
        if (!str_starts_with($url, 'http')) $url = 'https://' . $url;

        $siteDomain = parse_url('https://' . $site->domain, PHP_URL_HOST);
        $inputHost  = parse_url($url, PHP_URL_HOST);
        if ($inputHost && $siteDomain && !str_ends_with($inputHost, $siteDomain) && $inputHost !== $siteDomain) {
            return response()->json(['error' => 'URL must belong to the selected site (' . $site->domain . ').']);
        }

        $chain   = [];
        $visited = [];
        $current = $url;
        $maxHops = 10;

        for ($i = 0; $i < $maxHops; $i++) {
            if (in_array($current, $visited)) {
                $chain[] = ['url' => $current, 'status' => 0, 'error' => 'Redirect loop detected'];
                break;
            }
            $visited[] = $current;

            try {
                $response = Http::timeout(8)
                    ->withHeaders(['User-Agent' => 'Statalog/1.0'])
                    ->withOptions(['allow_redirects' => false])
                    ->get($current);

                $status = $response->status();
                $location = $response->header('Location');
                $chain[] = ['url' => $current, 'status' => $status, 'location' => $location];

                if ($status >= 300 && $status < 400 && $location) {
                    // Handle relative redirects
                    if (!str_starts_with($location, 'http')) {
                        $parsed = parse_url($current);
                        $location = ($parsed['scheme'] ?? 'https') . '://' . ($parsed['host'] ?? '') . '/' . ltrim($location, '/');
                    }
                    $current = $location;
                } else {
                    break;
                }
            } catch (\Throwable $e) {
                $chain[] = ['url' => $current, 'status' => 0, 'error' => $e->getMessage()];
                break;
            }
        }

        return response()->json(['chain' => $chain]);
    }

    // -------------------------------------------------------------------------
    // Meta Tags Preview
    // -------------------------------------------------------------------------

    public function metaTags(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return redirect()->route('user.sites.create');
        return view('user.seo.meta-tags', ['site' => $site, 'breadcrumbs' => [['label' => 'SEO Tools'], ['label' => 'Meta Tags']]]);
    }

    public function metaTagsCheck(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return response()->json(['error' => 'No site selected.']);

        $url = trim($request->input('url', ''));
        if (!$url) return response()->json(['error' => 'No URL provided.']);
        if (!str_starts_with($url, 'http')) $url = 'https://' . $url;

        $siteDomain = parse_url('https://' . $site->domain, PHP_URL_HOST);
        $inputHost  = parse_url($url, PHP_URL_HOST);
        if ($inputHost && $siteDomain && $inputHost !== $siteDomain && !str_ends_with($inputHost, '.' . $siteDomain)) {
            return response()->json(['error' => 'URL must belong to the selected site (' . $site->domain . ').']);
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; Statalog/1.0)'])
                ->get($url);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Could not fetch URL: ' . $e->getMessage()]);
        }

        if ($response->failed()) {
            return response()->json(['error' => 'HTTP ' . $response->status()]);
        }

        $html   = $response->body();
        $final  = (string) $response->effectiveUri();
        $issues = [];

        $extract = function (string $pattern) use ($html): string {
            return preg_match($pattern, $html, $m) ? html_entity_decode(trim($m[1]), ENT_QUOTES) : '';
        };

        $title       = $extract('/<title[^>]*>([^<]{1,200})<\/title>/si');
        $description = $extract('/<meta[^>]+name=["\']description["\'][^>]+content=["\']([^"\']{1,500})["\'][^>]*>/si')
                    ?: $extract('/<meta[^>]+content=["\']([^"\']{1,500})["\'][^>]+name=["\']description["\'][^>]*>/si');
        $ogTitle     = $extract('/<meta[^>]+property=["\']og:title["\'][^>]+content=["\']([^"\']{1,200})["\'][^>]*>/si')
                    ?: $extract('/<meta[^>]+content=["\']([^"\']{1,200})["\'][^>]+property=["\']og:title["\'][^>]*>/si');
        $ogDesc      = $extract('/<meta[^>]+property=["\']og:description["\'][^>]+content=["\']([^"\']{1,500})["\'][^>]*>/si')
                    ?: $extract('/<meta[^>]+content=["\']([^"\']{1,500})["\'][^>]+property=["\']og:description["\'][^>]*>/si');
        $ogImage     = $extract('/<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']{1,500})["\'][^>]*>/si')
                    ?: $extract('/<meta[^>]+content=["\']([^"\']{1,500})["\'][^>]+property=["\']og:image["\'][^>]*>/si');
        $canonical   = $extract('/<link[^>]+rel=["\']canonical["\'][^>]+href=["\']([^"\']{1,500})["\'][^>]*>/si')
                    ?: $extract('/<link[^>]+href=["\']([^"\']{1,500})["\'][^>]+rel=["\']canonical["\'][^>]*>/si');
        $robots      = $extract('/<meta[^>]+name=["\']robots["\'][^>]+content=["\']([^"\']{1,200})["\'][^>]*>/si')
                    ?: $extract('/<meta[^>]+content=["\']([^"\']{1,200})["\'][^>]+name=["\']robots["\'][^>]*>/si');
        $twitterCard = $extract('/<meta[^>]+name=["\']twitter:card["\'][^>]+content=["\']([^"\']{1,100})["\'][^>]*>/si')
                    ?: $extract('/<meta[^>]+content=["\']([^"\']{1,100})["\'][^>]+name=["\']twitter:card["\'][^>]*>/si');

        // Issues
        if (!$title) $issues[] = ['level' => 'error', 'message' => 'Missing <title> tag.'];
        elseif (mb_strlen($title) < 30) $issues[] = ['level' => 'warning', 'message' => 'Title is too short (' . mb_strlen($title) . ' chars). Aim for 50–60.'];
        elseif (mb_strlen($title) > 60) $issues[] = ['level' => 'warning', 'message' => 'Title is too long (' . mb_strlen($title) . ' chars). Google truncates above ~60.'];

        if (!$description) $issues[] = ['level' => 'error', 'message' => 'Missing meta description.'];
        elseif (mb_strlen($description) < 70) $issues[] = ['level' => 'warning', 'message' => 'Description is short (' . mb_strlen($description) . ' chars). Aim for 150–160.'];
        elseif (mb_strlen($description) > 160) $issues[] = ['level' => 'warning', 'message' => 'Description is too long (' . mb_strlen($description) . ' chars). Google truncates above ~160.'];

        if (!$ogTitle) $issues[] = ['level' => 'info', 'message' => 'No og:title — social shares will fall back to the page title.'];
        if (!$ogImage) $issues[] = ['level' => 'warning', 'message' => 'No og:image — social share cards will have no image.'];
        if ($canonical && $canonical !== $final && $canonical !== $url) {
            $issues[] = ['level' => 'info', 'message' => 'Canonical points to a different URL: ' . $canonical];
        }
        if ($robots && (stripos($robots, 'noindex') !== false)) {
            $issues[] = ['level' => 'error', 'message' => 'Page is set to noindex — it will not appear in search results.'];
        }

        return response()->json(compact('title', 'description', 'ogTitle', 'ogDesc', 'ogImage', 'canonical', 'robots', 'twitterCard', 'issues', 'final'));
    }

}
