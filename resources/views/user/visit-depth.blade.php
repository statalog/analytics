@extends('layouts.app')
@section('title', __('analytics.page_visit_depth'))
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0 font-heading-bold">{{ __('analytics.page_visit_depth') }}</h4>
    @include('components.date-range-picker')
</div>

<div class="pa-card mb-4">
    <h6 class="mb-3 font-heading">{{ __('analytics.visit_depth_distribution') }}</h6>
    <div style="height:300px"><canvas id="depth-chart"></canvas></div>
</div>

<div class="pa-card" style="padding:0">
    <div id="depth-table"><div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div></div>
</div>
@endsection

@push('scripts')
<script>
var depthChart = null;

function loadData() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.visit-depth.data") }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(function(data) { render(data.depth || []); });
}

var __t = {
    pageSingular: @json(__('analytics.depth_pages_singular')),
    pagePlural:   @json(__('analytics.depth_pages_plural')),
};

function depthLabel(depth) {
    var tpl = (depth === '1' || depth === 1) ? __t.pageSingular : __t.pagePlural;
    return tpl.replace(':n', depth);
}

function render(rows) {
    var labels = rows.map(function(r) { return depthLabel(r.depth); });
    var values = rows.map(function(r) { return parseInt(r.sessions || 0); });
    var ctx = document.getElementById('depth-chart').getContext('2d');
    if (depthChart) depthChart.destroy();
    depthChart = new Chart(ctx, {
        type: 'bar',
        data: { labels: labels, datasets: [{ label: @json(__('analytics.col_sessions')), data: values, backgroundColor: paColor(), borderRadius: 4, barPercentage: 0.6 }] },
        options: { responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#6B7290' } },
                y: { grid: { color: 'rgba(0,0,0,0.06)' }, ticks: { color: '#6B7290', callback: function(v) { return Number.isInteger(v) ? v : undefined; } }, beginAtZero: true }
            }
        }
    });

    var total = values.reduce(function(a, b) { return a + b; }, 0);
    var html = '<table class="pa-table"><thead><tr><th>{{ __("analytics.col_pages_visited") }}</th><th class="text-end">{{ __("analytics.col_sessions") }}</th><th class="text-end">{{ __("analytics.col_share") }}</th></tr></thead><tbody>';
    rows.forEach(function(row) {
        var pct = total > 0 ? Math.round(parseInt(row.sessions || 0) / total * 100) : 0;
        html += '<tr><td>' + depthLabel(row.depth) + '</td>';
        html += '<td class="text-end">' + parseInt(row.sessions || 0).toLocaleString() + '</td>';
        html += '<td class="text-end">' + pct + '%</td></tr>';
    });
    if (!rows.length) html += '<tr><td colspan="3" class="text-center text-muted">{{ __("analytics.no_data") }}</td></tr>';
    html += '</tbody></table>';
    document.getElementById('depth-table').innerHTML = html;
}

document.addEventListener('DOMContentLoaded', loadData);
</script>
@endpush
