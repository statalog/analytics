@extends('layouts.app')
@section('title', __('analytics.page_goal_report'))
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('user.goals.index') }}" class="btn-pa-outline" style="padding:0.25rem 0.5rem"><i class="bi bi-arrow-left"></i></a>
        <h4 class="mb-0 font-heading-bold">{{ $goal->name }}</h4>
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
    @if($goal->monetary_value > 0)
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon mb-2"><i class="bi bi-currency-dollar"></i></div>
            <div class="stat-value" id="total-revenue">-</div>
            <div class="stat-label">Total Revenue</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon mb-2"><i class="bi bi-tag"></i></div>
            <div class="stat-value">${{ number_format($goal->monetary_value, 2) }}</div>
            <div class="stat-label">Value per Conversion</div>
        </div>
    </div>
    @endif
</div>

<div class="pa-card mb-4">
    <h6 class="mb-3 font-heading">{{ __('analytics.trend_over_time') }}</h6>
    <div style="height:250px"><canvas id="goal-chart"></canvas></div>
</div>
@endsection

@push('scripts')
<script>
var goalChart = null;
var hasRevenue = false;

function loadData() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.goals.report.data", $goal) }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(function(data) {
            document.getElementById('total-completions').textContent = (data.total || 0).toLocaleString();
            hasRevenue = data.has_revenue;
            if (hasRevenue && document.getElementById('total-revenue')) {
                document.getElementById('total-revenue').textContent = '$' + parseFloat(data.total_revenue || 0).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
            }

            var rows = data.chart || [];
            var datasets = [{
                label: 'Completions',
                data: rows.map(function(r) { return parseInt(r.completions || 0); }),
                backgroundColor: 'var(--pa-primary)',
                borderRadius: 4,
                barPercentage: 0.6,
                yAxisID: 'y',
            }];

            if (hasRevenue) {
                datasets.push({
                    label: 'Revenue ($)',
                    data: rows.map(function(r) { return parseFloat(r.revenue || 0); }),
                    type: 'line',
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.08)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: rows.length > 30 ? 0 : 3,
                    borderWidth: 2,
                    yAxisID: 'y2',
                });
            }

            var ctx = document.getElementById('goal-chart').getContext('2d');
            if (goalChart) goalChart.destroy();
            goalChart = new Chart(ctx, {
                type: 'bar',
                data: { labels: rows.map(function(r) { return r.date; }), datasets: datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { display: hasRevenue, labels: { boxWidth: 12, font: { size: 12 } } } },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: '#6B7290' } },
                        y: { grid: { color: 'rgba(0,0,0,0.06)' }, ticks: { color: '#6B7290' }, beginAtZero: true, position: 'left' },
                        y2: hasRevenue ? { grid: { display: false }, ticks: { color: '#10b981', callback: function(v) { return '$' + v; } }, beginAtZero: true, position: 'right' } : undefined,
                    }
                }
            });
        });
}

document.addEventListener('DOMContentLoaded', loadData);
</script>
@endpush
