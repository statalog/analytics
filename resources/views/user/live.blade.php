@extends('layouts.app')
@section('title', __('analytics.page_live_stats'))

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush

@section('content')
<div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">{{ __('analytics.page_live_stats') }}</h4>
    <span class="live-badge"><span class="pulse"></span> <span id="live-count">0</span> {{ __('analytics.live_visitors_online') }}</span>
    @if($site->track_subdomains)
    <select class="form-select form-select-sm" id="subdomain-filter" style="width:auto;min-width:200px" onchange="onSubdomainChange()">
        <option value="">All domains</option>
    </select>
    @endif
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6"><div id="stat-30min"></div></div>
    <div class="col-md-6"><div id="stat-60min"></div></div>
</div>

<div class="pa-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0" style="font-family:'Space Grotesk',sans-serif">{{ __('analytics.live_visitors_per_minute') }}</h6>
        <div class="d-flex align-items-center gap-2">
            <span id="updated-at" style="font-size:0.75rem;color:var(--pa-text-muted)"></span>
            <button class="date-range-btn active" id="btn-30" onclick="setChartInterval(30)">30 min</button>
            <button class="date-range-btn" id="btn-60" onclick="setChartInterval(60)">60 min</button>
        </div>
    </div>
    <div style="position:relative;height:200px"><canvas id="live-chart"></canvas></div>
</div>

<div class="pa-card">
    <ul class="nav nav-tabs mb-3" style="border-bottom:1px solid var(--pa-border)">
        <li class="nav-item">
            <a class="nav-link active" id="tab-visits-btn" href="#" onclick="switchTab('visits');return false" style="font-size:0.875rem">
                <i class="bi bi-list-ul me-1"></i>Recent Visits
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tab-map-btn" href="#" onclick="switchTab('map');return false" style="font-size:0.875rem">
                <i class="bi bi-globe2 me-1"></i>Map
            </a>
        </li>
    </ul>

    <div id="tab-visits">
        <div class="table-responsive">
            <table class="pa-table" style="width:100%">
                <thead><tr>
                    <th style="width:16px"></th>
                    <th style="width:36px"></th>
                    <th>{{ __('analytics.live_col_time') }}</th>
                    <th>{{ __('analytics.live_col_page') }}</th>
                    <th>{{ __('analytics.live_col_location') }}</th>
                    <th>{{ __('analytics.live_col_device') }}</th>
                    <th>{{ __('analytics.live_col_browser') }}</th>
                    <th>{{ __('analytics.live_col_source') }}</th>
                </tr></thead>
                <tbody id="recent-visits"></tbody>
            </table>
        </div>
    </div>

    <div id="tab-map" style="display:none">
        <div id="live-map" style="height:420px;border-radius:8px;background:#e8edf2"></div>
        <div class="d-flex justify-content-between align-items-center mt-2" style="font-size:0.8rem;color:var(--pa-text-muted)">
            <span id="live-map-count"></span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
var liveChart = null;
var chartInterval = 30;
var lastData = null;
var liveMap = null;
var liveMarkerGroup = null;
var activeTab = 'visits';
var __hideCities = @json($hideCities ?? false);
var __currentHostname = '';
var __trackSubdomains = @json($site->track_subdomains ?? false);
var __t = {
    visitors30: @json(__('analytics.live_visitors_in_30_min')),
    visitors60: @json(__('analytics.live_visitors_in_60_min')),
    visitors:   @json(__('analytics.metric_visitors')),
    direct:     @json(__('analytics.live_source_direct')),
    noRecent:   @json(__('analytics.live_no_recent_visits')),
};

function visitorAvatar(id) {
    if (!id) return '<span style="width:28px;height:28px;display:inline-block;flex-shrink:0"></span>';
    var hash = 0;
    for (var i = 0; i < Math.min(id.length, 12); i++) { hash = id.charCodeAt(i) + ((hash << 5) - hash); }
    var hue = Math.abs(hash) % 360;
    var label = id.substring(0, 4).toUpperCase();
    return '<span title="Visitor ' + label + '" style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:50%;background:hsl(' + hue + ',60%,48%);color:#fff;font-size:0.55rem;font-weight:700;flex-shrink:0;font-family:monospace;letter-spacing:0">' + label + '</span>';
}

