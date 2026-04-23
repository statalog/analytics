@extends('layouts.app')
@section('title', 'Errors')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
        <i class="bi bi-bug me-2" style="color:var(--pa-primary)"></i>Errors
    </h4>
    @include('components.date-range-picker')
</div>

<div class="row g-3 mb-4" id="error-stats">
    <div class="col-6 col-md-3"><div class="pa-card"><div style="font-size:0.8125rem;color:var(--pa-text-muted)">Total errors</div><div id="stat-total" style="font-size:1.5rem;font-weight:700;font-family:'Space Grotesk',sans-serif">—</div></div></div>
    <div class="col-6 col-md-3"><div class="pa-card"><div style="font-size:0.8125rem;color:var(--pa-text-muted)">Unique errors</div><div id="stat-unique" style="font-size:1.5rem;font-weight:700;font-family:'Space Grotesk',sans-serif">—</div></div></div>
    <div class="col-6 col-md-3"><div class="pa-card"><div style="font-size:0.8125rem;color:var(--pa-text-muted)">Affected visitors</div><div id="stat-visitors" style="font-size:1.5rem;font-weight:700;font-family:'Space Grotesk',sans-serif">—</div></div></div>
    <div class="col-6 col-md-3"><div class="pa-card"><div style="font-size:0.8125rem;color:var(--pa-text-muted)">Error rate</div><div id="stat-rate" style="font-size:1.5rem;font-weight:700;font-family:'Space Grotesk',sans-serif">—</div></div></div>
</div>

<div class="row g-3 align-items-start">
    <div class="col-lg-5">
        <div class="pa-card">
            <h6 class="mb-3" style="font-family:'Space Grotesk',sans-serif;font-weight:700">Errors over time</h6>
            <canvas id="chart"></canvas>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="pa-card p-0">
            <div id="errors-table"><div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let chart = null;

function loadData() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.errors.data") }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(function(data) {
            renderStats(data.stats);
            renderChart(data.chart);
            renderTable(data.groups);
        });
}

function renderStats(s) {
    document.getElementById('stat-total').textContent = (s.total_errors || 0).toLocaleString();
    document.getElementById('stat-unique').textContent = (s.unique_errors || 0).toLocaleString();
    document.getElementById('stat-visitors').textContent = (s.affected_visitors || 0).toLocaleString();
    document.getElementById('stat-rate').textContent = (s.error_rate || 0) + '%';
}

function renderChart(rows) {
    var days = [...new Set(rows.map(r => r.day))].sort();
    var types = [...new Set(rows.map(r => r.error_type))];
    var datasets = types.map(function(t, i) {
        var colors = ['#d04a1f', '#1fa36a', '#5865f2'];
        return {
            label: t,
            data: days.map(d => {
                var m = rows.find(r => r.day === d && r.error_type === t);
                return m ? +m.cnt : 0;
            }),
            backgroundColor: colors[i % colors.length],
            borderColor: colors[i % colors.length],
            tension: 0.25,
            fill: false,
        };
    });
    if (chart) chart.destroy();
    chart = new Chart(document.getElementById('chart'), {
        type: 'line',
        data: { labels: days, datasets: datasets },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
}

function renderTable(rows) {
    if (!rows.length) {
        document.getElementById('errors-table').innerHTML = '<div class="text-center py-5" style="color:var(--pa-text-muted)"><i class="bi bi-check-circle" style="font-size:2rem;color:var(--pa-success);opacity:.5"></i><div class="mt-2">No errors in this range. Nice.</div></div>';
        return;
    }
    var html = '<table class="pa-table"><thead><tr>';
    html += '<th>Message</th>';
    html += '<th>First seen</th>';
    html += '<th>Last seen</th>';
    html += '<th style="text-align:right">Count</th>';
    html += '<th style="text-align:right">Affected</th>';
    html += '<th></th></tr></thead><tbody>';
    rows.forEach(function(row) {
        var msg = escapeHtml((row.message || '').substring(0, 120));
        var srcLine = row.source ? ' <span style="color:var(--pa-text-muted);font-size:0.75rem">' + escapeHtml(row.source.split('/').pop()) + ':' + row.line + '</span>' : '';
        var showUrl = '{{ route("user.errors.show", "__FP__") }}'.replace('__FP__', encodeURIComponent(row.fingerprint));
        html += '<tr>';
        html += '<td><a href="' + showUrl + '" style="color:var(--pa-text);text-decoration:none;font-weight:500">' + msg + '</a>' + srcLine + '</td>';
        html += '<td style="font-size:0.8125rem;color:var(--pa-text-muted);white-space:nowrap">' + (row.first_seen || '-') + '</td>';
        html += '<td style="font-size:0.8125rem;color:var(--pa-text-muted);white-space:nowrap">' + (row.last_seen || '-') + '</td>';
        html += '<td style="text-align:right;font-weight:600">' + (+row.total || 0).toLocaleString() + '</td>';
        html += '<td style="text-align:right">' + (+row.affected_visitors || 0).toLocaleString() + '</td>';
        html += '<td><a href="' + showUrl + '" class="btn-pa-outline" style="padding:0.25rem 0.5rem;font-size:0.75rem"><i class="bi bi-arrow-right"></i></a></td>';
        html += '</tr>';
    });
    html += '</tbody></table>';
    document.getElementById('errors-table').innerHTML = html;
}

function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

document.addEventListener('DOMContentLoaded', loadData);
</script>
@endpush
