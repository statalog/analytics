@extends('layouts.app')
@section('title', __('ga-import.page_select'))
@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('user.ga-import') }}" class="btn-pa-outline" style="padding:0.25rem 0.5rem"><i class="bi bi-arrow-left"></i></a>
    <h4 class="mb-0 font-heading-bold">{{ __('ga-import.page_select') }}</h4>
</div>

@if(empty($properties))
<div class="pa-card" style="max-width:720px">
    <div class="fw-semibold mb-2"><i class="bi bi-exclamation-triangle me-1 text-warning"></i>{{ __('ga-import.no_properties_title') }}</div>
    <div class="text-sm-muted">{{ __('ga-import.no_properties_body') }}</div>
</div>
@else
<div class="pa-card" style="max-width:720px">
    <form method="POST" action="{{ route('user.ga-import.start') }}">
        @csrf

        <div class="mb-3">
            <label class="auth-label">{{ __('ga-import.label_property') }}</label>
            <select name="ga_property_id" class="pa-input" required onchange="document.querySelector('[name=property_name]').value = this.options[this.selectedIndex].text">
                <option value="">{{ __('ga-import.choose_property') }}</option>
                @foreach($properties as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
            <input type="hidden" name="property_name">
        </div>

        <div class="mb-3">
            <label class="auth-label">{{ __('ga-import.label_target_site') }}</label>
            <select name="site_id" class="pa-input" required>
                <option value="">{{ __('ga-import.choose_site') }}</option>
                @foreach($sites as $site)
                    <option value="{{ $site->id }}">{{ $site->name }} ({{ $site->domain }})</option>
                @endforeach
            </select>
            <small class="text-sm-muted">{{ __('ga-import.hint_target_site') }}</small>
        </div>

        <div class="mb-3">
            <label class="auth-label">{{ __('ga-import.label_history') }}</label>
            <select name="months" class="pa-input">
                <option value="1">{{ __('ga-import.history_1') }}</option>
                <option value="3">{{ __('ga-import.history_3') }}</option>
                <option value="6">{{ __('ga-import.history_6') }}</option>
                <option value="12" selected>{{ __('ga-import.history_12') }}</option>
                <option value="14">{{ __('ga-import.history_14') }}</option>
            </select>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn-pa-primary"><i class="bi bi-cloud-download me-1"></i>{{ __('ga-import.btn_start_import') }}</button>
            <a href="{{ route('user.ga-import') }}" class="btn-pa-outline">{{ __('ga-import.btn_cancel') }}</a>
        </div>
    </form>
</div>
@endif
@endsection
