@extends('layouts.app')
@section('title', __('configuration.general_title'))
@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <h4 class="mb-0 font-heading-bold">
        <i class="bi bi-sliders me-2 icon-primary"></i>{{ __('configuration.general_title') }}
    </h4>
</div>

<div class="pa-card" style="max-width:600px">
    <form method="POST" action="{{ route('user.general.update') }}">
        @csrf @method('PUT')

        <h6 class="mb-3 font-heading-bold">
            <i class="bi bi-shield-check me-2 icon-primary"></i>{{ __('configuration.general_privacy') }}
        </h6>

        <div class="mb-4">
            <label class="auth-label">{{ __('configuration.general_excluded_ips') }}</label>
            <textarea name="excluded_ips" class="pa-input" rows="4" placeholder="{{ __('configuration.general_excluded_ips_placeholder') }}">{{ old('excluded_ips', $excludedIps) }}</textarea>
            <small class="text-sm-muted">{{ __('configuration.general_excluded_ips_hint') }}</small>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-4" style="padding:0.75rem;background:var(--pa-input-bg);border:1px solid var(--pa-border);border-radius:var(--pa-radius)">
            <div>
                <label class="mb-0 fw-semibold text-sm">{{ __('configuration.general_hide_cities') }}</label>
                <div style="font-size:0.8125rem;color:var(--pa-text-muted);margin-top:0.125rem">{{ __('configuration.general_hide_cities_hint') }}</div>
            </div>
            <label style="position:relative;display:inline-block;width:40px;height:22px;flex-shrink:0;cursor:pointer;margin-left:1rem">
                <input type="hidden" name="hide_cities" value="0">
                <input type="checkbox" name="hide_cities" value="1" {{ $hideCities ? 'checked' : '' }} style="opacity:0;width:0;height:0;position:absolute">
                <span class="toggle-track"></span><span class="toggle-dot"></span>
            </label>
        </div>

        <button type="submit" class="btn-pa-primary">
            <i class="bi bi-check-lg me-1"></i> {{ __('configuration.general_btn_save') }}
        </button>
    </form>
</div>
@endsection
