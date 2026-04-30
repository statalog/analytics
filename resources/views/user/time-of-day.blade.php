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

    <div class="row g-3 mt-3" id="tod-charts" style="display:none">
        <div class="col-lg-7">
            <h6 class="font-heading mb-3">{{ __('analytics.tod_chart_by_hour') }}</h6>
            <div style="position:relative;height:180px"><canvas id="tod-hour-chart"></canvas></div>
        </div>
        <div class="col-lg-5">
            <h6 class="font-heading mb-3">{{ __('analytics.tod_chart_by_day') }}</h6>
            <div style="position:relative;height:180px"><canvas id="tod-day-chart"></canvas></div>
        </div>
    </div>

    <p id="tod-summary" class="mt-3 mb-0" style="display:none"></p>
</div>
@endsection

@push('scripts')
<script>
var DAYS  = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
var HOURS = Array.from({length: 24}, function(_, i) { return i; });
var __tod = {
    peak_hour:       @json(__('analytics.tod_peak_hour')),
    even_hours:      @json(__('analytics.tod_even_hours')),
    busiest_day:     @json(__('analytics.tod_busiest_day')),
    busiest_weekend: @json(__('analytics.tod_busiest_weekend')),
    hottest_slot:    @json(__('analytics.tod_hottest_slot')),
    best_slot:       @json(__('analytics.tod_best_slot')),
    quiet_window:    @json(__('analytics.tod_quiet_window')),
    no_quiet:        @json(__('analytics.tod_no_quiet')),
};
var todHourChart = null, todDayChart = null;

function loadData() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.time-of-day.data") }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(render);
}

function render(rows) {
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
        html += '<th style="' + CELL + 'font-weight:500;color:var(--pa-text-muted)">' + String(h).padStart(2,'0') + '</th>';
    });
    html += '</tr></thead><tbody>';
    for (var d = 1; d <= 7; d++) {
        html += '<tr><td style="font-size:0.75rem;font-weight:600;color:var(--pa-text-muted);padding-right:6px;white-space:nowrap">' + DAYS[d-1] + '</td>';
        for (var h2 = 0; h2 < 24; h2++) {
            var v2 = grid[d][h2];
            var intensity = Math.pow(v2 / maxVal, 0.6);
            var alpha = intensity * 0.85;
            var bg    = v2 === 0 ? 'var(--pa-input-bg)' : 'rgba(208,74,31,' + alpha.toFixed(2) + ')';
            var color = intensity > 0.5 ? '#fff' : 'var(--pa-text-muted)';
            var title = DAYS[d-1] + ' ' + h2 + ':00 — ' + v2.toLocaleString() + ' pageviews';
            html += '<td style="' + CELL + 'background:' + bg + ';color:' + color + ';cursor:default" title="' + title + '">'
                  + (v2 > 0 ? v2 : '') + '</td>';
        }
        html += '</tr>';
    }
    html += '</tbody></table></div>';
    document.getElementById('heatmap-host').innerHTML = html;

    renderCharts(grid);
}

function renderCharts(grid) {
    var hourTotals = Array(24).fill(0);
    var dayTotals  = [0, 0, 0, 0, 0, 0, 0, 0]; // index 1–7
    for (var d = 1; d <= 7; d++) {
        for (var h = 0; h < 24; h++) {
            hourTotals[h] += grid[d][h];
            dayTotals[d]  += grid[d][h];
        }
    }
    var totalPv = hourTotals.reduce(function(a, b) { return a + b; }, 0);
    var chartsEl = document.getElementById('tod-charts');
    if (totalPv === 0) { chartsEl.style.display = 'none'; return; }
    chartsEl.style.display = '';

    var color      = 'rgba(208,74,31,0.75)';
    var colorHover = 'rgba(208,74,31,1)';
    var chartOpts  = {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { grid: { display: false } } }
    };

    if (todHourChart) todHourChart.destroy();
    todHourChart = new Chart(document.getElementById('tod-hour-chart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: Array.from({length:24}, function(_, i) { return String(i).padStart(2,'0'); }),
            datasets: [{ data: hourTotals, backgroundColor: color, hoverBackgroundColor: colorHover, borderRadius: 3 }]
        },
        options: chartOpts
    });

    if (todDayChart) todDayChart.destroy();
    todDayChart = new Chart(document.getElementById('tod-day-chart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: DAYS,
            datasets: [{ data: dayTotals.slice(1), backgroundColor: color, hoverBackgroundColor: colorHover, borderRadius: 3 }]
        },
        options: chartOpts
    });

    renderSummary(grid, hourTotals, dayTotals, totalPv);
}

function renderSummary(grid, hourTotals, dayTotals, totalPv) {
    var sentences  = [];
    var maxHourVal = Math.max.apply(null, hourTotals);
    var avgHour    = totalPv / 24;
    var variance   = hourTotals.reduce(function(s, v) { return s + Math.pow(v - avgHour, 2); }, 0) / 24;
    var cv         = Math.sqrt(variance) / (avgHour || 1);

    // 1. Peak hour vs even distribution
    if (cv > 0.4) {
        var peakHour = hourTotals.indexOf(maxHourVal);
        var pct = Math.round(maxHourVal / totalPv * 100);
        sentences.push(__tod.peak_hour
            .replace(':hour', String(peakHour).padStart(2,'0') + ':00')
            .replace(':pct', pct));
    } else {
        sentences.push(__tod.even_hours);
    }

    // 2. Busiest day + hottest slot (merged when they share the same day)
    var daySlice  = dayTotals.slice(1);
    var maxDayVal = Math.max.apply(null, daySlice);
    var peakDay   = daySlice.indexOf(maxDayVal) + 1; // 1-7
    var isWeekend = peakDay >= 6;

    var maxCell = { day: 1, hour: 0, val: 0 };
    for (var d = 1; d <= 7; d++) {
        for (var h = 0; h < 24; h++) {
            if (grid[d][h] > maxCell.val) maxCell = { day: d, hour: h, val: grid[d][h] };
        }
    }
    var slotLabel = String(maxCell.hour).padStart(2,'0') + ':00';

    if (!isWeekend && maxCell.day === peakDay) {
        sentences.push(__tod.best_slot
            .replace(':day', DAYS[peakDay - 1])
            .replace(':hour', slotLabel));
    } else {
        sentences.push(isWeekend
            ? __tod.busiest_weekend
            : __tod.busiest_day.replace(':day', DAYS[peakDay - 1]));
        sentences.push(__tod.hottest_slot
            .replace(':day', DAYS[maxCell.day - 1])
            .replace(':hour', slotLabel));
    }

    // 3. Quiet window
    var threshold = maxHourVal * 0.05;
    var longestRun = [], currentRun = [];
    for (var i = 0; i < 24; i++) {
        if (hourTotals[i] <= threshold) {
            currentRun.push(i);
        } else {
            if (currentRun.length > longestRun.length) longestRun = currentRun.slice();
            currentRun = [];
        }
    }
    if (currentRun.length > longestRun.length) longestRun = currentRun;

    if (longestRun.length >= 3) {
        sentences.push(__tod.quiet_window
            .replace(':from', String(longestRun[0]).padStart(2,'0') + ':00')
            .replace(':to',   String(longestRun[longestRun.length - 1]).padStart(2,'0') + ':00'));
    } else {
        sentences.push(__tod.no_quiet);
    }

    var el = document.getElementById('tod-summary');
    el.textContent = sentences.join(' ');
    el.style.display = '';
}

document.addEventListener('DOMContentLoaded', loadData);
</script>
@endpush
