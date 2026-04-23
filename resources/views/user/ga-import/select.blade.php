@extends('layouts.app')
@section('title', 'Choose a GA property')
@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('user.ga-import') }}" class="btn-pa-outline" style="padding:0.25rem 0.5rem"><i class="bi bi-arrow-left"></i></a>
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">Choose a GA property to import</h4>
</div>

@if(empty($properties))
<div class="pa-card" style="max-width:720px">
    <div style="font-weight:600;margin-bottom:0.5rem"><i class="bi bi-exclamation-triangle me-1" style="color:var(--pa-warning)"></i>No GA4 properties found</div>
    <div style="font-size:0.875rem;color:var(--pa-text-muted)">Make sure the Google account you connected has access to at least one GA4 property. Universal Analytics properties are no longer supported.</div>
</div>
@else
<div class="pa-card" style="max-width:720px">
    <form method="POST" action="{{ route('user.ga-import.start') }}">
        @csrf

        <div class="mb-3">
            <label class="auth-label">Google Analytics property</label>
            <select name="ga_property_id" class="pa-input" required onchange="document.querySelector('[name=property_name]').value = this.options[this.selectedIndex].text">
                <option value="">— Choose a property —</option>
                @foreach($properties as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
            <input type="hidden" name="property_name">
        </div>

        <div class="mb-3">
            <label class="auth-label">Import into Statalog site</label>
            <select name="site_id" class="pa-input" required>
                <option value="">— Choose a site —</option>
                @foreach($sites as $site)
                    <option value="{{ $site->id }}">{{ $site->name }} ({{ $site->domain }})</option>
                @endforeach
            </select>
            <small style="color:var(--pa-text-muted);font-size:0.8125rem">The imported historical data will be attached to this Statalog site.</small>
        </div>

        <div class="mb-3">
            <label class="auth-label">How much history</label>
            <select name="months" class="pa-input">
                <option value="1">Last 1 month</option>
                <option value="3">Last 3 months</option>
                <option value="6">Last 6 months</option>
                <option value="12" selected>Last 12 months</option>
                <option value="14">Last 14 months (GA4 max)</option>
            </select>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn-pa-primary"><i class="bi bi-cloud-download me-1"></i>Start import</button>
            <a href="{{ route('user.ga-import') }}" class="btn-pa-outline">Cancel</a>
        </div>
    </form>
</div>
@endif
@endsection
