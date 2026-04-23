@extends('layouts.app')
@section('title', 'Devices')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
        <i class="bi bi-laptop me-2" style="color:var(--pa-primary)"></i>Devices &amp; Software
    </h4>
    @include('components.date-range-picker')
</div>

<div id="devices-content"><div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div></div>
@endsection

@push('scripts')
<script>
function loadData() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.devices.data") }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(render);
}

var deviceIcons = { desktop: 'pc-display', mobile: 'phone', tablet: 'tablet', tv: 'tv', bot: 'robot' };

function render(data) {
    var html = '<div class="row g-3">';

    html += col('Device type', deviceTable(data.devices));
    html += col('Browsers', barTable(data.browsers, 'browser', 'visitors'));
    html += col('Operating systems', barTable(data.os, 'os', 'visitors'));
    html += col('Screen resolutions', barTable(data.resolutions, 'screen_resolution', 'cnt'));

    html += '</div>';
    document.getElementById('devices-content').innerHTML = html;
}

function col(title, inner) {
    return '<div class="col-lg-6"><div class="pa-card p-0">'
         + '<div style="padding:1rem 1.25rem;border-bottom:1px solid var(--pa-border)">'
         + '<h6 class="mb-0" style="font-family:\'Space Grotesk\',sans-serif;font-weight:700">' + title + '</h6>'
         + '</div>' + inner + '</div></div>';
}

function deviceTable(rows) {
    if (!rows || !rows.length) return '<div class="text-center py-4" style="color:var(--pa-text-muted)">No data.</div>';
    var total = rows.reduce(function(s, r) { return s + (+r.visitors); }, 0) || 1;
    var html = '<table class="pa-table"><thead><tr><th>Device</th><th style="text-align:right">Visitors</th><th style="text-align:right">Share</th></tr></thead><tbody>';
    rows.forEach(function(r) {
        var icon = deviceIcons[r.device] || 'display';
        var pct = Math.round((+r.visitors / total) * 100);
        html += '<tr><td><i class="bi bi-' + icon + ' me-2" style="color:var(--pa-primary)"></i>' + escHtml(r.device || '—') + '</td>'
              + '<td style="text-align:right;font-variant-numeric:tabular-nums">' + (+r.visitors).toLocaleString() + '</td>'
              + '<td style="text-align:right;color:var(--pa-text-muted)">' + pct + '%</td></tr>';
    });
    return html + '</tbody></table>';
}

function barTable(rows, labelKey, valueKey) {
    if (!rows || !rows.length) return '<div class="text-center py-4" style="color:var(--pa-text-muted)">No data.</div>';
    var max = rows.reduce(function(m, r) { return Math.max(m, +r[valueKey]); }, 1);
    var html = '<table class="pa-table"><thead><tr><th>Name</th><th style="text-align:right">Visitors</th></tr></thead><tbody>';
    rows.forEach(function(r) {
        var pct = Math.round((+r[valueKey] / max) * 100);
        html += '<tr><td>'
              + '<div style="font-weight:500">' + escHtml(r[labelKey] || '—') + '</div>'
              + '<div style="background:var(--pa-input-bg);border-radius:3px;height:4px;margin-top:3px">'
              + '<div style="background:var(--pa-primary);height:100%;width:' + pct + '%;border-radius:3px"></div></div>'
              + '</td><td style="text-align:right;font-variant-numeric:tabular-nums">' + (+r[valueKey]).toLocaleString() + '</td></tr>';
    });
    return html + '</tbody></table>';
}

function escHtml(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

document.addEventListener('DOMContentLoaded', loadData);
</script>
@endpush
