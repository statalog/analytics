@extends('layouts.app')
@section('title', 'Error detail')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
        <a href="{{ route('user.errors') }}" style="color:var(--pa-text-muted);text-decoration:none"><i class="bi bi-arrow-left"></i></a>
        Error detail
    </h4>
    @include('components.date-range-picker')
</div>

<div id="detail"><div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div></div>
@endsection

@push('scripts')
<script>
function loadDetail() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.errors.show.data", $fingerprint) }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(render);
}

function render(data) {
    var s = data.summary;
    if (!s) {
        document.getElementById('detail').innerHTML = '<div class="pa-card text-center py-5" style="color:var(--pa-text-muted)">No occurrences of this error in the selected range.</div>';
        return;
    }

    var html = '';
    html += '<div class="pa-card mb-4">';
    html += '<div style="font-family:monospace;font-size:0.9375rem;font-weight:600;margin-bottom:0.5rem">' + escapeHtml(s.message || '') + '</div>';
    if (s.source) {
        html += '<div style="font-size:0.8125rem;color:var(--pa-text-muted);margin-bottom:1rem">' + escapeHtml(s.source) + ':' + s.line + ':' + s.col + '</div>';
    }
    html += '<div class="row g-3" style="font-size:0.875rem">';
    html += statCell('Total', (+s.total).toLocaleString());
    html += statCell('Affected visitors', (+s.affected_visitors).toLocaleString());
    html += statCell('First seen', s.first_seen);
    html += statCell('Last seen', s.last_seen);
    html += '</div>';
    html += '</div>';

    if (s.stack) {
        html += '<div class="pa-card mb-4">';
        html += '<h6 class="mb-3" style="font-family:\'Space Grotesk\',sans-serif;font-weight:700">Stack</h6>';
        html += '<pre style="background:var(--pa-input-bg);border:1px solid var(--pa-border);border-radius:var(--pa-radius);padding:0.875rem;font-size:0.8125rem;overflow-x:auto;white-space:pre-wrap;margin:0">' + escapeHtml(s.stack) + '</pre>';
        html += '</div>';
    }

    html += '<div class="row g-3 mb-4">';
    html += breakdownCard('Browser', data.by_browser);
    html += breakdownCard('OS', data.by_os);
    html += breakdownCard('Device', data.by_device);
    html += breakdownCard('Page', data.by_url);
    html += '</div>';

    html += '<div class="pa-card" style="padding:0">';
    html += '<div style="padding:1rem 1.25rem;border-bottom:1px solid var(--pa-border)"><h6 class="mb-0" style="font-family:\'Space Grotesk\',sans-serif;font-weight:700">Recent occurrences</h6></div>';
    if (!data.recent.length) {
        html += '<div class="text-center py-4" style="color:var(--pa-text-muted)">None.</div>';
    } else {
        html += '<table class="pa-table"><thead><tr><th>When</th><th>URL</th><th>Browser</th><th>OS</th><th>Country</th></tr></thead><tbody>';
        data.recent.forEach(function(r) {
            html += '<tr>';
            html += '<td style="white-space:nowrap;font-size:0.8125rem">' + escapeHtml(r.timestamp) + '</td>';
            html += '<td style="font-size:0.8125rem;word-break:break-all">' + escapeHtml(r.url || '') + '</td>';
            html += '<td style="font-size:0.8125rem">' + escapeHtml(r.browser || '') + '</td>';
            html += '<td style="font-size:0.8125rem">' + escapeHtml(r.os || '') + '</td>';
            html += '<td style="font-size:0.8125rem">' + escapeHtml(r.country || '') + '</td>';
            html += '</tr>';
        });
        html += '</tbody></table>';
    }
    html += '</div>';

    document.getElementById('detail').innerHTML = html;
}

function statCell(label, value) {
    return '<div class="col-6 col-md-3"><div style="font-size:0.75rem;color:var(--pa-text-muted)">' + label + '</div><div style="font-weight:600">' + escapeHtml(value) + '</div></div>';
}

function breakdownCard(title, rows) {
    var html = '<div class="col-md-6 col-lg-3"><div class="pa-card h-100">';
    html += '<h6 class="mb-2" style="font-family:\'Space Grotesk\',sans-serif;font-weight:700;font-size:0.8125rem;text-transform:uppercase;color:var(--pa-text-muted)">' + title + '</h6>';
    if (!rows || !rows.length) {
        html += '<div style="color:var(--pa-text-muted);font-size:0.8125rem">—</div>';
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
