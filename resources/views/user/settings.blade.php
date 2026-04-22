@extends('layouts.app')
@section('title', __('app.nav_settings'))
@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
        <i class="bi bi-gear me-2" style="color:var(--pa-primary)"></i>{{ __('app.nav_settings') }}
    </h4>
</div>

<div class="pa-card" style="max-width:600px">
    <form method="POST" action="{{ route('user.settings.update') }}">
        @csrf @method('PUT')

        <h6 class="mb-3" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
            <i class="bi bi-shield-check me-2" style="color:var(--pa-primary)"></i>Privacy
        </h6>

        <div class="mb-4">
            <label class="auth-label">Excluded IP Addresses</label>
            <textarea name="excluded_ips" class="pa-input" rows="4" placeholder="Enter IP addresses to exclude, one per line or comma-separated">{{ old('excluded_ips', $excludedIps) }}</textarea>
            <small style="color:var(--pa-text-muted);font-size:0.8125rem">Hits from these IPs will not be recorded. Enter valid IPv4 addresses.</small>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-4" style="padding:0.75rem;background:var(--pa-input-bg);border:1px solid var(--pa-border);border-radius:var(--pa-radius)">
            <div>
                <label class="mb-0" style="font-weight:600;font-size:0.875rem">Hide City-Level Data</label>
                <div style="font-size:0.8125rem;color:var(--pa-text-muted);margin-top:0.125rem">Only country and region will be recorded, not cities.</div>
            </div>
            <label style="position:relative;display:inline-block;width:40px;height:22px;flex-shrink:0;cursor:pointer;margin-left:1rem">
                <input type="hidden" name="hide_cities" value="0">
                <input type="checkbox" name="hide_cities" value="1" {{ $hideCities ? 'checked' : '' }} style="opacity:0;width:0;height:0;position:absolute">
                <span class="toggle-track"></span><span class="toggle-dot"></span>
            </label>
        </div>

        <button type="submit" class="btn-pa-primary">
            <i class="bi bi-check-lg me-1"></i> Save Settings
        </button>
    </form>
</div>
@endsection
