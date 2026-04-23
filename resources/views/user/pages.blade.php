@extends('layouts.app')
@section('title', 'Pages')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
        <i class="bi bi-file-earmark-text me-2" style="color:var(--pa-primary)"></i>Pages
    </h4>
    @include('components.date-range-picker')
</div>

<div class="pa-card p-0">
    <div id="pages-table"><div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div></div>
</div>
@endsection

@push('scripts')
<script>
function loadData() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.pages.data") }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(render);
}

function fmtTime(s) {
    s = parseInt(s) || 0;
    return Math.floor(s / 60) + ':' + String(s % 60).padStart(2, '0');
}

function bar(pct, color) {
    return '<div style="background:var(--pa-input-bg);border-radius:3px;height:4px;margin-top:3px;width:80px">'
         + '<div style="background:' + color + ';height:100%;width:' + Math.min(100, pct) + '%;border-radius:3px"></div></div>';
}

function render(rows) {
    if (!rows.length) {
        document.getElementById('pages-table').innerHTML = '<div class="text-center py-5" style="color:var(--pa-text-muted)">No page data in this range.</div>';
        return;
    }
    var maxPv = rows.reduce(function(m, r) { return Math.max(m, +r.pageviews); }, 1);
    var html = '<table class="pa-table"><thead><tr>'
             + '<th>Page</th>'
             + '<th style="text-align:right">Pageviews</th>'
             + '<th style="text-align:right">Visitors</th>'
             + '<th style="text-align:right">Bounce rate</th>'
             + '<th style="text-align:right">Avg time</th>'
             + '</tr></thead><tbody>';
    rows.forEach(function(r) {
        var pct = Math.round((+r.pageviews / maxPv) * 100);
        html += '<tr>'
              + '<td style="max-width:380px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="' + escHtml(r.path) + '">'
              + '<div style="font-weight:500;font-size:0.875rem">' + escHtml(r.path) + '</div>'
              + bar(pct, 'var(--pa-primary)')
              + '</td>'
              + '<td style="text-align:right;font-variant-numeric:tabular-nums">' + (+r.pageviews).toLocaleString() + '</td>'
              + '<td style="text-align:right;font-variant-numeric:tabular-nums;color:var(--pa-text-muted)">' + (+r.unique_visitors).toLocaleString() + '</td>'
              + '<td style="text-align:right;font-variant-numeric:tabular-nums;color:var(--pa-text-muted)">' + (+r.bounce_rate) + '%</td>'
              + '<td style="text-align:right;font-variant-numeric:tabular-nums;color:var(--pa-text-muted)">' + fmtTime(r.avg_time) + '</td>'
              + '</tr>';
    });
    html += '</tbody></table>';
    document.getElementById('pages-table').innerHTML = html;
}

function escHtml(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

document.addEventListener('DOMContentLoaded', loadData);
</script>
@endpush
