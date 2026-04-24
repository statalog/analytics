@extends('layouts.app')
@section('title', 'Sitemap Checker')
@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
        <i class="bi bi-map me-2" style="color:var(--pa-primary)"></i>SEO Tools
    </h4>
</div>
@include('user.seo._nav')

<div class="pa-card mb-3">
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <div style="font-size:0.875rem;color:var(--pa-text-muted)">Checking sitemap for:</div>
        <code style="background:var(--pa-surface);padding:0.25rem 0.75rem;border-radius:6px;font-size:0.875rem">https://{{ $site->domain }}/sitemap.xml</code>
        <button onclick="runCheck()" class="btn btn-sm btn-primary ms-auto" id="btn-check">
            <i class="bi bi-play-fill me-1"></i>Check Sitemap
        </button>
    </div>
</div>

<div id="result"></div>
@endsection

@push('scripts')
<script>
function runCheck() {
    document.getElementById('btn-check').disabled = true;
    document.getElementById('result').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-secondary" role="status"></div></div>';

    fetch('{{ route("user.seo.sitemap.check") }}')
        .then(r => r.json())
        .then(data => {
            document.getElementById('btn-check').disabled = false;
            if (data.error) { document.getElementById('result').innerHTML = '<div class="pa-card"><div class="alert alert-danger mb-0"><i class="bi bi-exclamation-circle me-2"></i>' + data.error + '</div></div>'; return; }

            var html = '<div class="pa-card mb-3"><div class="d-flex gap-4 flex-wrap">';
            html += '<div><div class="stat-value">' + (data.count || 0).toLocaleString() + '</div><div class="stat-label">' + (data.type === 'index' ? 'Child sitemaps' : 'URLs') + '</div></div>';
            html += '</div>';

            if (data.issues && data.issues.length) {
                html += '<hr style="border-color:var(--pa-border)">';
                data.issues.forEach(function(i) { html += '<div class="d-flex gap-2 align-items-start mb-2"><i class="bi bi-exclamation-triangle-fill text-warning mt-1"></i><span style="font-size:0.875rem">' + i + '</span></div>'; });
            }
            html += '</div>';

            if (data.type === 'index') {
                html += '<div class="pa-card"><h6 class="mb-3">Child Sitemaps</h6><ul class="mb-0" style="font-size:0.875rem">';
                (data.sitemaps || []).forEach(function(s) { html += '<li><a href="' + s + '" target="_blank" rel="noopener">' + s + '</a></li>'; });
                html += '</ul></div>';
            } else if (data.urls && data.urls.length) {
                html += '<div class="pa-card" style="padding:0"><table class="pa-table" style="width:100%"><thead><tr><th>URL</th><th>Last Modified</th><th>Priority</th></tr></thead><tbody>';
                data.urls.forEach(function(u) {
                    html += '<tr><td style="font-size:0.8125rem;max-width:500px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><a href="' + u.loc + '" target="_blank" rel="noopener">' + u.loc + '</a></td><td style="font-size:0.8125rem">' + (u.lastmod || '—') + '</td><td style="font-size:0.8125rem">' + (u.priority || '—') + '</td></tr>';
                });
                if (data.count > 200) html += '<tr><td colspan="3" class="text-center" style="font-size:0.8125rem;color:var(--pa-text-muted)">Showing first 200 of ' + data.count.toLocaleString() + ' URLs</td></tr>';
                html += '</tbody></table></div>';
            }

            document.getElementById('result').innerHTML = html;
        });
}
</script>
@endpush
