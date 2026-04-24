@extends('layouts.app')
@section('title', __('analytics.page_new_vs_returning'))
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0 font-heading-bold">{{ __('analytics.page_new_vs_returning') }}</h4>
    @include('components.date-range-picker')
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="pa-card" style="height:260px">
            <h6 class="mb-2 font-heading">Distribution</h6>
            <div style="position:relative;width:180px;height:180px;margin:0 auto">
                <canvas id="nvr-donut" width="180" height="180"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="pa-card" style="padding:0">
            <div id="nvr-table"><div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div></div>
        </div>
    </div>
</div>

<div class="pa-card">
    <h6 class="mb-3 font-heading">Over time</h6>
    <div style="height:220px;position:relative">
        <canvas id="nvr-line"></canvas>
    </div>
</div>

@endsection

@push('scripts')
<script>
var donutChart = null;
var lineChart  = null;

function loadData() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.new-vs-returning.data") }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var segments = data.segments || [];
            var chart    = data.chart    || [];

            // Fallback: derive totals from chart data if segments query returned nothing
            if (!segments.length && chart.length) {
                var totalNew = chart.reduce(function(s, r) { return s + parseInt(r.new_visitors || 0); }, 0);
                var totalRet = chart.reduce(function(s, r) { return s + parseInt(r.returning_visitors || 0); }, 0);
                segments = [
                    { segment: 'New',       visitors: totalNew, sessions: totalNew },
                    { segment: 'Returning', visitors: totalRet, sessions: totalRet },
                ];
            }

            renderDistribution(segments);
            renderTimeline(chart);
        });
}

function renderDistribution(rows) {
    // Normalize: segment may be 'New'/'Returning' or numeric 0/1
    var newRow = rows.find(function(r) {
        return r.segment === 'New' || r.segment === 1 || r.segment === '1';
    }) || {};
    var retRow = rows.find(function(r) {
        return r.segment === 'Returning' || r.segment === 0 || r.segment === '0';
    }) || {};

    var newVal = parseInt(newRow.visitors || 0);
    var retVal = parseInt(retRow.visitors || 0);
    var total  = newVal + retVal;

    // Donut
    var ctx = document.getElementById('nvr-donut').getContext('2d');
    if (donutChart) donutChart.destroy();
    donutChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['New', 'Returning'],
            datasets: [{
                data: total > 0 ? [newVal, retVal] : [1, 1],
                backgroundColor: total > 0 ? [paColor(), paColor(0.45)] : ['#e5e7eb', '#e5e7eb'],
                borderWidth: 0,
                hoverOffset: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: { display: false },
                tooltip: { enabled: total > 0 }
            }
        }
    });

    // Table
    var html = '<table class="pa-table" style="width:100%">'
        + '<thead><tr><th>Visitor Type</th><th class="text-end">Visitors</th><th class="text-end">Sessions</th><th class="text-end">Share</th></tr></thead><tbody>';

    [[newRow, 'New', paColor()], [retRow, 'Returning', paColor(0.45)]].forEach(function(item) {
        var row = item[0]; var label = item[1]; var color = item[2];
        var v = parseInt(row.visitors || 0);
        var s = parseInt(row.sessions || 0);
        var pct = total > 0 ? Math.round(v / total * 100) : 0;
        html += '<tr>'
            + '<td><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:' + color + ';margin-right:8px"></span>' + label + '</td>'
            + '<td class="text-end">' + v.toLocaleString() + '</td>'
            + '<td class="text-end">' + s.toLocaleString() + '</td>'
            + '<td class="text-end">'
            + '<div class="d-flex align-items-center justify-content-end gap-2">'
            + '<div style="width:80px;height:4px;border-radius:2px;background:var(--pa-border)">'
            + '<div style="width:' + pct + '%;height:100%;border-radius:2px;background:' + color + '"></div></div>'
            + pct + '%</div></td>'
            + '</tr>';
    });
    html += '</tbody></table>';
    document.getElementById('nvr-table').innerHTML = html;
}

function renderTimeline(rows) {
    var labels   = rows.map(function(r) { return r.date; });
    var newData  = rows.map(function(r) { return parseInt(r.new_visitors || 0); });
    var retData  = rows.map(function(r) { return parseInt(r.returning_visitors || 0); });

    var ctx = document.getElementById('nvr-line').getContext('2d');
    if (lineChart) lineChart.destroy();
    lineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'New',
                    data: newData,
                    borderColor: paColor(),
                    backgroundColor: paColor(0.08),
                    fill: true,
                    tension: 0.4,
                    pointRadius: labels.length > 30 ? 0 : 3,
                    borderWidth: 2,
                },
                {
                    label: 'Returning',
                    data: retData,
                    borderColor: paColor(0.55),
                    backgroundColor: paColor(0.04),
                    fill: true,
                    tension: 0.4,
                    pointRadius: labels.length > 30 ? 0 : 3,
                    borderWidth: 2,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                x: { grid: { display: false }, ticks: { maxTicksLimit: 10, font: { size: 11 } } },
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { size: 11 } } }
            },
            plugins: { legend: { labels: { boxWidth: 12, font: { size: 12 } } } }
        }
    });
}

document.addEventListener('DOMContentLoaded', loadData);
</script>
@endpush
