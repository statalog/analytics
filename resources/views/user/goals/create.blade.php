@extends('layouts.app')
@section('title', __('analytics.page_create_goal'))
@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('user.goals.index') }}" class="btn-pa-outline" style="padding:0.25rem 0.5rem"><i class="bi bi-arrow-left"></i></a>
    <h4 class="mb-0 font-heading-bold">{{ __('analytics.page_create_goal') }}</h4>
</div>

<div class="pa-card" style="max-width:600px">
    <form method="POST" action="{{ route('user.goals.store') }}">
        @csrf
        <div class="mb-3">
            <label class="auth-label">{{ __('analytics.label_goal_name') }}</label>
            <input type="text" name="name" class="pa-input" required placeholder="{{ __('analytics.placeholder_goal_name') }}" value="{{ old('name') }}">
        </div>
        <div class="mb-3">
            <label class="auth-label">{{ __('analytics.label_match_type') }}</label>
            <select name="match_type" class="pa-input">
                <option value="exact" {{ old('match_type') === 'exact' ? 'selected' : '' }}>{{ __('analytics.match_exact') }}</option>
                <option value="contains" {{ old('match_type') === 'contains' ? 'selected' : '' }}>{{ __('analytics.match_contains') }}</option>
                <option value="starts_with" {{ old('match_type') === 'starts_with' ? 'selected' : '' }}>{{ __('analytics.match_starts_with') }}</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="auth-label">{{ __('analytics.label_target_path') }}</label>
            <input type="text" name="target_path" class="pa-input" required placeholder="{{ __('analytics.placeholder_target_path') }}" value="{{ old('target_path') }}">
            <small class="text-sm-muted">{{ __('analytics.hint_target_path') }}</small>
        </div>
        <div class="mb-3">
            <label class="auth-label">{{ __('analytics.label_monetary_value') }} <span style="color:var(--pa-text-muted);font-weight:400">(optional)</span></label>
            <input type="number" name="monetary_value" class="pa-input" step="0.01" min="0" placeholder="0.00" value="{{ old('monetary_value') }}">
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn-pa-primary">{{ __('analytics.btn_create_goal_submit') }}</button>
            <a href="{{ route('user.goals.index') }}" class="btn-pa-outline">{{ __('analytics.btn_cancel') }}</a>
        </div>
    </form>
</div>
@endsection
