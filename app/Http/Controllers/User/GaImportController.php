<?php
/**
 * Statalog - Open source web analytics.
 * https://statalog.com
 * @license AGPL-3.0
 */

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\BackfillGaImportJob;
use App\Models\GaImport;
use App\Models\Site;
use App\Services\GoogleAnalyticsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GaImportController extends Controller
{
    public function __construct(protected GoogleAnalyticsService $ga) {}

    /** Landing page — lists imports + starts new one. */
    public function index(Request $request): View
    {
        $user    = $request->user();
        $imports = GaImport::where('user_id', $user->id)
            ->with('site')
            ->orderByDesc('created_at')
            ->take(20)
            ->get();

        return view('user.ga-import.index', [
            'user'        => $user,
            'imports'     => $imports,
            'configured'  => $this->isConfigured(),
            'connected'   => (bool) $user->ga_access_token,
            'sites'       => $user->sites,
        ]);
    }

    /** Kick off Google OAuth. */
    public function connect(Request $request): RedirectResponse
    {
        abort_unless($this->isConfigured(), 500, 'Google Analytics import is not configured. Set GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET in .env first.');

        return redirect()->away($this->ga->authUrl($request->user()));
    }

    /** Google redirects back here with ?code=... */
    public function callback(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($request->has('error')) {
            return redirect()->route('user.ga-import')->with('error', 'Google sign-in cancelled.');
        }

        $code = $request->input('code');
        if (!$code) {
            return redirect()->route('user.ga-import')->with('error', 'No authorisation code received from Google.');
        }

        try {
            $this->ga->exchangeCode($user, $code);
            return redirect()->route('user.ga-import')->with('success', 'Connected to Google Analytics. Choose a property to import.');
        } catch (\Throwable $e) {
            return redirect()->route('user.ga-import')->with('error', 'Could not connect: ' . $e->getMessage());
        }
    }

    /** Disconnect — clears stored tokens. */
    public function disconnect(Request $request): RedirectResponse
    {
        $this->ga->disconnect($request->user());
        return redirect()->route('user.ga-import')->with('success', 'Disconnected from Google Analytics.');
    }

    /** Show GA property -> Statalog site mapping form. */
    public function selectProperty(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        if (!$user->ga_access_token) {
            return redirect()->route('user.ga-import');
        }

        try {
            $properties = $this->ga->listProperties($user);
        } catch (\Throwable $e) {
            return redirect()->route('user.ga-import')->with('error', 'Could not list GA properties: ' . $e->getMessage());
        }

        return view('user.ga-import.select', [
            'properties' => $properties,
            'sites'      => $user->sites,
        ]);
    }

    /** Start the backfill job for a property × site pair. */
    public function start(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ga_property_id' => ['required', 'string', 'max:64'],
            'site_id'        => ['required', 'integer'],
            'months'         => ['required', 'integer', 'min:1', 'max:14'],
            'property_name'  => ['nullable', 'string', 'max:255'],
        ]);

        $site = Site::findOrFail($data['site_id']);
        abort_unless($site->user_id === $request->user()->id, 403);

        $from = now()->subMonths($data['months'])->startOfDay();
        $to   = now()->subDay()->endOfDay();

        $import = GaImport::create([
            'user_id'          => $request->user()->id,
            'site_id'          => $site->id,
            'ga_property_id'   => $data['ga_property_id'],
            'ga_property_name' => $data['property_name'] ?? null,
            'from_date'        => $from->toDateString(),
            'to_date'          => $to->toDateString(),
            'status'           => 'queued',
        ]);

        BackfillGaImportJob::dispatch($import->id);

        return redirect()->route('user.ga-import.progress', $import)->with('success', 'Import queued. This may take a few minutes.');
    }

    /** Progress page (AJAX-refreshes). */
    public function progress(GaImport $import): View
    {
        abort_unless($import->user_id === auth()->id(), 403);
        return view('user.ga-import.progress', compact('import'));
    }

    /** JSON endpoint for progress polling. */
    public function progressData(GaImport $import)
    {
        abort_unless($import->user_id === auth()->id(), 403);
        return response()->json([
            'status'         => $import->status,
            'total_days'     => $import->total_days,
            'processed_days' => $import->processed_days,
            'percent'        => $import->progressPercent(),
            'error'          => $import->error_message,
            'done'           => $import->isDone(),
        ]);
    }

    /** Show the imported historical data for a site. */
    public function summary(Site $site): View
    {
        abort_unless($site->user_id === auth()->id(), 403);

        return view('user.ga-import.summary', [
            'site'      => $site,
            'daily'     => DB::table('ga_historical_daily')->where('site_id', $site->id)->orderBy('date')->get(),
            'pages'     => DB::table('ga_historical_pages')->where('site_id', $site->id)->orderBy('rank')->get(),
            'sources'   => DB::table('ga_historical_sources')->where('site_id', $site->id)->orderBy('rank')->get(),
            'countries' => DB::table('ga_historical_countries')->where('site_id', $site->id)->orderBy('rank')->get(),
        ]);
    }

    protected function isConfigured(): bool
    {
        return !empty(config('services.google.client_id')) && !empty(config('services.google.client_secret'));
    }
}
