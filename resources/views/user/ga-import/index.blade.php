@extends('layouts.app')
@section('title', 'Import from Google Analytics')
@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0 font-heading-bold">
        <i class="bi bi-cloud-download me-2 icon-primary"></i>Import from Google Analytics
    </h4>
</div>

<p style="color:var(--pa-text-muted);max-width:720px;margin-bottom:1.5rem">
    Pull historical pageviews, visitors, top pages and top sources from your GA4 property into Statalog. Useful when switching away from Google Analytics — don't lose your past numbers.
</p>

@if(!$configured)
<div class="pa-card" style="border-left:3px solid var(--pa-warning);max-width:720px">
    <div class="fw-semibold mb-2"><i class="bi bi-exclamation-triangle me-1 text-warning"></i>Google OAuth not configured</div>
    <div style="font-size:0.875rem;color:var(--pa-text-muted);line-height:1.65">
        To enable GA import, register an OAuth 2.0 Web app at
        <a class="icon-primary" href="https://console.cloud.google.com/apis/credentials" target="_blank" rel="noopener">console.cloud.google.com</a>,
        enable the <em>Google Analytics Data API</em>, and set the redirect URI to
        <code style="background:var(--pa-input-bg);padding:0.1rem 0.35rem;border-radius:4px">{{ url('/account/ga-import/callback') }}</code>.
        Then add <code>GOOGLE_CLIENT_ID</code>, <code>GOOGLE_CLIENT_SECRET</code> and <code>GOOGLE_REDIRECT_URI</code> to your <code>.env</code> file and refresh.
    </div>
</div>
@elseif(!$connected)
<div class="pa-card" style="max-width:720px">
    <div class="fw-semibold mb-2">Step 1 — Sign in with Google</div>
    <div style="font-size:0.875rem;color:var(--pa-text-muted);margin-bottom:1rem">
        We'll request read-only access to your Google Analytics. We never see your Google account data and you can disconnect any time.
    </div>
    <form method="POST" action="{{ route('user.ga-import.connect') }}">
        @csrf
        <button type="submit" class="btn-pa-primary">
            <i class="bi bi-google me-1"></i>Connect with Google
        </button>
    </form>
</div>
@else
<div class="row g-4" style="max-width:900px">
    <div class="col-md-8">
        <div class="pa-card">
            <div style="font-family:'Space Grotesk',sans-serif;font-weight:700;margin-bottom:0.5rem">
                <i class="bi bi-check-circle-fill me-1 text-success"></i>Connected to Google Analytics
            </div>
            <div style="font-size:0.875rem;color:var(--pa-text-muted);margin-bottom:1.25rem">Pick a GA4 property to import into one of your Statalog sites.</div>
            <a href="{{ route('user.ga-import.select') }}" class="btn-pa-primary">
                <i class="bi bi-arrow-right me-1"></i>Continue
            </a>
            <form method="POST" action="{{ route('user.ga-import.disconnect') }}" class="d-inline ms-2">
                @csrf @method('DELETE')
                <button type="submit" class="btn-pa-outline">Disconnect</button>
            </form>
        </div>
    </div>
    <div class="col-md-4">
        <div class="pa-card">
            <div style="font-family:'Space Grotesk',sans-serif;font-weight:700;margin-bottom:0.5rem;font-size:0.9rem">What gets imported</div>
            <ul style="list-style:none;padding:0;margin:0;font-size:0.85rem;line-height:1.9;color:var(--pa-text-muted)">
                <li><i class="bi bi-check2 me-1 icon-primary"></i> Daily visitors, pageviews, sessions</li>
                <li><i class="bi bi-check2 me-1 icon-primary"></i> Bounce rate &amp; avg duration</li>
                <li><i class="bi bi-check2 me-1 icon-primary"></i> Top 50 pages</li>
                <li><i class="bi bi-check2 me-1 icon-primary"></i> Top 20 sources &amp; countries</li>
                <li style="margin-top:0.4rem"><i class="bi bi-info-circle me-1 text-muted"></i> GA4 data, up to 14 months</li>
            </ul>
        </div>
    </div>
</div>
@endif

@if($imports->count())
<h6 class="mt-5 mb-3 font-heading-bold">Recent imports</h6>
<div class="pa-card p-0" style="max-width:900px">
    <table class="pa-table">
        <thead>
            <tr><th>Site</th><th>GA property</th><th>Range</th><th>Status</th><th>Progress</th><th></th></tr>
        </thead>
        <tbody>
            @foreach($imports as $i)
            <tr>
                <td class="fw-medium">{{ $i->site?->name ?? '—' }}</td>
                <td class="text-sm-muted">{{ $i->ga_property_name ?? $i->ga_property_id }}</td>
                <td class="text-sm-muted">{{ $i->from_date?->format('M j, Y') }} – {{ $i->to_date?->format('M j, Y') }}</td>
                <td>
                    @php
                        $color = match($i->status) {
                            'completed' => 'var(--pa-success)',
                            'failed'    => 'var(--pa-danger)',
                            'running'   => 'var(--pa-primary)',
                            default     => 'var(--pa-text-muted)',
                        };
                    @endphp
                    <span style="color:{{ $color }};font-weight:600;font-size:0.8125rem">{{ ucfirst($i->status) }}</span>
                </td>
                <td style="min-width:130px">
                    <div style="height:6px;background:var(--pa-input-bg);border-radius:4px;overflow:hidden">
                        <div style="height:100%;width:{{ $i->progressPercent() }}%;background:{{ $color }}"></div>
                    </div>
                    <div style="font-size:0.7rem;color:var(--pa-text-muted);margin-top:0.25rem">{{ $i->processed_days }}/{{ $i->total_days }} days</div>
                </td>
                <td class="text-end">
                    @if($i->status === 'completed' && $i->site)
                        <a href="{{ route('user.ga-import.summary', $i->site) }}" class="btn-pa-outline" style="padding:0.2rem 0.6rem;font-size:0.8125rem">View</a>
                    @elseif(in_array($i->status, ['queued', 'running']))
                        <a href="{{ route('user.ga-import.progress', $i) }}" class="btn-pa-outline" style="padding:0.2rem 0.6rem;font-size:0.8125rem">Progress</a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
