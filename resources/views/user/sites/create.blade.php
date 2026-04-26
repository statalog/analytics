@extends('layouts.app')
@section('title', __('sites.page_create'))
@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('user.sites.index') }}" class="btn-pa-outline" style="padding:0.25rem 0.5rem"><i class="bi bi-arrow-left"></i></a>
    <h4 class="mb-0 font-heading-bold">{{ __('sites.page_create') }}</h4>
</div>

<div class="pa-card" style="max-width:600px">
    <form method="POST" action="{{ route('user.sites.store') }}">
        @csrf
        <div class="mb-3">
            <label class="auth-label">{{ __('sites.field_name') }}</label>
            <input type="text" name="name" class="pa-input @error('name') is-invalid @enderror" required
                   placeholder="{{ __('sites.placeholder_name') }}" value="{{ old('name') }}">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="auth-label">{{ __('sites.field_domain') }}</label>
            <input type="text" name="domain" class="pa-input @error('domain') is-invalid @enderror" required
                   placeholder="{{ __('sites.placeholder_domain') }}" value="{{ old('domain') }}">
            @error('domain')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-sm-muted">{{ __('sites.hint_domain') }}</small>
        </div>

        <div class="mb-3">
            <label class="auth-label">{{ __('sites.field_timezone') }}</label>
            <select name="timezone" class="pa-input js-searchable @error('timezone') is-invalid @enderror">
                @foreach(timezone_identifiers_list() as $tz)
                <option value="{{ $tz }}" {{ old('timezone', 'UTC') === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                @endforeach
            </select>
            @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-sm-muted">{{ __('sites.hint_timezone') }}</small>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-3" style="padding:0.75rem;background:var(--pa-input-bg);border:1px solid var(--pa-border);border-radius:var(--pa-radius)">
            <div>
                <label class="mb-0 fw-semibold text-sm">{{ __('sites.field_track_subdomains') }}</label>
                <div style="font-size:0.8125rem;color:var(--pa-text-muted);margin-top:0.125rem">{{ __('sites.hint_track_subdomains') }}</div>
            </div>
            <label style="position:relative;display:inline-block;width:40px;height:22px;flex-shrink:0;cursor:pointer;margin-left:1rem">
                <input type="hidden" name="track_subdomains" value="0">
                <input type="checkbox" name="track_subdomains" value="1" {{ old('track_subdomains') ? 'checked' : '' }} style="opacity:0;width:0;height:0;position:absolute">
                <span class="toggle-track"></span><span class="toggle-dot"></span>
            </label>
        </div>

        <div class="mb-4" style="padding:0.85rem 1rem;background:color-mix(in srgb, var(--pa-success) 6%, var(--pa-input-bg));border:1px solid color-mix(in srgb, var(--pa-success) 25%, var(--pa-border));border-radius:var(--pa-radius)">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-robot" style="color:var(--pa-success);font-size:1.05rem;margin-top:0.15rem;flex-shrink:0"></i>
                <div>
                    <div class="fw-semibold text-sm">{{ __('sites.bot_detection_label') }}</div>
                    <div style="font-size:0.8125rem;color:var(--pa-text-muted);margin-top:0.25rem;line-height:1.5">
                        {{ __('sites.bot_detection_hint') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn-pa-primary">{{ __('sites.btn_save') }}</button>
            <a href="{{ route('user.sites.index') }}" class="btn-pa-outline">{{ __('app.action_cancel') }}</a>
        </div>
    </form>
</div>
@endsection
