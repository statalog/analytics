@extends('layouts.app')
@section('title', __('seo.page_broken_links'))
@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <h4 class="mb-0 font-heading-bold">
        <i class="bi bi-map me-2 icon-primary"></i>{{ __('seo.page_seo_tools') }}
    </h4>
</div>
@include('user.seo._nav')

<div class="pa-card mb-3">
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <div>
            <div class="fw-semibold text-sm">{{ __('seo.broken_heading') }}</div>
            <div class="text-sm-muted">{{ __('seo.broken_subheading') }}</div>
        </div>
        <button onclick="runScan()" class="btn btn-sm btn-primary ms-auto" id="btn-scan">
            <i class="bi bi-play-fill me-1"></i>{{ __('seo.broken_btn_start') }}
        </button>
    </div>
</div>

<div id="result"></div>
@endsection

@push('scripts')
<script>
function runScan() {
    document.getElementById('btn-scan').disabled = true;
    document.getElementById('btn-scan').innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>' + @json(__('seo.broken_scanning'));
    document.getElementById('result').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-secondary" role="status"></div><div class="mt-2 text-sm-muted">' + @json(__('seo.broken_loading')) + '</div></div>';

    fetch('{{ route("user.seo.broken-links.scan") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            document.getElementById('btn-scan').disabled = false;
            document.getElementById('btn-scan').innerHTML = '<i class="bi bi-play-fill me-1"></i>' + @json(__('seo.broken_btn_start'));

            if (data.error) { document.getElementById('result').innerHTML = '<div class="pa-card"><div class="alert alert-danger mb-0">' + data.error + '</div></div>'; return; }
            if (data.message) { document.getElementById('result').innerHTML = '<div class="pa-card"><div class="text-center py-3 text-muted">' + data.message + '</div></div>'; return; }

            var html = '<div class="pa-card mb-3"><div class="d-flex gap-4 flex-wrap">';
            html += '<div><div class="stat-value">' + data.scanned + '</div><div class="stat-label">' + @json(__('seo.broken_pages_scanned')) + '</div></div>';
            html += '<div><div class="stat-value" style="color:' + (data.broken > 0 ? 'var(--pa-danger,#dc3545)' : 'var(--pa-success,#22c55e)') + '">' + data.broken + '</div><div class="stat-label">' + @json(__('seo.broken_issues_found')) + '</div></div>';
            html += '</div></div>';

            if (!data.results.length) {
                var msg = @json(__('seo.broken_no_issues', ['count' => '__C__'])).replace('__C__', data.scanned);
                html += '<div class="pa-card"><div class="d-flex align-items-center gap-2" style="color:var(--pa-success,#22c55e)"><i class="bi bi-check-circle-fill fs-5"></i><span>' + msg + '</span></div></div>';
            } else {
                html += '<div class="pa-card" style="padding:0"><table class="pa-table" style="width:100%"><thead><tr><th>' + @json(__('seo.broken_col_url')) + '</th><th>' + @json(__('seo.broken_col_status')) + '</th><th>' + @json(__('seo.broken_col_type')) + '</th></tr></thead><tbody>';
                data.results.forEach(function(r) {
                    var badge = r.type === 'broken' ? 'danger' : r.type === 'error' ? 'warning' : 'secondary';
                    var label = r.status ? r.status : @json(__('seo.broken_unreachable'));
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