function getLiveReferrer(referrer) {
    if (!referrer) return __t.direct;
    var domain = referrer.replace(/^https?:\/\//, '').split('/')[0].replace(/^www\./, '');
    var d = domain.toLowerCase();
    var icon = '<i class="bi bi-globe2 me-1"></i>';
    if (d.indexOf('google') !== -1)    icon = '<i class="bi bi-google me-1"></i>';
    else if (d.indexOf('facebook') !== -1 || d === 'fb.com') icon = '<i class="bi bi-facebook me-1"></i>';
    else if (d.indexOf('twitter') !== -1 || d === 't.co')    icon = '<i class="bi bi-twitter-x me-1"></i>';
    else if (d.indexOf('linkedin') !== -1) icon = '<i class="bi bi-linkedin me-1"></i>';
    else if (d.indexOf('github') !== -1)   icon = '<i class="bi bi-github me-1"></i>';
    return icon + domain;
}

function formatTimestamp(ts) {
    if (!ts) return '';
    var d = new Date(ts.replace(' ', 'T') + 'Z');
    var now = new Date();
    var diffSec = Math.floor((now - d) / 1000);
    var diffMin = Math.floor(diffSec / 60);
    if (diffSec < 60) return '<span style="font-weight:500">Just now</span>';
    if (diffMin < 60) return '<span style="font-weight:500">' + diffMin + 'm ago</span><span style="display:block;font-size:0.75rem;color:var(--pa-text-muted)">' + d.toLocaleTimeString() + '</span>';
    return d.toLocaleTimeString();
}

function renderChart(chartDataArr, minutes) {
    var now = new Date();
    var slotLabels = [], slotData = [];
    var chartMap = {};
    chartDataArr.forEach(function(d) { if (d.minute) chartMap[d.minute.substring(0,16)] = parseInt(d.visitors || 0); });
    for (var i = minutes - 1; i >= 0; i--) {
        var t = new Date(now.getTime() - i * 60000);
        var key = t.getUTCFullYear() + '-' + String(t.getUTCMonth()+1).padStart(2,'0') + '-' + String(t.getUTCDate()).padStart(2,'0') + ' ' + String(t.getUTCHours()).padStart(2,'0') + ':' + String(t.getUTCMinutes()).padStart(2,'0');
        slotLabels.push(String(t.getHours()).padStart(2,'0') + ':' + String(t.getMinutes()).padStart(2,'0'));
        slotData.push(chartMap[key] || 0);
    }
    var ctx = document.getElementById('live-chart').getContext('2d');
    if (liveChart) liveChart.destroy();
    liveChart = new Chart(ctx, {
        type: 'bar',
        data: { labels: slotLabels, datasets: [{ label: __t.visitors, data: slotData, backgroundColor: '#0e7dd5', borderRadius: 4, barPercentage: 0.6 }] },
        options: { responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#6B7290', maxTicksLimit: 10 } },
                y: { grid: { color: 'rgba(0,0,0,0.06)' }, ticks: { color: '#6B7290', callback: function(v) { return Number.isInteger(v) ? v : undefined; } }, beginAtZero: true }
            }
        }
    });
}

function setChartInterval(minutes) {
    chartInterval = minutes;
    document.getElementById('btn-30').classList.toggle('active', minutes === 30);
    document.getElementById('btn-60').classList.toggle('active', minutes === 60);
    if (lastData) renderChart(lastData.chart || [], minutes);
}

function onSubdomainChange() {
    __currentHostname = (document.getElementById('subdomain-filter') || {}).value || '';
    loadLiveData();
}

function loadLiveData() {
    var url = '{{ route("user.live.data") }}';
    if (__currentHostname) url += '?hostname=' + encodeURIComponent(__currentHostname);
    fetch(url)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            lastData = data;
            document.getElementById('live-count').textContent = data.count30 || 0;
            document.getElementById('stat-30min').innerHTML = '<div class="stat-card mt-2"><div class="stat-value">' + (data.count30 || 0) + '</div><div class="stat-label">' + __t.visitors30 + '</div></div>';
            document.getElementById('stat-60min').innerHTML = '<div class="stat-card mt-2"><div class="stat-value">' + (data.count60 || 0) + '</div><div class="stat-label">' + __t.visitors60 + '</div></div>';
            renderChart(data.chart || [], chartInterval);

            if (__trackSubdomains && data.subdomains && data.subdomains.length > 0) {
                var sel = document.getElementById('subdomain-filter');
                if (sel) {
                    var prev = sel.value;
                    sel.innerHTML = '<option value="">All domains</option>';
                    data.subdomains.forEach(function(h) {
                        var opt = document.createElement('option');
                        opt.value = h; opt.textContent = h;
                        if (h === prev) opt.selected = true;
                        sel.appendChild(opt);
                    });
                }
            }

            var now = new Date();
            document.getElementById('updated-at').textContent = 'Updated ' + now.toLocaleTimeString();

            var rows = '';
            (data.recent || []).forEach(function(v) {
                var ts = v.timestamp ? new Date(v.timestamp.replace(' ', 'T') + 'Z') : null;
                var isLive = ts && (Date.now() - ts.getTime()) < 300000;
                var deviceIcon = v.device_type === 'mobile' ? 'phone' : v.device_type === 'tablet' ? 'tablet' : 'laptop';
                rows += '<tr>';
                rows += '<td>' + (isLive ? '<span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#22C55E"></span>' : '') + '</td>';
                rows += '<td>' + visitorAvatar(v.visitor_id) + '</td>';
                rows += '<td>' + formatTimestamp(v.timestamp) + '</td>';
                rows += '<td style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:200px">' + (v.url || '').replace(/^https?:\/\//, '') + '</td>';
                rows += '<td><span style="display:inline-flex;align-items:center;gap:5px">' + (v.country ? '<img src="/img/flags/' + v.country.toLowerCase() + '.svg" width="20" height="20" style="border-radius:2px;object-fit:cover;flex-shrink:0" onerror="this.style.display=\'none\'">' : '') + (v.country || '') + (!__hideCities && v.city ? ', ' + v.city : '') + '</span></td>';
                rows += '<td><i class="bi bi-' + deviceIcon + ' me-1"></i>' + (v.device_type || 'Desktop') + '</td>';
                rows += '<td>' + (v.browser || '') + '</td>';
                rows += '<td>' + getLiveReferrer(v.referrer) + '</td>';
                rows += '</tr>';
            });
            document.getElementById('recent-visits').innerHTML = rows || '<tr><td colspan="7" class="text-center" style="color:var(--pa-text-muted)">' + __t.noRecent + '</td></tr>';
        });
}

