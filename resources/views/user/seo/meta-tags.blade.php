@extends('layouts.app')
@section('title', 'Meta Tags Preview')
@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
        <i class="bi bi-map me-2" style="color:var(--pa-primary)"></i>SEO Tools
    </h4>
</div>
@include('user.seo._nav')

<div class="pa-card mb-3">
    <div class="d-flex gap-2 flex-wrap align-items-center">
        <input type="text" id="url-input" class="form-control" placeholder="https://example.com/page"
               value="https://{{ $site->domain }}" style="max-width:480px"
               onkeydown="if(event.key==='Enter')runCheck()">
        <button onclick="runCheck()" class="btn btn-sm btn-primary" id="btn-check">
            <i class="bi bi-play-fill me-1"></i>Analyse
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

    fetch('{{ route("user.seo.meta-tags.check") }}?url=' + encodeURIComponent(url))
        .then(r => r.json())
        .then(data => {
            document.getElementById('btn-check').disabled = false;
            if (data.error) { document.getElementById('result').innerHTML = '<div class="pa-card"><div class="alert alert-danger mb-0">' + data.error + '</div></div>'; return; }

            var html = '';

            // Issues
            if (data.issues && data.issues.length) {
                html += '<div class="pa-card mb-3">';
                data.issues.forEach(function(i) {
                    var cls = i.level === 'error' ? 'danger' : i.level === 'warning' ? 'warning' : 'info';
                    var icon = i.level === 'error' ? 'exclamation-octagon-fill' : i.level === 'warning' ? 'exclamation-triangle-fill' : 'info-circle-fill';
                    html += '<div class="alert alert-' + cls + ' mb-2 d-flex gap-2 align-items-start py-2"><i class="bi bi-' + icon + ' mt-1 flex-shrink-0"></i><span style="font-size:0.875rem">' + i.message + '</span></div>';
                });
                html += '</div>';
            }

            // Google SERP preview
            html += '<div class="pa-card mb-3"><h6 class="mb-3">Google Search Preview</h6>';
            html += '<div style="max-width:600px;padding:1rem;border:1px solid var(--pa-border);border-radius:8px;font-family:arial,sans-serif">';
            html += '<div style="font-size:0.8rem;color:#202124;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">' + escHtml(data.final || url) + '</div>';
            html += '<div style="font-size:1.1rem;color:#1a0dab;margin:2px 0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">' + escHtml(data.title || '(no title)') + '</div>';
            html += '<div style="font-size:0.875rem;color:#4d5156;line-height:1.4">' + escHtml(data.description || '(no description)') + '</div>';
            html += '</div></div>';

            // Social share preview
            if (data.ogTitle || data.ogDesc || data.ogImage) {
                html += '<div class="pa-card mb-3"><h6 class="mb-3">Social Share Preview</h6>';
                html += '<div style="max-width:500px;border:1px solid var(--pa-border);border-radius:8px;overflow:hidden">';
                if (data.ogImage) html += '<img src="' + escHtml(data.ogImage) + '" style="width:100%;max-height:250px;object-fit:cover" onerror="this.style.display=\'none\'">';
                html += '<div style="padding:0.75rem">';
                html += '<div style="font-size:0.75rem;color:var(--pa-text-muted);text-transform:uppercase;letter-spacing:0.05em">' + escHtml(new URL(data.final || url).hostname) + '</div>';
                html += '<div style="font-weight:600;margin:2px 0">' + escHtml(data.ogTitle || data.title || '') + '</div>';
                html += '<div style="font-size:0.875rem;color:var(--pa-text-muted)">' + escHtml(data.ogDesc || data.description || '') + '</div>';
                html += '</div></div></div>';
            }

            // Raw tags table
            html += '<div class="pa-card" style="padding:0"><table class="pa-table" style="width:100%"><thead><tr><th>Tag</th><th>Value</th><th>Length</th></tr></thead><tbody>';
            var tags = [
                ['<title>', data.title],
                ['meta description', data.description],
                ['og:title', data.ogTitle],
                ['og:description', data.ogDesc],
                ['og:image', data.ogImage],
                ['canonical', data.canonical],
                ['robots', data.robots],
                ['twitter:card', data.twitterCard],
            ];
            tags.forEach(function(t) {
                var cls = !t[1] ? ' style="color:var(--pa-text-muted)"' : '';
                html += '<tr><td style="font-family:monospace;font-size:0.8125rem;white-space:nowrap">' + t[0] + '</td>';
                html += '<td style="font-size:0.8125rem;max-width:400px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"' + cls + '>' + (t[1] ? escHtml(t[1]) : '—') + '</td>';
                html += '<td style="font-size:0.8125rem;color:var(--pa-text-muted)">' + (t[1] ? t[1].length : '—') + '</td></tr>';
            });
            html += '</tbody></table></div>';

            document.getElementById('result').innerHTML = html;
        });
}

function escHtml(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}
</script>
@endpush
