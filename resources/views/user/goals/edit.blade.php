@extends('layouts.app')
@section('title', __('analytics.page_edit_goal'))
@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('user.goals.index') }}" class="btn-pa-outline" style="padding:0.25rem 0.5rem"><i class="bi bi-arrow-left"></i></a>
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">{{ __('analytics.page_edit_goal') }}: {{ $goal->name }}</h4>
</div>

<div class="pa-card" style="max-width:600px">
    <form method="POST" action="{{ route('user.goals.update', $goal) }}">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="auth-label">{{ __('analytics.label_goal_name') }}</label>
            <input type="text" name="name" class="pa-input" required value="{{ old('name', $goal->name) }}">
        </div>
        <div class="mb-3">
            <label class="auth-label">{{ __('analytics.label_match_type') }}</label>
            <select name="match_type" class="pa-input">
                <option value="exact" {{ old('match_type', $goal->match_type) === 'exact' ? 'selected' : '' }}>{{ __('analytics.match_exact') }}</option>
                <option value="contains" {{ old('match_type', $goal->match_type) === 'contains' ? 'selected' : '' }}>{{ __('analytics.match_contains') }}</option>
                <option value="starts_with" {{ old('match_type', $goal->match_type) === 'starts_with' ? 'selected' : '' }}>{{ __('analytics.match_starts_with') }}</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="auth-label">{{ __('analytics.label_target_path') }}</label>
            <input type="text" name="target_path" class="pa-input" required value="{{ old('target_path', $goal->target_path) }}">
        </div>
        <div class="mb-3">
            <label class="auth-label">{{ __('analytics.label_monetary_value') }} <span style="color:var(--pa-text-muted);font-weight:400">(optional)</span></label>
            <input type="number" name="monetary_value" class="pa-input" step="0.01" min="0" value="{{ old('monetary_value', $goal->monetary_value) }}">
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn-pa-primary">{{ __('analytics.btn_update_goal') }}</button>
            <a href="{{ route('user.goals.index') }}" class="btn-pa-outline">{{ __('analytics.btn_cancel') }}</a>
        </div>
    </form>
</div>
@endsection
