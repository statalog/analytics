@extends('layouts.app')
@section('title', 'Visitor Map')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
        <i class="bi bi-globe2 me-2" style="color:var(--pa-primary)"></i>Visitor Map
    </h4>
    @include('components.date-range-picker')
</div>

<div class="pa-card">
    <div id="visitor-map" style="height:520px;border-radius:8px;background:#e8edf2"></div>
    <div class="d-flex justify-content-between align-items-center mt-2" style="font-size:0.8rem;color:var(--pa-text-muted)">
        <span id="map-count"></span>
        <span id="map-updated"></span>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV/XN/WPcU=" crossorigin=""></script>
<script>
var map = null;
var markerGroup = null;

function initMap() {
    map = L.map('visitor-map', { zoomControl: true, scrollWheelZoom: true });
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);
    markerGroup = L.layerGroup().addTo(map);
    map.setView([20, 0], 2);
}

function renderPoints(points) {
    markerGroup.clearLayers();
    if (!points || points.length === 0) {
        document.getElementById('map-count').textContent = 'No data for selected period';
        return;
    }

    var maxHits = Math.max.apply(null, points.map(function(p) { return parseInt(p.hits) || 1; }));
    var bounds = [];

    points.forEach(function(p) {
        var lat = parseFloat(p.latitude);
        var lng = parseFloat(p.longitude);
        if (!lat && !lng) return;

        var hits = parseInt(p.hits) || 1;
        var r = Math.max(6, Math.min(28, 6 + (hits / maxHits) * 22));
        var opacity = Math.max(0.4, Math.min(0.85, 0.4 + (hits / maxHits) * 0.45));

        var circle = L.circleMarker([lat, lng], {
            radius: r,
            fillColor: '#0e7dd5',
            color: '#0a5fa3',
            weight: 1,
            opacity: 0.8,
            fillOpacity: opacity
        });

        var label = p.city ? p.city + (p.country ? ', ' + p.country : '') : (p.country || '');
        circle.bindTooltip(
            '<strong>' + (label || 'Unknown') + '</strong><br>' + hits + (hits === 1 ? ' visitor' : ' visitors'),
            { direction: 'top', offset: [0, -4] }
        );

        markerGroup.addLayer(circle);
        bounds.push([lat, lng]);
    });

    if (bounds.length > 0) {
        map.fitBounds(bounds, { padding: [40, 40], maxZoom: 8 });
    }

    var total = points.reduce(function(s, p) { return s + (parseInt(p.hits) || 0); }, 0);
    document.getElementById('map-count').textContent = total.toLocaleString() + ' visitors across ' + points.length + ' location' + (points.length === 1 ? '' : 's');
}

function loadMapData(from, to) {
    var url = '{{ route("user.visitor-map.data") }}?from=' + encodeURIComponent(from) + '&to=' + encodeURIComponent(to);
    fetch(url)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            renderPoints(data.points || []);
            document.getElementById('map-updated').textContent = 'Updated ' + new Date().toLocaleTimeString();
        });
}

document.addEventListener('DOMContentLoaded', function() {
    initMap();
});

document.addEventListener('dateRangeChanged', function(e) {
    loadMapData(e.detail.from, e.detail.to);
});
</script>
@endpush
