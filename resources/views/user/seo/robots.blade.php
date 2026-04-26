@extends('layouts.app')
@section('title', __('seo.page_robots'))
@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <h4 class="mb-0 font-heading-bold">
        <i class="bi bi-map me-2 icon-primary"></i>{{ __('seo.page_seo_tools') }}
    </h4>
</div>
@include('user.seo._nav')

<div class="pa-card mb-3">
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <div class="text-sm-muted">{{ __('seo.robots_checking') }}</div>
        <code style="background:var(--pa-surface);padding:0.25rem 0.75rem;border-radius:6px;font-size:0.875rem">https://{{ $site->domain }}/robots.txt</code>
        <button onclick="runCheck()" class="btn btn-sm btn-primary ms-auto" id="btn-check">
            <i class="bi bi-play-fill me-1"></i>{{ __('seo.robots_btn_fetch') }}
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

    fetch('{{ route("user.seo.robots.check") }}')
        .then(r => r.json())
        .then(data => {
            document.getElementById('btn-check').disabled = false;
            if (data.error && !data.content) { document.getElementById('result').innerHTML = '<div class="pa-card"><div class="alert alert-danger mb-0"><i class="bi bi-exclamation-circle me-2"></i>' + data.error + '</div></div>'; return; }

            var html = '';
            if (data.issues && data.issues.length) {
                html += '<div class="pa-card mb-3">';
                data.issues.forEach(function(i) {
                    var cls = i.level === 'error' ? 'danger' : i.level === 'warning' ? 'warning' : 'info';
                    var icon = i.level === 'error' ? 'exclamation-octagon-fill' : i.level === 'warning' ? 'exclamation-triangle-fill' : 'info-circle-fill';
                    html += '<div class="alert alert-' + cls + ' mb-2 d-flex gap-2 align-items-start"><i class="bi bi-' + icon + ' mt-1 flex-shrink-0"></i><span class="text-sm">' + i.message + '</span></div>';
                });
                html += '</div>';
            }

            if (data.content) {
                var meta = @json(__('seo.robots_lines_bytes', ['lines' => '__L__', 'size' => '__S__'])).replace('__L__', data.lines).replace('__S__', data.size);
                html += '<div class="pa-card"><div class="d-flex justify-content-between align-items-center mb-2"><h6 class="mb-0">' + @json(__('seo.robots_content')) + '</h6><span class="text-xs-muted">' + meta + '</span></div>';
                html += '<pre style="background:var(--pa-surface);padding:1rem;border-radius:6px;font-size:0.8125rem;overflow-x:auto;white-space:pre-wrap;margin:0">' + escHtml(data.content) + '</pre></div>';
            }

            document.getElementById('result').innerHTML = html || '<div class="pa-card"><div class="text-center py-3 text-muted">' + @json(__('seo.robots_empty')) + '</div></div>';
        });
}

function escHtml(s) {
    return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}
</script>
@endpush
