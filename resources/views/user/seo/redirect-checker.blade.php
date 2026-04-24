@extends('layouts.app')
@section('title', 'Redirect Checker')
@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <h4 class="mb-0 font-heading-bold">
        <i class="bi bi-map me-2 icon-primary"></i>SEO Tools
    </h4>
</div>
@include('user.seo._nav')

<div class="pa-card mb-3">
    <div class="d-flex gap-2 flex-wrap align-items-center">
        <input type="text" id="url-input" class="form-control" placeholder="https://example.com/old-page"
               value="https://{{ $site->domain }}" style="max-width:480px"
               onkeydown="if(event.key==='Enter')runCheck()">
        <button onclick="runCheck()" class="btn btn-sm btn-primary" id="btn-check">
            <i class="bi bi-play-fill me-1"></i>Check Redirects
        </button>
    </div>
</div>

<div id="result"></div>
@endsection

@push('scripts')
<script>
function runCheck() {
    var url = document.getElementById('url-input').value.trim();
    if (!url) return;
    document.getElementById('btn-check').disabled = true;
    document.getElementById('result').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-secondary" role="status"></div></div>';

    fetch('{{ route("user.seo.redirect-checker.check") }}?url=' + encodeURIComponent(url))
        .then(r => r.json())
        .then(data => {
            document.getElementById('btn-check').disabled = false;
            if (data.error) { document.getElementById('result').innerHTML = '<div class="pa-card"><div class="alert alert-danger mb-0">' + data.error + '</div></div>'; return; }

            var chain = data.chain || [];
            var html = '<div class="pa-card">';
            html += '<div class="mb-3"><span class="text-sm-muted">' + chain.length + ' hop' + (chain.length === 1 ? '' : 's') + '</span></div>';

            chain.forEach(function(step, i) {
                var isLast = i === chain.length - 1;
                var statusColor = step.status >= 200 && step.status < 300 ? '#22c55e' : step.status >= 300 && step.status < 400 ? '#f59e0b' : step.status >= 400 ? '#ef4444' : '#6b7280';
                var statusLabel = step.status || 'Error';

                html += '<div class="d-flex align-items-start gap-3 mb-2">';
                html += '<span style="display:inline-flex;align-items:center;justify-content:center;min-width:52px;height:28px;border-radius:6px;background:' + statusColor + '20;color:' + statusColor + ';font-size:0.75rem;font-weight:700">' + statusLabel + '</span>';
                html += '<div style="flex:1">';
                html += '<div style="font-size:0.875rem;word-break:break-all"><a class="icon-primary" href="' + step.url + '" target="_blank" rel="noopener">' + step.url + '</a></div>';
                if (step.error) html += '<div style="font-size:0.8rem;color:#ef4444">' + step.error + '</div>';
                html += '</div></div>';

                if (!isLast && step.location) {
                    html += '<div class="d-flex align-items-center gap-2 ms-4 mb-2" style="color:var(--pa-text-muted);font-size:0.8rem"><i class="bi bi-arrow-down-short"></i> Redirects to</div>';
                }
            });

            html += '</div>';
            document.getElementById('result').innerHTML = html;
        });
}
</script>
@endpush