function switchTab(tab) {
    activeTab = tab;
    document.getElementById('tab-visits').style.display = tab === 'visits' ? '' : 'none';
    document.getElementById('tab-map').style.display    = tab === 'map'    ? '' : 'none';
    document.getElementById('tab-visits-btn').classList.toggle('active', tab === 'visits');
    document.getElementById('tab-map-btn').classList.toggle('active', tab === 'map');

    if (tab === 'map' && !liveMap) {
        liveMap = L.map('live-map', { zoomControl: true, scrollWheelZoom: true });
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd', maxZoom: 19
        }).addTo(liveMap);
        liveMarkerGroup = L.layerGroup().addTo(liveMap);
        liveMap.setView([20, 0], 2);
    }
    if (tab === 'map') loadLiveMapData();
}

function renderLiveMap(points) {
    if (!liveMap) return;
    liveMarkerGroup.clearLayers();
    if (!points || points.length === 0) {
        document.getElementById('live-map-count').textContent = 'No visitors with location data in the last 30 minutes';
        return;
    }
    var maxHits = Math.max.apply(null, points.map(function(p) { return parseInt(p.hits) || 1; }));
    var bounds = [];
    points.forEach(function(p) {
        var lat = parseFloat(p.latitude), lng = parseFloat(p.longitude);
        if (!lat && !lng) return;
        var hits = parseInt(p.hits) || 1;
        var r = Math.max(6, Math.min(24, 6 + (hits / maxHits) * 18));
        var opacity = Math.max(0.4, Math.min(0.85, 0.4 + (hits / maxHits) * 0.45));
        var circle = L.circleMarker([lat, lng], {
            radius: r, fillColor: '#22C55E', color: '#16a34a', weight: 1, opacity: 0.8, fillOpacity: opacity
        });
        var label = p.city ? p.city + (p.country ? ', ' + p.country : '') : (p.country || '');
        circle.bindTooltip('<strong>' + (label || 'Unknown') + '</strong><br>' + hits + (hits === 1 ? ' visitor' : ' visitors'), { direction: 'top', offset: [0, -4] });
        liveMarkerGroup.addLayer(circle);
        bounds.push([lat, lng]);
    });
    if (bounds.length > 0) liveMap.fitBounds(bounds, { padding: [40, 40], maxZoom: 8 });
    var total = points.reduce(function(s, p) { return s + (parseInt(p.hits) || 0); }, 0);
    document.getElementById('live-map-count').textContent = total + ' visitor' + (total === 1 ? '' : 's') + ' at ' + points.length + ' location' + (points.length === 1 ? '' : 's') + ' (last 30 min)';
}

function loadLiveMapData() {
    var url = '{{ route("user.visitor-map.live") }}';
    if (__currentHostname) url += '?hostname=' + encodeURIComponent(__currentHostname);
    fetch(url)
        .then(function(r) { return r.json(); })
        .then(function(data) { renderLiveMap(data.points || []); });
}

document.addEventListener('DOMContentLoaded', function() {
    loadLiveData();
    setInterval(function() {
        loadLiveData();
        if (activeTab === 'map') loadLiveMapData();
    }, 10000);
});
</script>
@endpush
