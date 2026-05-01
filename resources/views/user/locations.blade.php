@extends('layouts.app')
@section('title', __('analytics.page_locations'))

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0 font-heading-bold">
        <i class="bi bi-geo-alt me-2 icon-primary"></i>{{ __('analytics.page_locations') }}
    </h4>
    @include('components.date-range-picker')
</div>

<div class="pa-card mb-3">
    <div id="locations-map" style="height:480px;border-radius:8px;background:#e8edf2"></div>
    <div class="d-flex justify-content-between align-items-center mt-2 text-sm-muted">
        <span id="map-count"></span>
        <span id="map-updated"></span>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="pa-card p-0">
            <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--pa-border)">
                <h6 class="mb-0 font-heading-bold">{{ __('analytics.card_countries') }}</h6>
            </div>
            <div id="countries-table"><div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="pa-card p-0">
            <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--pa-border)">
                <h6 class="mb-0 font-heading-bold">{{ __('analytics.card_cities') }}</h6>
            </div>
            <div id="cities-table"><div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
var __t = {
    noData:     @json(__('analytics.no_data_dot')),
    noCity:     @json(__('analytics.no_city_data')),
    colLocation:@json(__('analytics.col_location')),
    colVisitors:@json(__('analytics.col_visitors')),
    colCity:    @json(__('analytics.col_city')),
    colCountry: @json(__('analytics.col_country')),
};

function loadData() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.locations.data") }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(function(data) {
            renderTable('countries-table', data.countries, 'country', 'visitors');
            renderCities(data.cities);
        });
}

function bar(pct) {
    return '<div style="background:var(--pa-input-bg);border-radius:3px;height:4px;margin-top:3px">'
         + '<div style="background:var(--pa-primary);height:100%;width:' + Math.min(100, pct) + '%;border-radius:3px"></div></div>';
}

function renderTable(id, rows, labelKey, valueKey) {
    if (!rows || !rows.length) {
        document.getElementById(id).innerHTML = '<div class="text-center py-4 text-muted">' + __t.noData + '</div>';
        return;
    }
    var max = rows.reduce(function(m, r) { return Math.max(m, +r[valueKey]); }, 1);
    var html = '<table class="pa-table"><thead><tr><th>' + __t.colLocation + '</th><th class="text-end">' + __t.colVisitors + '</th></tr></thead><tbody>';
    rows.forEach(function(r) {
        var pct = Math.round((+r[valueKey] / max) * 100);
        html += '<tr><td><div style="font-weight:500;display:flex;align-items:center">' + flag(r[labelKey]) + escHtml(r[labelKey] || '—') + '</div>' + bar(pct) + '</td>'
              + '<td class="text-num">' + (+r[valueKey]).toLocaleString() + '</td></tr>';
    });
    html += '</tbody></table>';
    document.getElementById(id).innerHTML = html;
}

function renderCities(rows) {
    if (!rows || !rows.length) {
        document.getElementById('cities-table').innerHTML = '<div class="text-center py-4 text-muted">' + __t.noCity + '</div>';
        return;
    }
    var max = rows.reduce(function(m, r) { return Math.max(m, +r.visitors); }, 1);
    var html = '<table class="pa-table"><thead><tr><th>' + __t.colCity + '</th><th>' + __t.colCountry + '</th><th class="text-end">' + __t.colVisitors + '</th></tr></thead><tbody>';
    rows.forEach(function(r) {
        var pct = Math.round((+r.visitors / max) * 100);
        html += '<tr><td><div class="fw-medium">' + escHtml(r.city) + '</div>' + bar(pct) + '</td>'
              + '<td class="text-sm"><span style="display:inline-flex;align-items:center;gap:5px">' + flag(r.country) + escHtml(r.country) + '</span></td>'
              + '<td class="text-num">' + (+r.visitors).toLocaleString() + '</td></tr>';
    });
    html += '</tbody></table>';
    document.getElementById('cities-table').innerHTML = html;
}

function flag(code) {
    if (!code || code === '—') return '';
    return '<img src="/img/flags/' + code.toLowerCase() + '.svg" width="20" height="20" style="border-radius:2px;object-fit:cover;margin-right:6px;vertical-align:middle;flex-shrink:0" onerror="this.style.display=\'none\'">';
}

function escHtml(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

function initMap() {
    if (window.__locMap) return;
    window.__locMap = L.map('locations-map', { zoomControl: true, scrollWheelZoom: true });
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd', maxZoom: 19
    }).addTo(window.__locMap);
    window.__locMapMarkers = L.layerGroup().addTo(window.__locMap);
    window.__locMap.setView([20, 0], 2);
}

function renderMapPoints(points) {
    window.__locMapMarkers.clearLayers();
    if (!points || !points.length) {
        document.getElementById('map-count').textContent = 'No data for selected period';
        return;
    }
    var maxHits = Math.max.apply(null, points.map(function(p) { return parseInt(p.hits) || 1; }));
    var bounds = [];
    points.forEach(function(p) {
        var lat = parseFloat(p.latitude), lng = parseFloat(p.longitude);
        if (!lat && !lng) return;
        var hits    = parseInt(p.hits) || 1;
        var r       = Math.max(6, Math.min(28, 6 + (hits / maxHits) * 22));
        var opacity = Math.max(0.4, Math.min(0.85, 0.4 + (hits / maxHits) * 0.45));
        var circle  = L.circleMarker([lat, lng], {
            radius: r, fillColor: paColor(), color: paColor(0.8),
            weight: 1, opacity: 0.8, fillOpacity: opacity
        });
        var label = p.city ? p.city + (p.country ? ', ' + p.country : '') : (p.country || '');
        circle.bindTooltip('<strong>' + (label || 'Unknown') + '</strong><br>' + hits + (hits === 1 ? ' visitor' : ' visitors'), { direction: 'top', offset: [0, -4] });
        window.__locMapMarkers.addLayer(circle);
        bounds.push([lat, lng]);
    });
    if (bounds.length) window.__locMap.fitBounds(bounds, { padding: [40, 40], maxZoom: 8 });
    var total = points.reduce(function(s, p) { return s + (parseInt(p.hits) || 0); }, 0);
    document.getElementById('map-count').textContent = total.toLocaleString() + ' visitors across ' + points.length + ' location' + (points.length === 1 ? '' : 's');
}

function loadMapData() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.visitor-map.data") }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(function(data) {
            renderMapPoints(data.points || []);
            document.getElementById('map-updated').textContent = 'Updated ' + new Date().toLocaleTimeString();
        });
}

document.addEventListener('DOMContentLoaded', function() {
    loadData();
    initMap();
    loadMapData();
});

document.addEventListener('dateRangeChanged', function() {
    loadData();
    loadMapData();
});
</script>
@endpush
