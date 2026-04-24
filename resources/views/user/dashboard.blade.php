@extends('layouts.app')
@section('title', __('analytics.page_dashboard'))
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">{{ __('analytics.page_dashboard') }}</h4>
    @include('components.date-range-picker')
</div>

<div id="dashboard-app">
    <div class="row g-3 mb-4" id="stats-row">
        <div class="col-12 text-center py-4">
            <div class="spinner-border text-secondary" role="status"></div>
        </div>
    </div>

    {{-- AI Insights widget (cloud package only). --}}
    @if(Route::has('user.dashboard.ai-insight'))
        @include('cloud::partials.dashboard-ai-widget')
    @endif

    <div class="pa-card mb-4">
        <h6 class="mb-3" style="font-family:'Space Grotesk',sans-serif">{{ __('analytics.chart_traffic_overview') }}</h6>
        <div style="position:relative;height:300px">
            <canvas id="main-chart"></canvas>
            <div id="chart-no-data" style="display:none;position:absolute;inset:0;flex-direction:column;align-items:center;justify-content:center;color:var(--pa-text-muted)">
                <i class="bi bi-graph-up" style="font-size:2rem;opacity:0.4"></i>
                <div class="mt-2" style="font-size:0.875rem">{{ __('analytics.no_data_period') }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-4 col-md-6" id="card-pages"></div>
        <div class="col-lg-4 col-md-6" id="card-sources"></div>
        <div class="col-lg-4 col-md-6" id="card-locations"></div>
        <div class="col-lg-4 col-md-6" id="card-devices"></div>
        <div class="col-lg-4 col-md-6" id="card-browsers"></div>
        <div class="col-lg-4 col-md-6" id="card-os"></div>
        <div class="col-lg-4 col-md-6" id="card-resolutions"></div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css">
<script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/js/jsvectormap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/maps/world.js"></script>
<script>
function flagImg(code) {
    if (!code) return '';
    return '<img src="/img/flags/' + code.toLowerCase() + '.svg" width="20" height="20" style="border-radius:2px;object-fit:cover;flex-shrink:0" onerror="this.style.display=\'none\'">';
}

var mainChart = null;
var chartData = {};
var activeMetric = 'visitors';
var __t = {
    topPages:    @json(__('analytics.card_top_pages')),
    sources:     @json(__('analytics.card_traffic_sources')),
    locations:   @json(__('analytics.card_locations')),
    devices:     @json(__('analytics.card_devices')),
    browsers:    @json(__('analytics.card_browsers')),
    os:          @json(__('analytics.card_operating_systems')),
    resolutions: @json(__('analytics.card_screen_resolutions')),
    noData:      @json(__('analytics.no_data_available')),
    unknown:     @json(__('analytics.unknown')),
};
var __locationData = [];
var __locMap = null;

function getSourceIcon(source) {
    if (!source || source === 'Direct') return '<i class="bi bi-cursor me-1"></i>';
    var s = source.toLowerCase();
    if (s.indexOf('google') !== -1)    return '<i class="bi bi-google me-1"></i>';
    if (s.indexOf('facebook') !== -1 || s === 'fb.com') return '<i class="bi bi-facebook me-1"></i>';
    if (s.indexOf('twitter') !== -1 || s === 't.co' || s.indexOf('x.com') !== -1) return '<i class="bi bi-twitter-x me-1"></i>';
    if (s.indexOf('youtube') !== -1)   return '<i class="bi bi-youtube me-1"></i>';
    if (s.indexOf('reddit') !== -1)    return '<i class="bi bi-reddit me-1"></i>';
    if (s.indexOf('linkedin') !== -1)  return '<i class="bi bi-linkedin me-1"></i>';
    if (s.indexOf('github') !== -1)    return '<i class="bi bi-github me-1"></i>';
    if (s.indexOf('bing') !== -1)      return '<i class="bi bi-microsoft me-1"></i>';
    return '<i class="bi bi-globe2 me-1"></i>';
}

function getBrowserIcon(browser) {
    var b = (browser || '').toLowerCase();
    if (b === 'chrome')  return '<i class="bi bi-browser-chrome me-1"></i>';
    if (b === 'firefox') return '<i class="bi bi-browser-firefox me-1"></i>';
    if (b === 'safari')  return '<i class="bi bi-browser-safari me-1"></i>';
    if (b === 'edge')    return '<i class="bi bi-browser-edge me-1"></i>';
    return '<i class="bi bi-window me-1"></i>';
}

function renderLocationsCard(locationData) {
    __locationData = locationData;
    var total = 0;
    locationData.forEach(function(r) { total += parseInt(r.visitors || 0); });
    var html = '<div class="detail-card"><div class="detail-card-header"><h6>' + __t.locations + '</h6>';
    html += '<div style="display:flex;gap:4px"><button class="date-range-btn active" id="loc-btn-table" onclick="switchLocTab(\'table\')"><i class="bi bi-list-ul"></i></button>';
    html += '<button class="date-range-btn" id="loc-btn-map" onclick="switchLocTab(\'map\')"><i class="bi bi-globe2"></i></button></div></div>';
    html += '<div id="loc-tab-table" class="detail-card-body">';
    if (!locationData.length) html += '<div class="detail-row"><span class="detail-label" style="color:var(--pa-text-muted)">' + __t.noData + '</span></div>';
    locationData.forEach(function(row) {
        var val = parseInt(row.visitors || 0);
        var pct = total > 0 ? Math.round(val / total * 100) : 0;
        html += '<div class="detail-row"><span class="detail-label" style="display:inline-flex;align-items:center;gap:5px">' + flagImg(row.country) + (row.country || __t.unknown) + '</span>';
        html += '<span class="detail-value">' + val.toLocaleString() + ' (' + pct + '%)</span>';
        html += '<div class="detail-bar"><div class="detail-bar-fill" style="width:' + pct + '%"></div></div></div>';
    });
    html += '</div><div id="loc-tab-map" style="display:none;padding:8px 4px"><div id="locations-map" style="height:220px"></div></div></div>';
    document.getElementById('card-locations').innerHTML = html;
}

function switchLocTab(tab) {
    document.getElementById('loc-tab-table').style.display = tab === 'table' ? '' : 'none';
    document.getElementById('loc-tab-map').style.display   = tab === 'map'   ? '' : 'none';
    document.getElementById('loc-btn-table').classList.toggle('active', tab === 'table');
    document.getElementById('loc-btn-map').classList.toggle('active',   tab === 'map');
    if (tab === 'map') initLocationMap();
}

function initLocationMap() {
    var values = {};
    __locationData.forEach(function(r) {
        if (r.country && r.country !== 'Unknown') values[r.country.toUpperCase()] = parseInt(r.visitors || 0);
    });
    if (__locMap) { try { __locMap.destroy(); } catch(e) {} __locMap = null; }
    try {
        __locMap = new jsVectorMap({
            selector: '#locations-map', map: 'world',
            series: { regions: [{ values: values, scale: ['#93c5fd', '#0e7dd5'], normalizeFunction: 'polynomial' }] },
            regionStyle: { initial: { fill: 'var(--pa-border)', stroke: 'var(--pa-bg)', strokeWidth: 0.3 }, hover: { fill: '#0e7dd5', cursor: 'pointer' } },
            backgroundColor: 'transparent', zoomOnScroll: false,
        });
    } catch(e) {}
}

function loadDashboardData() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.dashboard.data") }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(function(data) {
            renderStats(data.stats || []);
            renderDetailCard('card-pages',    __t.topPages,   data.topPages || [],   'url',    'pageviews', function(r) { return (r.url || __t.unknown).replace(/^https?:\/\//, ''); });
            renderDetailCard('card-sources',  __t.sources,    data.sources || [],    'source', 'visits',    function(r) { return getSourceIcon(r.source) + (r.source || __t.unknown); });
            renderLocationsCard(data.locations || []);
            renderDetailCard('card-devices',  __t.devices,    data.devices || [],    'device', 'visitors');
            renderDetailCard('card-browsers', __t.browsers,   data.browsers || [],   'browser','visitors',  function(r) { return getBrowserIcon(r.browser) + (r.browser || __t.unknown); });
            renderDetailCard('card-os',       __t.os,         data.os || [],         'os',     'visitors');
            renderDetailCard('card-resolutions', __t.resolutions, data.resolutions || [], 'screen_resolution', 'cnt');
        });
}

function loadChartData() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.dashboard.chart") }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(function(data) { chartData = data; renderChart('visitors'); });
}

