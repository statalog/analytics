@extends('layouts.app')
@section('title', __('analytics.page_goal_report'))
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('user.goals.index') }}" class="btn-pa-outline" style="padding:0.25rem 0.5rem"><i class="bi bi-arrow-left"></i></a>
        <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">{{ $goal->name }}</h4>
    </div>
    @include('components.date-range-picker')
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon mb-2"><i class="bi bi-bullseye"></i></div>
            <div class="stat-value" id="total-completions">-</div>
            <div class="stat-label">{{ __('analytics.goal_total_completions') }}</div>
        </div>
    </div>
</div>

<div class="pa-card mb-4">
    <h6 class="mb-3" style="font-family:'Space Grotesk',sans-serif">{{ __('analytics.trend_over_time') }}</h6>
    <div style="height:250px"><canvas id="goal-chart"></canvas></div>
</div>
@endsection

@push('scripts')
<script>
var goalChart = null;

function loadData() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.goals.report.data", $goal) }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(function(data) {
            document.getElementById('total-completions').textContent = (data.total || 0).toLocaleString();
            var rows = data.chart || [];
            var ctx = document.getElementById('goal-chart').getContext('2d');
            if (goalChart) goalChart.destroy();
            goalChart = new Chart(ctx, {
                type: 'bar',
                data: { labels: rows.map(function(r) { return r.date; }), datasets: [{ label: 'Completions', data: rows.map(function(r) { return parseInt(r.completions || 0); }), backgroundColor: '#0e7dd5', borderRadius: 4, barPercentage: 0.6 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } },
                    scales: { x: { grid: { display: false }, ticks: { color: '#6B7290' } }, y: { grid: { color: 'rgba(0,0,0,0.06)' }, ticks: { color: '#6B7290' }, beginAtZero: true } }
                }
            });
        });
}

document.addEventListener('DOMContentLoaded', loadData);
</script>
@endpush
