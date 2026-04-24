@extends('layouts.app')
@section('title', __('analytics.page_goals'))
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 font-heading-bold">{{ __('analytics.page_goals') }}</h4>
    <a href="{{ route('user.goals.create') }}" class="btn-pa-primary"><i class="bi bi-plus-lg me-1"></i> {{ __('analytics.btn_create_goal') }}</a>
</div>

<div class="pa-card" style="padding:0">
    <table class="pa-table">
        <thead>
            <tr>
                <th>{{ __('analytics.label_name') }}</th>
                <th>{{ __('analytics.col_target_path') }}</th>
                <th>{{ __('analytics.col_match_type') }}</th>
                <th class="text-end">{{ __('analytics.col_completions') }}</th>
                <th class="text-end">Revenue</th>
                <th>{{ __('analytics.label_actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($goals as $goal)
            <tr>
                <td class="fw-medium">{{ $goal['name'] }}</td>
                <td><code style="color:var(--pa-primary);font-size:0.8125rem">{{ $goal['target_path'] }}</code></td>
                <td class="text-sm-muted">{{ $goal['match_type'] }}</td>
                <td class="text-num">{{ number_format($goal['completions']) }}</td>
                <td class="text-num">
                    @if($goal['monetary_value'] > 0)
                        ${{ number_format($goal['revenue'], 2) }}
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('user.goals.report', $goal['id']) }}" class="btn-pa-outline" style="padding:0.25rem 0.5rem;font-size:0.75rem"><i class="bi bi-graph-up"></i></a>
                        <a href="{{ route('user.goals.edit', $goal['id']) }}" class="btn-pa-outline" style="padding:0.25rem 0.5rem;font-size:0.75rem"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="{{ route('user.goals.destroy', $goal['id']) }}">
                            @csrf @method('DELETE')
                            <button type="submit" data-pa-confirm="delete-goal" class="btn-pa-outline" style="padding:0.25rem 0.5rem;font-size:0.75rem;color:var(--pa-danger)"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center" style="padding:3rem;color:var(--pa-text-muted)">{{ __('analytics.no_goals') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<x-confirm-modal
    id="delete-goal"
    variant="danger"
    icon="exclamation-triangle"
    title="Delete goal?"
    :body="__('analytics.confirm_delete_goal')"
    confirmLabel="{{ __('app.action_delete') }}"
/>
@endsection