function renderStats(stats) {
    var html = '';
    stats.forEach(function(stat) {
        var trendClass = stat.trend >= 0 ? 'up' : 'down';
        var trendIcon  = stat.trend >= 0 ? 'arrow-up' : 'arrow-down';
        var activeClass = stat.metric === activeMetric ? ' stat-card-active' : '';
        html += '<div class="col-lg col-md-4 col-6"><div class="stat-card' + activeClass + '" role="button" onclick="switchMetric(\'' + stat.metric + '\')" style="cursor:pointer" data-metric="' + stat.metric + '">';
        html += '<div class="d-flex justify-content-between align-items-start mb-2">';
        html += '<div class="stat-icon"><i class="bi bi-' + stat.icon + '"></i></div>';
        html += '<span class="stat-trend ' + trendClass + '"><i class="bi bi-' + trendIcon + '"></i> ' + Math.abs(stat.trend) + '%</span></div>';
        html += '<div class="stat-value">' + stat.value + '</div><div class="stat-label">' + stat.label + '</div></div></div>';
    });
    document.getElementById('stats-row').innerHTML = html;
}

function renderDetailCard(id, title, rows, labelKey, valueKey, labelFn) {
    if (!document.getElementById(id)) return;
    var total = 0;
    rows.forEach(function(r) { total += parseInt(r[valueKey] || 0); });
    var html = '<div class="detail-card"><div class="detail-card-header"><h6>' + title + '</h6></div><div class="detail-card-body">';
    if (!rows.length) html += '<div class="detail-row"><span class="detail-label" style="color:var(--pa-text-muted)">' + __t.noData + '</span></div>';
    rows.forEach(function(row) {
        var val = parseInt(row[valueKey] || 0);
        var pct = total > 0 ? Math.round(val / total * 100) : 0;
        var label = labelFn ? labelFn(row) : (row[labelKey] || __t.unknown);
        html += '<div class="detail-row"><span class="detail-label">' + label + '</span>';
        html += '<span class="detail-value">' + val.toLocaleString() + ' (' + pct + '%)</span>';
        html += '<div class="detail-bar"><div class="detail-bar-fill" style="width:' + pct + '%"></div></div></div>';
    });
    html += '</div></div>';
    document.getElementById(id).innerHTML = html;
}

