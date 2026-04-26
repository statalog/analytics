@extends('layouts.app')
@section('title', __('seo.page_redirect_checker'))
@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <h4 class="mb-0 font-heading-bold">
        <i class="bi bi-map me-2 icon-primary"></i>{{ __('seo.page_seo_tools') }}
    </h4>
</div>
@include('user.seo._nav')

<div class="pa-card mb-3">
    <div class="d-flex gap-2 align-items-center">
        <div class="input-group" style="max-width:560px">
            <span class="input-group-text" style="font-size:0.8125rem;color:var(--pa-text-muted);background:var(--pa-surface-alt);border-color:var(--pa-border);white-space:nowrap">https://{{ $site->domain }}</span>
            <input type="text" id="url-input" class="form-control" placeholder="{{ __('seo.redirect_placeholder_path') }}"
                   style="border-color:var(--pa-border)"
                   onkeydown="if(event.key==='Enter')runCheck()">
        </div>
        <button onclick="runCheck()" class="btn-pa-primary flex-shrink-0" id="btn-check">
            <i class="bi bi-play-fill me-1"></i>{{ __('seo.redirect_btn_check') }}
        </button>
    </div>
    <div class="text-sm-muted mt-2">{!! __('seo.redirect_hint_path', ['domain' => '<strong>'.e($site->domain).'</strong>']) !!}</div>
</div>

<div id="result"></div>
@endsection

@push('scripts')
<script>
var SITE_BASE = 'https://{{ $site->domain }}';

function runCheck() {
    var path = document.getElementById('url-input').value.trim();
    if (!path) return;
    if (!path.startsWith('/')) path = '/' + path;
    var url = SITE_BASE + path;
    document.getElementById('btn-check').disabled = true;
    document.getElementById('result').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-secondary" role="status"></div></div>';

    fetch('{{ route("user.seo.redirect-checker.check") }}?url=' + encodeURIComponent(url))
        .then(r => r.json())
        .then(data => {
            document.getElementById('btn-check').disabled = false;
            if (data.error) { document.getElementById('result').innerHTML = '<div class="pa-card"><div class="alert alert-danger mb-0">' + data.error + '</div></div>'; return; }

            var chain = data.chain || [];
            var html = '<div class="pa-card">';
            var hopLabel = chain.length === 1 ? @json(__('seo.redirect_hops_one', ['count' => '__C__'])) : @json(__('seo.redirect_hops_many', ['count' => '__C__']));
            hopLabel = hopLabel.replace('__C__', chain.length);
            html += '<div class="mb-3"><span class="text-sm-muted">' + hopLabel + '</span></div>';

            chain.forEach(function(step, i) {
                var isLast = i === chain.length - 1;
                var statusColor = step.status >= 200 && step.status < 300 ? '#22c55e' : step.status >= 300 && step.status < 400 ? '#f59e0b' : step.status >= 400 ? '#ef4444' : '#6b7280';
                var statusLabel = step.status || @json(__('seo.redirect_error'));

                html += '<div class="d-flex align-items-start gap-3 mb-2">';
                html += '<span style="display:inline-flex;align-items:center;justify-content:center;min-width:52px;height:28px;border-radius:6px;background:' + statusColor + '20;color:' + statusColor + ';font-size:0.75rem;font-weight:700">' + statusLabel + '</span>';
                html += '<div style="flex:1">';
                html += '<div style="font-size:0.875rem;word-break:break-all"><a class="icon-primary" href="' + step.url + '" target="_blank" rel="noopener">' + step.url + '</a></div>';
                if (step.error) html += '<div style="font-size:0.8rem;color:#ef4444">' + step.error + '</div>';
                html += '</div></div>';

                if (!isLast && step.location) {
                    html += '<div class="d-flex align-items-center gap-2 ms-4 mb-2" style="color:var(--pa-text-muted);font-size:0.8rem"><i class="bi bi-arrow-down-short"></i> ' + @json(__('seo.redirect_redirects_to')) + '</div>';
                }
            });

            html += '</div>';
            document.getElementById('result').innerHTML = html;
        });
}
</script>
@endpush
