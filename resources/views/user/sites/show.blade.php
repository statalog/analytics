@extends('layouts.app')
@section('title', __('sites.page_show'))
@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('user.sites.index') }}" class="btn-pa-outline" style="padding:0.25rem 0.5rem"><i class="bi bi-arrow-left"></i></a>
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">{{ $site->name }}</h4>
</div>

<form method="POST" action="{{ route('user.sites.update', $site) }}">
    @csrf @method('PUT')
<div class="row g-4">

    <div class="col-lg-6">

        <div class="pa-card mb-4">
            <h6 class="mb-3" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
                <i class="bi bi-globe me-2" style="color:var(--pa-primary)"></i>Website Details
            </h6>

            <div class="mb-3">
                <label class="auth-label">{{ __('sites.field_name') }}</label>
                <input type="text" name="name" class="pa-input @error('name') is-invalid @enderror" required value="{{ old('name', $site->name) }}">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="auth-label">{{ __('sites.field_domain') }}</label>
                <input type="text" name="domain" class="pa-input @error('domain') is-invalid @enderror" required value="{{ old('domain', $site->domain) }}">
                @error('domain')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small style="color:var(--pa-text-muted);font-size:0.8125rem">{{ __('sites.hint_domain') }}</small>
            </div>

            <div class="mb-3">
                <label class="auth-label">{{ __('sites.field_timezone') }}</label>
                <select name="timezone" class="pa-input @error('timezone') is-invalid @enderror">
                    @foreach(timezone_identifiers_list() as $tz)
                    <option value="{{ $tz }}" {{ old('timezone', $site->timezone) === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                    @endforeach
                </select>
                @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small style="color:var(--pa-text-muted);font-size:0.8125rem">{{ __('sites.hint_timezone') }}</small>
            </div>

            <div class="d-flex align-items-center justify-content-between" style="padding:0.75rem;background:var(--pa-input-bg);border:1px solid var(--pa-border);border-radius:var(--pa-radius)">
                <div>
                    <label class="mb-0" style="font-weight:600;font-size:0.875rem">{{ __('sites.field_track_subdomains') }}</label>
                    <div style="font-size:0.8125rem;color:var(--pa-text-muted);margin-top:0.125rem">{{ __('sites.hint_track_subdomains') }}</div>
                </div>
                <label style="position:relative;display:inline-block;width:40px;height:22px;flex-shrink:0;cursor:pointer;margin-left:1rem">
                    <input type="hidden" name="track_subdomains" value="0">
                    <input type="checkbox" name="track_subdomains" value="1" {{ old('track_subdomains', $site->track_subdomains) ? 'checked' : '' }} style="opacity:0;width:0;height:0;position:absolute">
                    <span class="toggle-track"></span><span class="toggle-dot"></span>
                </label>
            </div>
        </div>

    </div>

    <div class="col-lg-6">
        <div class="pa-card mb-4">
            <h6 class="mb-2" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
                <i class="bi bi-code-slash me-2" style="color:var(--pa-primary)"></i>{{ __('sites.tracking_snippet_title') }}
            </h6>
            <p style="font-size:0.8125rem;color:var(--pa-text-muted);margin-bottom:0.75rem">{{ __('sites.tracking_snippet_hint') }}</p>
            <div style="position:relative">
                <pre id="snippet-code" style="background:var(--pa-input-bg);border:1px solid var(--pa-border);border-radius:var(--pa-radius);padding:0.875rem 2.5rem 0.875rem 0.875rem;font-size:0.8125rem;overflow-x:auto;white-space:pre-wrap;word-break:break-all;margin:0">{{ $site->tracking_snippet }}</pre>
                <button type="button" onclick="copySnippet()" title="{{ __('sites.tracking_copy') }}"
                        style="position:absolute;top:0.5rem;right:0.5rem;background:none;border:none;color:var(--pa-text-muted);cursor:pointer;padding:0.25rem">
                    <i class="bi bi-clipboard" id="snippet-icon"></i>
                </button>
            </div>
        </div>

        {{-- Public dashboard --}}
        <div class="pa-card">
            <h6 class="mb-3" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
                <i class="bi bi-share me-2" style="color:var(--pa-primary)"></i>{{ __('sites.public_title') }}
            </h6>

            <div class="d-flex align-items-center justify-content-between mb-3" style="padding:0.75rem;background:var(--pa-input-bg);border:1px solid var(--pa-border);border-radius:var(--pa-radius)">
                <label class="mb-0" style="font-weight:600;font-size:0.875rem">{{ __('sites.public_enable') }}</label>
                <label style="position:relative;display:inline-block;width:40px;height:22px;flex-shrink:0;cursor:pointer;margin-left:1rem">
                    <input type="hidden" name="is_public" value="0">
                    <input type="checkbox" name="is_public" value="1" id="is_public_toggle" {{ old('is_public', $site->is_public) ? 'checked' : '' }} style="opacity:0;width:0;height:0;position:absolute" onchange="togglePublicOptions()">
                    <span class="toggle-track"></span><span class="toggle-dot"></span>
                </label>
            </div>

            <div id="public-options" style="{{ old('is_public', $site->is_public) ? '' : 'display:none' }}">

                @if($site->public_token)
                <div class="mb-3">
                    <label class="auth-label">{{ __('sites.public_url') }}</label>
                    <div class="d-flex gap-2">
                        <input type="text" class="pa-input" readonly value="{{ route('public.dashboard', $site->public_token) }}" id="public-url-input">
                        <button type="button" onclick="copyPublicUrl()" class="btn-pa-outline" style="flex-shrink:0">
                            <i class="bi bi-clipboard" id="pub-url-icon"></i>
                        </button>
                    </div>
                </div>
                @endif

                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="auth-label mb-0">{{ __('sites.public_password_label') }}</label>
                        <label style="position:relative;display:inline-block;width:36px;height:20px;flex-shrink:0;cursor:pointer">
                            <input type="hidden" name="password_protected" value="0">
                            <input type="checkbox" name="password_protected" value="1" id="pw_toggle"
                                   {{ old('password_protected', $site->public_password ? true : false) ? 'checked' : '' }}
                                   style="opacity:0;width:0;height:0;position:absolute" onchange="togglePasswordField()">
                            <span class="toggle-track"></span><span class="toggle-dot"></span>
                        </label>
                    </div>
                    <div id="pw-field" style="{{ old('password_protected', $site->public_password ? true : false) ? '' : 'display:none' }}">
                        <input type="password" name="public_password" class="pa-input" placeholder="{{ __('sites.public_password_placeholder') }}">
                    </div>
                </div>

                <div class="mb-0">
                    <label class="auth-label">{{ __('sites.public_sections') }}</label>
                    @php
                        $allSections = ['chart', 'pages', 'sources', 'locations', 'devices', 'browsers', 'os', 'resolutions'];
                        $activeSections = old('public_sections', $site->public_sections ?: $allSections);
                        $sectionLabels = [
                            'chart'       => 'Chart',
                            'pages'       => 'Top Pages',
                            'sources'     => 'Traffic Sources',
                            'locations'   => 'Locations',
                            'devices'     => 'Devices',
                            'browsers'    => 'Browsers',
                            'os'          => 'Operating Systems',
                            'resolutions' => 'Screen Resolutions',
                        ];
                    @endphp
                    <div class="d-flex flex-column gap-2">
                        @foreach($allSections as $section)
                        <div class="d-flex align-items-center justify-content-between" style="padding:0.5rem 0.75rem;background:var(--pa-input-bg);border:1px solid var(--pa-border);border-radius:var(--pa-radius)">
                            <span style="font-size:0.875rem">{{ $sectionLabels[$section] }}</span>
                            <label style="position:relative;display:inline-block;width:36px;height:20px;flex-shrink:0;cursor:pointer;margin-left:1rem">
                                <input type="checkbox" name="public_sections[]" value="{{ $section }}"
                                       {{ in_array($section, (array)$activeSections) ? 'checked' : '' }}
                                       style="opacity:0;width:0;height:0;position:absolute">
                                <span class="toggle-track"></span><span class="toggle-dot"></span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn-pa-primary">{{ __('sites.btn_save') }}</button>
    <a href="{{ route('user.sites.index') }}" class="btn-pa-outline">{{ __('app.action_cancel') }}</a>
</div>
</form>

<div class="d-flex justify-content-end mt-3">
    <form method="POST" action="{{ route('user.sites.destroy', $site) }}" id="delete-site-form">
        @csrf @method('DELETE')
        <button type="submit" class="btn-pa-danger" data-pa-confirm="delete-site">
            <i class="bi bi-trash me-1"></i>{{ __('sites.btn_delete') }}
        </button>
    </form>
</div>

<x-confirm-modal
    id="delete-site"
    variant="danger"
    icon="exclamation-triangle"
    title="Delete website?"
    :body="__('sites.confirm_delete')"
    confirmLabel="{{ __('sites.btn_delete') }}"
/>

@push('scripts')
<script>
function togglePublicOptions() {
    var show = document.getElementById('is_public_toggle').checked;
    document.getElementById('public-options').style.display = show ? '' : 'none';
}
function togglePasswordField() {
    var show = document.getElementById('pw_toggle').checked;
    document.getElementById('pw-field').style.display = show ? '' : 'none';
}
function copySnippet() {
    var text = document.getElementById('snippet-code').textContent;
    navigator.clipboard.writeText(text).then(function() {
        var icon = document.getElementById('snippet-icon');
        icon.className = 'bi bi-check-lg';
        setTimeout(function() { icon.className = 'bi bi-clipboard'; }, 2000);
    });
}
function copyPublicUrl() {
    var el = document.getElementById('public-url-input');
    if (!el) return;
    navigator.clipboard.writeText(el.value).then(function() {
        var icon = document.getElementById('pub-url-icon');
        icon.className = 'bi bi-check-lg';
        setTimeout(function() { icon.className = 'bi bi-clipboard'; }, 2000);
    });
}
</script>
@endpush
@endsection