function formatDuration(s) {
    s = parseInt(s) || 0;
    return Math.floor(s / 60) + ':' + String(s % 60).padStart(2, '0');
}

function renderChart(metric) {
    var ctx = document.getElementById('main-chart').getContext('2d');
    var labels = chartData.map(function(d) { return d.date; });
    var values = chartData.map(function(d) { return parseFloat(d[metric] || 0); });
    var hasData = values.some(function(v) { return v > 0; });
    var noDataEl = document.getElementById('chart-no-data');
    var canvas = document.getElementById('main-chart');
    if (!hasData) {
        canvas.style.display = 'none';
        noDataEl.style.display = 'flex';
        if (mainChart) { mainChart.destroy(); mainChart = null; }
        return;
    }
    canvas.style.display = '';
    noDataEl.style.display = 'none';
    var isBounce = metric === 'bounce';
    var isDuration = metric === 'duration';
    var yTicks = { color: '#6B7290', callback: function(v) {
        if (isBounce) return Number.isInteger(v) ? v + '%' : undefined;
        if (isDuration) return Number.isInteger(v) ? formatDuration(v) : undefined;
        return Number.isInteger(v) ? v : undefined;
    }};
    if (mainChart) mainChart.destroy();
    mainChart = new Chart(ctx, {
        type: 'line',
        data: { labels: labels, datasets: [{ label: metric, data: values, borderColor: '#0e7dd5', backgroundColor: 'rgba(14,125,213,0.1)', fill: true, tension: 0.4, borderWidth: 2, pointRadius: 3, pointHoverRadius: 5 }] },
        options: { responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: function(c) {
                if (isBounce) return c.parsed.y + '%';
                if (isDuration) return formatDuration(c.parsed.y);
                return c.parsed.y;
            }}}},
            scales: {
                x: { grid: { color: 'rgba(0,0,0,0.06)' }, ticks: { color: '#6B7290' } },
                y: { grid: { color: 'rgba(0,0,0,0.06)' }, ticks: yTicks, beginAtZero: true }
            }
        }
    });
}

function switchMetric(metric) {
    activeMetric = metric;
    document.querySelectorAll('[data-metric]').forEach(function(el) {
        el.classList.toggle('stat-card-active', el.getAttribute('data-metric') === metric);
    });
    renderChart(metric);
}

document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
    loadChartData();
    if (typeof loadAiInsight === 'function') loadAiInsight();
});
</script>
@endpush
