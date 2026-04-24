@extends('layouts.app')
@section('title', 'Performance')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0 font-heading-bold">
        <i class="bi bi-speedometer me-2 icon-primary"></i>Performance
    </h4>
    @include('components.date-range-picker')
</div>

<div class="pa-card mb-4">
    <h6 class="mb-3 font-heading-bold">Page load time over time</h6>
    <div style="height:300px"><canvas id="perf-chart"></canvas></div>
</div>

<div class="pa-card">
    <h6 class="mb-3 font-heading-bold">Performance overview</h6>
    <div id="perf-overview" class="row g-3">
        <div class="col-12 text-center py-3"><div class="spinner-border text-secondary" role="status"></div></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var perfChart = null;

var METRICS = [
    { key: 'avg_network',        label: 'Network time',        color: '#3B82F6' },
    { key: 'avg_server',         label: 'Server time',         color: '#F59E0B' },
    { key: 'avg_transfer',       label: 'Transfer time',       color: '#EC4899' },
    { key: 'avg_dom_processing', label: 'DOM processing time', color: '#8B5CF6' },
    { key: 'avg_dom_completion', label: 'DOM completion time', color: '#10B981' },
    { key: 'avg_on_load',        label: 'On load time',        color: '#06B6D4' },
    { key: 'avg_load',           label: 'Page load time',      color: '#6366F1' },
];

function fmtMs(ms) {
    ms = Math.round(ms || 0);
    if (ms === 0) return '—';
    if (ms < 1000) return ms + ' ms';
    return (ms / 1000).toFixed(2) + ' s';
}

function loadData() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.performance.data") }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(function(data) { render(data); });
}

function render(data) {
    var chart  = data.chart || [];
    var overview = data.overview || {};

    // Chart
    var labels = chart.map(function(r) { return r.date; });
    var datasets = METRICS.map(function(m) {
        return {
            label: m.label,
            data: chart.map(function(r) { return Math.round(parseFloat(r[m.key] || 0)); }),
            borderColor: m.color,
            backgroundColor: 'transparent',
            borderWidth: 2,
            pointRadius: chart.length <= 14 ? 3 : 0,
            tension: 0.3,
        };
    });

    var ctx = document.getElementById('perf-chart').getContext('2d');
    if (perfChart) perfChart.destroy();
    perfChart = new Chart(ctx, {
        type: 'line',
        data: { labels: labels, datasets: datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11 } } },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return ' ' + ctx.dataset.label + ': ' + fmtMs(ctx.parsed.y);
                        }
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#6B7290', maxTicksLimit: 10 } },
                y: {
                    grid: { color: 'rgba(0,0,0,0.06)' },
                    ticks: {
                        color: '#6B7290',
                        callback: function(v) { return v >= 1000 ? (v/1000).toFixed(1) + 's' : v + 'ms'; }
                    },
                    beginAtZero: true
                }
            }
        }
    });

    // Overview grid
    var html = '';
    METRICS.forEach(function(m) {
        var val = parseFloat(overview[m.key] || 0);
        html += '<div class="col-md-6 col-lg-4">'
              + '<div style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem;background:var(--pa-input-bg);border-radius:8px">'
              + '<div style="width:12px;height:12px;border-radius:50%;background:' + m.color + ';flex-shrink:0"></div>'
              + '<div>'
              + '<div style="font-size:1.25rem;font-weight:700">' + fmtMs(val) + '</div>'
              + '<div class="text-sm-muted">' + m.label + '</div>'
              + '</div></div></div>';
    });
    document.getElementById('perf-overview').innerHTML = html;
}

document.addEventListener('DOMContentLoaded', loadData);
window.addEventListener('popstate', loadData);
document.addEventListener('dateRangeChanged', loadData);
</script>
@endpush
