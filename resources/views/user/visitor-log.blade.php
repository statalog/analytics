@extends('layouts.app')
@section('title', 'Visitors')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0 font-heading-bold">
        <i class="bi bi-person-lines-fill me-2 icon-primary"></i>Visitors
        <span id="total-badge" style="font-size:0.875rem;font-weight:400;color:var(--pa-text-muted);margin-left:0.5rem"></span>
    </h4>
    <div class="text-sm-muted">Last 30 days</div>
</div>

<div class="pa-card p-0">
    <div id="visitor-table">
        <div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div>
    </div>
    <div id="pagination" style="display:flex;align-items:center;justify-content:space-between;padding:0.75rem 1.25rem;border-top:1px solid var(--pa-border)"></div>
</div>
@endsection

@push('scripts')
<script>
var currentPage = 1;
var totalPages  = 1;

function visitorAvatar(id) {
    if (!id) return '<span style="width:24px;height:24px;display:inline-block;flex-shrink:0"></span>';
    var h = 5381;
    for (var i = 0; i < id.length; i++) { h = (((h << 5) + h) ^ id.charCodeAt(i)) | 0; }
    var hue = (h >>> 0) % 360;
    return '<span style="display:inline-block;width:24px;height:24px;border-radius:50%;background:hsl(' + hue + ',65%,45%);flex-shrink:0"></span>';
}

function flag(code) {
    if (!code) return '';
    return '<img src="/img/flags/' + code.toLowerCase() + '.svg" width="18" height="18" style="border-radius:2px;object-fit:cover;margin-right:5px;vertical-align:middle;flex-shrink:0" onerror="this.style.display=\'none\'">';
}

function fmtDuration(s) {
    s = parseInt(s) || 0;
    if (s < 60) return s + 's';
    return Math.floor(s / 60) + 'm ' + (s % 60) + 's';
}

function fmtTime(ts) {
    if (!ts) return '—';
    var d = new Date(ts.replace(' ', 'T') + 'Z');
    var pad = function(n) { return String(n).padStart(2, '0'); };
    return d.getFullYear() + '-' + pad(d.getMonth()+1) + '-' + pad(d.getDate())
         + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes());
}

function fmtUrl(url) {
    return (url || '').replace(/^https?:\/\/[^\/]+/, '').replace(/^$/, '/');
}

function fmtRef(ref) {
    if (!ref) return '<span>—</span>';
    return '<span class="text-sm-muted">' + escHtml(ref) + '</span>';
}

function escHtml(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function(c) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
    });
}

function deviceIcon(type) {
    if (type === 'mobile') return '<i class="bi bi-phone" title="Mobile"></i>';
    if (type === 'tablet') return '<i class="bi bi-tablet" title="Tablet"></i>';
    return '<i class="bi bi-laptop" title="Desktop"></i>';
}

