@extends('layouts.app')
@section('title', __('analytics.page_devices'))
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0 font-heading-bold">
        <i class="bi bi-laptop me-2 icon-primary"></i>{{ __('analytics.page_devices') }}
    </h4>
    @include('components.date-range-picker')
</div>

<div id="devices-content"><div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div></div>
@endsection

@push('scripts')
<script>
var __t = {
    deviceType: @json(__('analytics.card_device_type')),
    browsers:   @json(__('analytics.card_browsers')),
    os:         @json(__('analytics.card_operating_systems')),
    resolutions:@json(__('analytics.card_screen_resolutions')),
    noData:     @json(__('analytics.no_data_dot')),
    colDevice:  @json(__('analytics.col_device')),
    colVisitors:@json(__('analytics.col_visitors')),
    colShare:   @json(__('analytics.col_share')),
    colName:    @json(__('analytics.col_name')),
};

function loadData() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.devices.data") }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(render);
}

var deviceIcons = { desktop: 'pc-display', mobile: 'phone', tablet: 'tablet', tv: 'tv', bot: 'robot' };

function render(data) {
    var html = '<div class="row g-3">';

    html += col(__t.deviceType, deviceTable(data.devices));
    html += col(__t.browsers, barTable(data.browsers, 'browser', 'visitors', browserIcon));
    html += col(__t.os, barTable(data.os, 'os', 'visitors', osIcon));
    html += col(__t.resolutions, barTable(data.resolutions, 'screen_resolution', 'cnt'));

    html += '</div>';
    document.getElementById('devices-content').innerHTML = html;
}

function col(title, inner) {
    return '<div class="col-lg-6"><div class="pa-card p-0">'
         + '<div style="padding:1rem 1.25rem;border-bottom:1px solid var(--pa-border)">'
         + '<h6 class="mb-0 font-heading-bold">' + title + '</h6>'
         + '</div>' + inner + '</div></div>';
}

function deviceTable(rows) {
    if (!rows || !rows.length) return '<div class="text-center py-4 text-muted">' + __t.noData + '</div>';
    var total = rows.reduce(function(s, r) { return s + (+r.visitors); }, 0) || 1;
    var html = '<table class="pa-table"><thead><tr><th>' + __t.colDevice + '</th><th class="text-end">' + __t.colVisitors + '</th><th class="text-end">' + __t.colShare + '</th></tr></thead><tbody>';
    rows.forEach(function(r) {
        var icon = deviceIcons[r.device] || 'display';
        var pct = Math.round((+r.visitors / total) * 100);
        html += '<tr><td><i class="bi bi-' + icon + ' me-2 icon-primary"></i>' + escHtml(r.device || '—') + '</td>'
              + '<td class="text-num">' + (+r.visitors).toLocaleString() + '</td>'
              + '<td style="text-align:right;color:var(--pa-text-muted)">' + pct + '%</td></tr>';
    });
    return html + '</tbody></table>';
}

function browserIcon(name) {
    var b = (name || '').toLowerCase();
    if (b === 'chrome')  return '<i class="bi bi-browser-chrome me-2 icon-primary"></i>';
    if (b === 'firefox') return '<i class="bi bi-browser-firefox me-2 icon-primary"></i>';
    if (b === 'safari')  return '<i class="bi bi-browser-safari me-2 icon-primary"></i>';
    if (b === 'edge')    return '<i class="bi bi-browser-edge me-2 icon-primary"></i>';
    return '<i class="bi bi-window me-2 icon-primary"></i>';
}

function osIcon(name) {
    var o = (name || '').toLowerCase();
    if (o === 'windows')                      return '<i class="bi bi-windows me-2 icon-primary"></i>';
    if (o === 'macos' || o === 'macintosh')   return '<i class="bi bi-apple me-2 icon-primary"></i>';
    if (o === 'ios' || o === 'iphone')        return '<i class="bi bi-apple me-2 icon-primary"></i>';
    if (o === 'android')                      return '<i class="bi bi-android2 me-2 icon-primary"></i>';
    if (o === 'linux')                        return '<i class="bi bi-ubuntu me-2 icon-primary"></i>';
    if (o === 'chrome os')                    return '<i class="bi bi-browser-chrome me-2 icon-primary"></i>';
    return '<i class="bi bi-question-circle me-2 icon-primary"></i>';
}

function barTable(rows, labelKey, valueKey, iconFn) {
    if (!rows || !rows.length) return '<div class="text-center py-4 text-muted">' + __t.noData + '</div>';
    var max = rows.reduce(function(m, r) { return Math.max(m, +r[valueKey]); }, 1);
    var html = '<table class="pa-table"><thead><tr><th>' + __t.colName + '</th><th class="text-end">' + __t.colVisitors + '</th></tr></thead><tbody>';
    rows.forEach(function(r) {
        var pct = Math.round((+r[valueKey] / max) * 100);
        var icon = iconFn ? iconFn(r[labelKey]) : '';
        html += '<tr><td>'
              + '<div class="fw-medium">' + icon + escHtml(r[labelKey] || '—') + '</div>'
              + '<div style="background:var(--pa-input-bg);border-radius:3px;height:4px;margin-top:3px">'
              + '<div style="background:var(--pa-primary);height:100%;width:' + pct + '%;border-radius:3px"></div></div>'
              + '</td><td class="text-num">' + (+r[valueKey]).toLocaleString() + '</td></tr>';
    });
    return html + '</tbody></table>';
}

function escHtml(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

document.addEventListener('DOMContentLoaded', loadData);
</script>
@endpush
