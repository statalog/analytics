@extends('layouts.app')
@section('title', __('analytics.page_create_funnel'))
@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('user.funnels.index') }}" class="btn-pa-outline" style="padding:0.25rem 0.5rem"><i class="bi bi-arrow-left"></i></a>
    <h4 class="mb-0 font-heading-bold">{{ __('analytics.page_create_funnel') }}</h4>
</div>

<div class="pa-card" style="max-width:700px">
    <form method="POST" action="{{ route('user.funnels.store') }}">
        @csrf
        <div class="mb-3">
            <label class="auth-label">{{ __('analytics.label_funnel_name') }}</label>
            <input type="text" name="name" class="pa-input" required placeholder="{{ __('analytics.placeholder_funnel_name') }}" value="{{ old('name') }}">
        </div>
        <div class="mb-2">
            <label class="auth-label">{{ __('analytics.label_steps_hint') }}</label>
            <div style="font-size:0.8125rem;color:var(--pa-text-muted);margin-bottom:0.5rem">Step label (left) and URL path (right)</div>
        </div>
        <div id="steps-container">
            <div class="step-row d-flex gap-2 mb-2">
                <input type="text" name="steps[0][label]" class="pa-input" placeholder="{{ __('analytics.placeholder_step_label') }}" style="flex:1">
                <input type="text" name="steps[0][path]" class="pa-input" placeholder="{{ __('analytics.placeholder_step_path') }}" required style="flex:1">
            </div>
            <div class="step-row d-flex gap-2 mb-2">
                <input type="text" name="steps[1][label]" class="pa-input" placeholder="{{ __('analytics.placeholder_step_label') }}" style="flex:1">
                <input type="text" name="steps[1][path]" class="pa-input" placeholder="{{ __('analytics.placeholder_step_path') }}" required style="flex:1">
            </div>
        </div>
        <button type="button" class="btn-pa-outline mb-3" onclick="addStep()"><i class="bi bi-plus me-1"></i> {{ __('analytics.btn_add_step') }}</button>
        <div class="d-flex gap-2">
            <button type="submit" class="btn-pa-primary">{{ __('analytics.btn_create_funnel_submit') }}</button>
            <a href="{{ route('user.funnels.index') }}" class="btn-pa-outline">{{ __('analytics.btn_cancel') }}</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
var stepCount = 2;
function addStep() {
    if (stepCount >= 10) return;
    var div = document.createElement('div');
    div.className = 'step-row d-flex gap-2 mb-2';
    div.innerHTML = '<input type="text" name="steps[' + stepCount + '][label]" class="pa-input" placeholder="{{ __('analytics.placeholder_step_label') }}" style="flex:1">' +
        '<input type="text" name="steps[' + stepCount + '][path]" class="pa-input" placeholder="{{ __('analytics.placeholder_step_path') }}" required style="flex:1">' +
        '<button type="button" class="btn-pa-outline" style="padding:0.375rem 0.5rem;color:var(--pa-danger)" onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>';
    document.getElementById('steps-container').appendChild(div);
    stepCount++;
}
</script>
@endpush
