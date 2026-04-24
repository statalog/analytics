@extends('layouts.app')
@section('title', 'Time of Day')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0 font-heading-bold">
        <i class="bi bi-calendar3 me-2 icon-primary"></i>Time of Day
    </h4>
    @include('components.date-range-picker')
</div>

<div class="pa-card">
    <p style="color:var(--pa-text-muted);font-size:0.875rem;margin-bottom:1.25rem">
        When your visitors are most active — by hour of day and day of week. Darker = more traffic.
    </p>
    <div id="heatmap-host"><div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div></div>
</div>
@endsection

@push('scripts')
<script>
var DAYS   = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
var HOURS  = Array.from({length: 24}, function(_, i) { return i; });

function loadData() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.time-of-day.data") }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(render);
}

function render(rows) {
    // Build lookup: grid[day][hour] = pageviews
    var grid = {};
    for (var d = 1; d <= 7; d++) { grid[d] = {}; for (var h = 0; h < 24; h++) grid[d][h] = 0; }
    var maxVal = 0;
    rows.forEach(function(r) {
        var v = +r.pageviews;
        grid[r.day_of_week][r.hour] = v;
        if (v > maxVal) maxVal = v;
    });
    if (maxVal === 0) maxVal = 1;

    var CELL = 'padding:5px 3px;text-align:center;font-size:0.7rem;border-radius:4px;min-width:28px;';

    var html = '<div style="overflow-x:auto"><table style="border-collapse:separate;border-spacing:3px;width:100%"><thead><tr>'
             + '<td style="width:36px"></td>';
    HOURS.forEach(function(h) {
        var label = h === 0 ? '12a' : h < 12 ? h + 'a' : h === 12 ? '12p' : (h-12) + 'p';
        html += '<th style="' + CELL + 'font-weight:500;color:var(--pa-text-muted)">' + label + '</th>';
    });
    html += '</tr></thead><tbody>';

    for (var d = 1; d <= 7; d++) {
        html += '<tr><td style="font-size:0.75rem;font-weight:600;color:var(--pa-text-muted);padding-right:6px;white-space:nowrap">' + DAYS[d-1] + '</td>';
        for (var h2 = 0; h2 < 24; h2++) {
            var v2 = grid[d][h2];
            var intensity = Math.pow(v2 / maxVal, 0.6);
            var alpha = intensity * 0.85;
            var bg = v2 === 0
                ? 'var(--pa-input-bg)'
                : 'rgba(208,74,31,' + alpha.toFixed(2) + ')';
            var color = intensity > 0.5 ? '#fff' : 'var(--pa-text-muted)';
            var title = DAYS[d-1] + ' ' + h2 + ':00 — ' + v2.toLocaleString() + ' pageviews';
            html += '<td style="' + CELL + 'background:' + bg + ';color:' + color + ';cursor:default" title="' + title + '">'
                  + (v2 > 0 ? v2 : '') + '</td>';
        }
        html += '</tr>';
    }

    html += '</tbody></table></div>';
    document.getElementById('heatmap-host').innerHTML = html;
}

document.addEventListener('DOMContentLoaded', loadData);
</script>
@endpush
