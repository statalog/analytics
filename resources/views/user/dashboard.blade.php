@extends('layouts.app')
@section('title', __('analytics.page_dashboard'))
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0 font-heading-bold">{{ __('analytics.page_dashboard') }}</h4>
    @include('components.date-range-picker')
</div>

<div id="dashboard-app">
    <div class="row g-3 mb-4" id="stats-row">
        <div class="col-12 text-center py-4">
            <div class="spinner-border text-secondary" role="status"></div>
        </div>
    </div>

    <div class="pa-card mb-4">
        <h6 class="mb-3 font-heading">{{ __('analytics.chart_traffic_overview') }}</h6>
        <div style="position:relative;height:300px">
            <canvas id="main-chart"></canvas>
            <div id="chart-no-data" style="display:none;position:absolute;inset:0;flex-direction:column;align-items:center;justify-content:center;color:var(--pa-text-muted)">
                <i class="bi bi-graph-up" style="font-size:2rem;opacity:0.4"></i>
                <div class="mt-2 text-sm">{{ __('analytics.no_data_period') }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6" id="card-pages"></div>
        <div class="col-lg-6" id="card-sources"></div>
        <div class="col-lg-6" id="card-locations"></div>
        <div class="col-lg-6" id="card-locations-map"></div>
        <div class="col-lg-6" id="card-devices"></div>
        <div class="col-lg-6" id="card-browsers"></div>
        <div class="col-lg-6" id="card-os"></div>
        <div class="col-lg-6" id="card-resolutions"></div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
var mainChart = null;
var chartData = {};
var activeMetric = 'visitors';
var __t = {
    topPages:    @json(__('analytics.card_top_pages')),
    sources:     @json(__('analytics.card_traffic_sources')),
    locations:   @json(__('analytics.card_locations')),
    locMap:      'Locations Map',
    devices:     @json(__('analytics.card_devices')),
    browsers:    @json(__('analytics.card_browsers')),
    os:          @json(__('analytics.card_operating_systems')),
    resolutions: @json(__('analytics.card_screen_resolutions')),
    noData:      @json(__('analytics.no_data_available')),
    unknown:     @json(__('analytics.unknown')),
};
var __dashMap = null;
var __dashMarkers = null;

function flagImg(code) {
    if (!code) return '';
    return '<img src="/img/flags/' + code.toLowerCase() + '.svg" width="20" height="20" style="border-radius:2px;object-fit:cover;flex-shrink:0" onerror="this.style.display=\'none\'">';
}

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

function getDeviceIcon(device) {
    var d = (device || '').toLowerCase();
    if (d === 'mobile')  return '<i class="bi bi-phone me-1"></i>';
    if (d === 'tablet')  return '<i class="bi bi-tablet me-1"></i>';
    if (d === 'desktop') return '<i class="bi bi-display me-1"></i>';
    return '<i class="bi bi-question-circle me-1"></i>';
}

function getOsIcon(os) {
    var o = (os || '').toLowerCase();
    if (o === 'windows')   return '<i class="bi bi-windows me-1"></i>';
    if (o === 'macos' || o === 'macintosh') return '<i class="bi bi-apple me-1"></i>';
    if (o === 'ios' || o === 'iphone')      return '<i class="bi bi-apple me-1"></i>';
    if (o === 'android')   return '<i class="bi bi-android2 me-1"></i>';
    if (o === 'linux')     return '<i class="bi bi-ubuntu me-1"></i>';
    if (o === 'chrome os') return '<i class="bi bi-browser-chrome me-1"></i>';
    return '<i class="bi bi-question-circle me-1"></i>';
}

function renderDetailCard(id, title, rows, labelKey, valueKey, labelFn) {
    if (!document.getElementById(id)) return;
    var total = 0;
    rows.forEach(function(r) { total += parseInt(r[valueKey] || 0); });
    var html = '<div class="detail-card"><div class="detail-card-header"><h6>' + title + '</h6></div><div class="detail-card-body">';
    if (!rows.length) html += '<div class="detail-row"><span class="detail-label text-muted">' + __t.noData + '</span></div>';
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

function renderLocationsCard(locationData) {
    var total = 0;
    locationData.forEach(function(r) { total += parseInt(r.visitors || 0); });
    var html = '<div class="detail-card"><div class="detail-card-header"><h6>' + __t.locations + '</h6></div><div class="detail-card-body">';
    if (!locationData.length) html += '<div class="detail-row"><span class="detail-label text-muted">' + __t.noData + '</span></div>';
    locationData.forEach(function(row) {
        var val = parseInt(row.visitors || 0);
        var pct = total > 0 ? Math.round(val / total * 100) : 0;
        html += '<div class="detail-row"><span class="detail-label" style="display:inline-flex;align-items:center;gap:5px">' + flagImg(row.country) + (row.country || __t.unknown) + '</span>';
        html += '<span class="detail-value">' + val.toLocaleString() + ' (' + pct + '%)</span>';
        html += '<div class="detail-bar"><div class="detail-bar-fill" style="width:' + pct + '%"></div></div></div>';
    });
    html += '</div></div>';
    document.getElementById('card-locations').innerHTML = html;
}

function renderLocationsMapCard(points) {
    var el = document.getElementById('card-locations-map');
    if (!el) return;
    el.innerHTML = '<div class="detail-card"><div class="detail-card-header"><h6>' + __t.locMap + '</h6></div>'
        + '<div style="padding:8px"><div id="dash-map" style="height:280px;border-radius:6px;background:#e8edf2"></div></div></div>';

    if (__dashMap) { try { __dashMap.remove(); } catch(e) {} __dashMap = null; }

    requestAnimationFrame(function() {
        __dashMap = L.map('dash-map', { zoomControl: true, scrollWheelZoom: false });
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd', maxZoom: 19
        }).addTo(__dashMap);
        __dashMarkers = L.layerGroup().addTo(__dashMap);
        __dashMap.setView([20, 0], 2);

        if (!points || !points.length) return;

        var maxHits = Math.max.apply(null, points.map(function(p) { return parseInt(p.hits) || 1; }));
        var bounds = [];

        points.forEach(function(p) {
            var lat = parseFloat(p.latitude);
            var lng = parseFloat(p.longitude);
            if (!lat && !lng) return;
            var hits = parseInt(p.hits) || 1;
            var r = Math.max(5, Math.min(22, 5 + (hits / maxHits) * 17));
            var opacity = Math.max(0.4, Math.min(0.85, 0.4 + (hits / maxHits) * 0.45));
            var circle = L.circleMarker([lat, lng], {
                radius: r, fillColor: paColor(), color: paColor(0.8),
                weight: 1, opacity: 0.8, fillOpacity: opacity
            });
            var label = p.city ? p.city + (p.country ? ', ' + p.country : '') : (p.country || '');
            circle.bindTooltip('<strong>' + (label || 'Unknown') + '</strong><br>' + hits + (hits === 1 ? ' visitor' : ' visitors'), { direction: 'top', offset: [0, -4] });
            __dashMarkers.addLayer(circle);
            bounds.push([lat, lng]);
        });

        if (bounds.length > 0) {
            __dashMap.fitBounds(bounds, { padding: [30, 30], maxZoom: 7 });
        }
    });
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
            renderLocationsMapCard(data.mapPoints || []);
            renderDetailCard('card-devices',  __t.devices,    data.devices || [],    'device', 'visitors',  function(r) { return getDeviceIcon(r.device) + (r.device || __t.unknown); });
            renderDetailCard('card-browsers', __t.browsers,   data.browsers || [],   'browser','visitors',  function(r) { return getBrowserIcon(r.browser) + (r.browser || __t.unknown); });
            renderDetailCard('card-os',       __t.os,         data.os || [],         'os',     'visitors',  function(r) { return getOsIcon(r.os) + (r.os || __t.unknown); });
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
        data: { labels: labels, datasets: [{ label: metric, data: values, borderColor: paColor(), backgroundColor: paColor(0.1), fill: true, tension: 0.4, borderWidth: 2, pointRadius: 3, pointHoverRadius: 5 }] },
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
