@extends('layouts.app')
@section('title', __('analytics.page_error_detail'))
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0 font-heading-bold">
        <a href="{{ route('user.errors') }}" style="color:var(--pa-text-muted);text-decoration:none"><i class="bi bi-arrow-left"></i></a>
        {{ __('analytics.page_error_detail') }}
    </h4>
    @include('components.date-range-picker', ['botFilter' => false])
</div>

<div id="detail"><div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div></div>
@endsection

@push('scripts')
<script>
var __t = {
    noOccurrences: @json(__('analytics.no_occurrences')),
    total:         @json(__('analytics.errors_total_short')),
    affected:      @json(__('analytics.errors_affected')),
    firstSeen:     @json(__('analytics.col_first_seen_short')),
    lastSeen:      @json(__('analytics.col_last_seen_short')),
    stack:         @json(__('analytics.errors_stack')),
    browser:       @json(__('analytics.col_browser')),
    os:            @json(__('analytics.col_os')),
    device:        @json(__('analytics.col_device')),
    page:          @json(__('analytics.col_page')),
    recent:        @json(__('analytics.errors_recent')),
    none:          @json(__('analytics.none_dot')),
    when:          @json(__('analytics.col_when')),
    url:           @json(__('analytics.col_url')),
    country:       @json(__('analytics.col_country')),
};

function loadDetail() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.errors.show.data", $fingerprint) }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(render);
}

function render(data) {
    var s = data.summary;
    if (!s) {
        document.getElementById('detail').innerHTML = '<div class="pa-card text-center py-5 text-muted">' + __t.noOccurrences + '</div>';
        return;
    }

    var html = '';
    html += '<div class="pa-card mb-4">';
    html += '<div style="font-family:monospace;font-size:0.9375rem;font-weight:600;margin-bottom:0.5rem">' + escapeHtml(s.message || '') + '</div>';
    if (s.source) {
        html += '<div class="text-sm-muted mb-3">' + escapeHtml(s.source) + ':' + s.line + ':' + s.col + '</div>';
    }
    html += '<div class="row g-3 text-sm">';
    html += statCell(__t.total, (+s.total).toLocaleString());
    html += statCell(__t.affected, (+s.affected_visitors).toLocaleString());
    html += statCell(__t.firstSeen, s.first_seen);
    html += statCell(__t.lastSeen, s.last_seen);
    html += '</div>';
    html += '</div>';

    if (s.stack) {
        html += '<div class="pa-card mb-4">';
        html += '<h6 class="mb-3 font-heading-bold">' + __t.stack + '</h6>';
        html += '<pre style="background:var(--pa-input-bg);border:1px solid var(--pa-border);border-radius:var(--pa-radius);padding:0.875rem;font-size:0.8125rem;overflow-x:auto;white-space:pre-wrap;margin:0">' + escapeHtml(s.stack) + '</pre>';
        html += '</div>';
    }

    html += '<div class="row g-3 mb-4">';
    html += breakdownCard(__t.browser, data.by_browser);
    html += breakdownCard(__t.os, data.by_os);
    html += breakdownCard(__t.device, data.by_device);
    html += breakdownCard(__t.page, data.by_url);
    html += '</div>';

    html += '<div class="pa-card" style="padding:0">';
    html += '<div style="padding:1rem 1.25rem;border-bottom:1px solid var(--pa-border)"><h6 class="mb-0 font-heading-bold">' + __t.recent + '</h6></div>';
    if (!data.recent.length) {
        html += '<div class="text-center py-4 text-muted">' + __t.none + '</div>';
    } else {
        html += '<table class="pa-table"><thead><tr><th>' + __t.when + '</th><th>' + __t.url + '</th><th>' + __t.browser + '</th><th>' + __t.os + '</th><th>' + __t.country + '</th></tr></thead><tbody>';
        data.recent.forEach(function(r) {
            html += '<tr>';
            html += '<td style="white-space:nowrap;font-size:0.8125rem">' + escapeHtml(r.timestamp) + '</td>';
            html += '<td style="font-size:0.8125rem;word-break:break-all">' + escapeHtml(r.url || '') + '</td>';
            html += '<td class="text-sm">' + escapeHtml(r.browser || '') + '</td>';
            html += '<td class="text-sm">' + escapeHtml(r.os || '') + '</td>';
            html += '<td class="text-sm">' + escapeHtml(r.country || '') + '</td>';
            html += '</tr>';
        });
        html += '</tbody></table>';
    }
    html += '</div>';

    document.getElementById('detail').innerHTML = html;
}

function statCell(label, value) {
    return '<div class="col-6 col-md-3"><div class="text-xs-muted">' + label + '</div><div class="fw-semibold">' + escapeHtml(value) + '</div></div>';
}

function breakdownCard(title, rows) {
    var html = '<div class="col-md-6 col-lg-3"><div class="pa-card h-100">';
    html += '<h6 class="mb-2" style="font-family:\'Space Grotesk\',sans-serif;font-weight:700;font-size:0.8125rem;text-transform:uppercase;color:var(--pa-text-muted)">' + title + '</h6>';
    if (!rows || !rows.length) {
        html += '<div class="text-sm-muted">—</div>';
    } else {
        var total = rows.reduce((a, r) => a + (+r.cnt), 0);
        rows.slice(0, 5).forEach(function(r) {
            var pct = total > 0 ? Math.round((+r.cnt / total) * 100) : 0;
            html += '<div style="display:flex;justify-content:space-between;align-items:center;gap:0.5rem;margin-bottom:0.375rem;font-size:0.8125rem">';
            html += '<span style="flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + escapeHtml(r.name || '—') + '</span>';
            html += '<span style="color:var(--pa-text-muted);font-variant-numeric:tabular-nums">' + pct + '%</span>';
            html += '</div>';
        });
    }
    html += '</div></div>';
    return html;
}

function escapeHtml(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

document.addEventListener('DOMContentLoaded', loadDetail);
</script>
@endpush
