@extends('layouts.app')
@section('title', __('analytics.page_funnels'))
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">{{ __('analytics.page_funnels') }}</h4>
    <a href="{{ route('user.funnels.create') }}" class="btn-pa-primary"><i class="bi bi-plus-lg me-1"></i> {{ __('analytics.btn_create_funnel') }}</a>
</div>

@forelse($funnels as $funnel)
<div class="pa-card mb-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h6 class="mb-1">{{ $funnel->name }}</h6>
            <span style="color:var(--pa-text-muted);font-size:0.8125rem">{{ $funnel->steps->count() }} {{ __('analytics.label_steps') }}</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('user.funnels.report', $funnel) }}" class="btn-pa-primary" style="padding:0.375rem 0.75rem;font-size:0.8125rem"><i class="bi bi-graph-up me-1"></i> {{ __('analytics.btn_report') }}</a>
            <a href="{{ route('user.funnels.edit', $funnel) }}" class="btn-pa-outline" style="padding:0.375rem 0.75rem"><i class="bi bi-pencil"></i></a>
            <form method="POST" action="{{ route('user.funnels.destroy', $funnel) }}">
                @csrf @method('DELETE')
                <button type="submit" data-pa-confirm="delete-funnel" class="btn-pa-outline" style="padding:0.375rem 0.75rem;color:var(--pa-danger);border-color:var(--pa-border)"><i class="bi bi-trash"></i></button>
            </form>
        </div>
    </div>
</div>
@empty
<div class="pa-card">
    <div class="pa-empty-state">
        <i class="bi bi-funnel"></i>
        <h5>{{ __('analytics.no_funnels') }}</h5>
        <p>Create a funnel to track multi-step conversion paths.</p>
        <a href="{{ route('user.funnels.create') }}" class="btn-pa-primary">{{ __('analytics.btn_create_funnel') }}</a>
    </div>
</div>
@endforelse

<x-confirm-modal
    id="delete-funnel"
    variant="danger"
    icon="exclamation-triangle"
    title="Delete funnel?"
    :body="__('analytics.confirm_delete_funnel')"
    confirmLabel="{{ __('app.action_delete') }}"
/>
@endsection
