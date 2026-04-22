@extends('layouts.app')
@section('title', __('analytics.page_edit_funnel'))
@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('user.funnels.index') }}" class="btn-pa-outline" style="padding:0.25rem 0.5rem"><i class="bi bi-arrow-left"></i></a>
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">{{ __('analytics.page_edit_funnel') }}: {{ $funnel->name }}</h4>
</div>

<div class="pa-card" style="max-width:700px">
    <form method="POST" action="{{ route('user.funnels.update', $funnel) }}">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="auth-label">{{ __('analytics.label_funnel_name') }}</label>
            <input type="text" name="name" class="pa-input" required value="{{ old('name', $funnel->name) }}">
        </div>
        <div class="mb-2">
            <label class="auth-label">{{ __('analytics.label_steps') }}</label>
        </div>
        <div id="steps-container">
            @foreach($funnel->steps as $i => $step)
            <div class="step-row d-flex gap-2 mb-2">
                <input type="text" name="steps[{{ $i }}][label]" class="pa-input" placeholder="{{ __('analytics.placeholder_step_label') }}" value="{{ $step->label }}" style="flex:1">
                <input type="text" name="steps[{{ $i }}][path]" class="pa-input" placeholder="{{ __('analytics.placeholder_step_path') }}" value="{{ $step->path }}" required style="flex:1">
                @if($i > 1)
                <button type="button" class="btn-pa-outline" style="padding:0.375rem 0.5rem;color:var(--pa-danger)" onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
                @endif
            </div>
            @endforeach
        </div>
        <button type="button" class="btn-pa-outline mb-3" onclick="addStep()"><i class="bi bi-plus me-1"></i> {{ __('analytics.btn_add_step') }}</button>
        <div class="d-flex gap-2">
            <button type="submit" class="btn-pa-primary">{{ __('analytics.btn_update_funnel') }}</button>
            <a href="{{ route('user.funnels.index') }}" class="btn-pa-outline">{{ __('analytics.btn_cancel') }}</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
var stepCount = {{ $funnel->steps->count() }};
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
