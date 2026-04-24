@extends('layouts.app')
@section('title', __('analytics.page_funnel_report'))
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('user.funnels.index') }}" class="btn-pa-outline" style="padding:0.25rem 0.5rem"><i class="bi bi-arrow-left"></i></a>
        <h4 class="mb-0 font-heading-bold">{{ $funnel->name }}</h4>
    </div>
    @include('components.date-range-picker')
</div>

<div class="pa-card mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted">{{ __('analytics.funnel_overall_conversion') }}</span>
        <span style="font-size:1.5rem;font-weight:700;font-family:'Space Grotesk',sans-serif;color:var(--pa-primary)">{{ $overallRate }}%</span>
    </div>
</div>

@php $maxVisitors = collect($steps)->max('visitors') ?: 1; @endphp
@foreach($steps as $i => $step)

{{-- Time between steps indicator --}}
@if($i > 0 && $step['avg_time_secs'] !== null && $step['avg_time_secs'] > 0)
@php
    $secs = $step['avg_time_secs'];
    if ($secs < 60)       $timeLabel = $secs . 's';
    elseif ($secs < 3600) $timeLabel = round($secs / 60, 1) . 'm';
    else                  $timeLabel = round($secs / 3600, 1) . 'h';
@endphp
<div class="d-flex align-items-center gap-2 mb-2" style="padding-left:1rem;color:var(--pa-text-muted)">
    <i class="bi bi-clock text-xs"></i>
    <span class="text-sm">avg <strong style="color:var(--pa-text)">{{ $timeLabel }}</strong> to reach this step</span>
</div>
@endif

<div class="funnel-step">
    <div class="funnel-info">
        <div class="funnel-label">{{ $step['label'] }}</div>
        <div class="funnel-stats">{{ $step['path'] }} &middot; {{ number_format($step['visitors']) }} {{ __('analytics.funnel_visitors') }}</div>
        @if($i > 0)
        <div class="funnel-dropoff"><i class="bi bi-arrow-down"></i> {{ number_format($step['dropoff']) }} {{ __('analytics.funnel_dropped') }} ({{ $step['conversion_rate'] }}%)</div>
        @endif
    </div>
    <div style="flex:1;min-width:0">
        <div class="funnel-bar" style="width:{{ max(5, ($step['visitors'] / $maxVisitors) * 100) }}%">
            {{ number_format($step['visitors']) }}
        </div>
    </div>
</div>
@endforeach
@endsection