function loadPage(page) {
    currentPage = page;
    document.getElementById('visitor-table').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div>';

    var params = new URLSearchParams(window.location.search);
    params.set('page', page);

    fetch('{{ route("user.visitor-log.data") }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(function(data) { render(data); });
}

function render(data) {
    var rows = data.rows || [];
    totalPages = data.pages || 1;

    var total = (data.total || 0).toLocaleString();
    document.getElementById('total-badge').textContent = total + ' sessions';

    if (!rows.length) {
        document.getElementById('visitor-table').innerHTML = '<div class="text-center py-5 text-muted">No visitor sessions in the last 30 days.</div>';
        document.getElementById('pagination').innerHTML = '';
        return;
    }

    var html = '<div style="overflow-x:auto"><table class="pa-table" style="min-width:900px">';
    html += '<thead><tr>'
          + '<th style="width:36px"></th>'
          + '<th>Time</th>'
          + '<th>Location</th>'
          + '<th>Entry page</th>'
          + '<th>Device</th>'
          + '<th>Browser</th>'
          + '<th>OS</th>'
          + '<th class="text-end">Pages</th>'
          + '<th class="text-end">Duration</th>'
          + '<th>Source</th>'
          + '</tr></thead><tbody>';

    rows.forEach(function(r) {
        var location = flag(r.country) + escHtml(r.country || '—') + (r.city ? ', ' + escHtml(r.city) : '');
        var source = r.utm_campaign ? escHtml(r.utm_source || 'utm') + ' / ' + escHtml(r.utm_campaign)
                   : r.utm_source   ? escHtml(r.utm_source)
                   : fmtRef(r.referrer);

        html += '<tr>'
              + '<td style="padding-right:4px">' + visitorAvatar(r.visitor_id) + '</td>'
              + '<td style="white-space:nowrap;font-size:0.8125rem;color:var(--pa-text-muted)">' + fmtTime(r.first_seen) + '</td>'
              + '<td><span style="display:inline-flex;align-items:center">' + location + '</span></td>'
              + '<td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:0.8125rem" title="' + escHtml(r.entry_page) + '">' + escHtml(fmtUrl(r.entry_page)) + '</td>'
              + '<td>' + deviceIcon(r.device_type) + '</td>'
              + '<td class="text-sm">' + escHtml(r.browser || '—') + '</td>'
              + '<td class="text-sm">' + escHtml(r.os || '—') + '</td>'
              + '<td class="text-num">' + (r.pages || 0) + '</td>'
              + '<td style="text-align:right;font-variant-numeric:tabular-nums;white-space:nowrap">' + fmtDuration(r.duration) + '</td>'
              + '<td style="font-size:0.8125rem;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">' + source + '</td>'
              + '</tr>';
    });

    html += '</tbody></table></div>';
    document.getElementById('visitor-table').innerHTML = html;
    renderPagination(data.page, totalPages, data.total);
}

function renderPagination(page, pages, total) {
    if (pages <= 1) { document.getElementById('pagination').innerHTML = ''; return; }

    var from = ((page - 1) * 25) + 1;
    var to   = Math.min(page * 25, total);
    var info = '<span>' + from + '–' + to + ' of ' + total.toLocaleString() + '</span>';

    var btns = '<div style="display:flex;gap:4px">';
    btns += '<button onclick="loadPage(' + (page - 1) + ')" class="btn-pa-outline" style="padding:0.25rem 0.6rem;font-size:0.8125rem" ' + (page <= 1 ? 'disabled' : '') + '><i class="bi bi-chevron-left"></i></button>';

    var start = Math.max(1, page - 2);
    var end   = Math.min(pages, page + 2);
    if (start > 1) btns += '<button onclick="loadPage(1)" class="btn-pa-outline" style="padding:0.25rem 0.6rem;font-size:0.8125rem">1</button>' + (start > 2 ? '<span style="padding:0.25rem 0.3rem;font-size:0.8125rem;color:var(--pa-text-muted)">…</span>' : '');
    for (var i = start; i <= end; i++) {
        btns += '<button onclick="loadPage(' + i + ')" class="btn-pa-outline' + (i === page ? ' active' : '') + '" style="padding:0.25rem 0.6rem;font-size:0.8125rem' + (i === page ? ';background:var(--pa-primary);color:#fff;border-color:var(--pa-primary)' : '') + '">' + i + '</button>';
    }
    if (end < pages) btns += (end < pages - 1 ? '<span style="padding:0.25rem 0.3rem;font-size:0.8125rem;color:var(--pa-text-muted)">…</span>' : '') + '<button onclick="loadPage(' + pages + ')" class="btn-pa-outline" style="padding:0.25rem 0.6rem;font-size:0.8125rem">' + pages + '</button>';

    btns += '<button onclick="loadPage(' + (page + 1) + ')" class="btn-pa-outline" style="padding:0.25rem 0.6rem;font-size:0.8125rem" ' + (page >= pages ? 'disabled' : '') + '><i class="bi bi-chevron-right"></i></button>';
    btns += '</div>';

    document.getElementById('pagination').innerHTML = info + btns;
}

document.addEventListener('DOMContentLoaded', function() { loadPage(1); });
</script>
@endpush
