@extends('layouts.app')
@section('title', 'Broken Links')
@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
        <i class="bi bi-map me-2" style="color:var(--pa-primary)"></i>SEO Tools
    </h4>
</div>
@include('user.seo._nav')

<div class="pa-card mb-3">
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <div>
            <div style="font-size:0.875rem;font-weight:600">Check your top pages for broken links</div>
            <div style="font-size:0.8125rem;color:var(--pa-text-muted)">Scans the 80 most-visited pages from the last 30 days and checks each for HTTP errors.</div>
        </div>
        <button onclick="runScan()" class="btn btn-sm btn-primary ms-auto" id="btn-scan">
            <i class="bi bi-play-fill me-1"></i>Start Scan
        </button>
    </div>
</div>

<div id="result"></div>
@endsection

@push('scripts')
<script>
function runScan() {
    document.getElementById('btn-scan').disabled = true;
    document.getElementById('btn-scan').innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Scanning…';
    document.getElementById('result').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-secondary" role="status"></div><div class="mt-2" style="font-size:0.875rem;color:var(--pa-text-muted)">Checking pages… this may take up to 30 seconds.</div></div>';

    fetch('{{ route("user.seo.broken-links.scan") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            document.getElementById('btn-scan').disabled = false;
            document.getElementById('btn-scan').innerHTML = '<i class="bi bi-play-fill me-1"></i>Start Scan';

            if (data.error) { document.getElementById('result').innerHTML = '<div class="pa-card"><div class="alert alert-danger mb-0">' + data.error + '</div></div>'; return; }
            if (data.message) { document.getElementById('result').innerHTML = '<div class="pa-card"><div class="text-center py-3" style="color:var(--pa-text-muted)">' + data.message + '</div></div>'; return; }

            var html = '<div class="pa-card mb-3"><div class="d-flex gap-4 flex-wrap">';
            html += '<div><div class="stat-value">' + data.scanned + '</div><div class="stat-label">Pages scanned</div></div>';
            html += '<div><div class="stat-value" style="color:' + (data.broken > 0 ? 'var(--pa-danger,#dc3545)' : 'var(--pa-success,#22c55e)') + '">' + data.broken + '</div><div class="stat-label">Issues found</div></div>';
            html += '</div></div>';

            if (!data.results.length) {
                html += '<div class="pa-card"><div class="d-flex align-items-center gap-2" style="color:var(--pa-success,#22c55e)"><i class="bi bi-check-circle-fill fs-5"></i><span>No broken links found across ' + data.scanned + ' pages.</span></div></div>';
            } else {
                html += '<div class="pa-card" style="padding:0"><table class="pa-table" style="width:100%"><thead><tr><th>URL</th><th>Status</th><th>Type</th></tr></thead><tbody>';
                data.results.forEach(function(r) {
                    var badge = r.type === 'broken' ? 'danger' : r.type === 'error' ? 'warning' : 'secondary';
                    var label = r.status ? r.status : 'Unreachable';
                    html += '<tr><td style="font-size:0.8125rem;max-width:500px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><a href="' + r.url + '" target="_blank" rel="noopener">' + r.url + '</a></td>';
                    html += '<td><span class="badge bg-' + badge + '">' + label + '</span></td>';
                    html += '<td style="font-size:0.8125rem;text-transform:capitalize">' + r.type + '</td></tr>';
                });
                html += '</tbody></table></div>';
            }

            document.getElementById('result').innerHTML = html;
        });
}
</script>
@endpush
