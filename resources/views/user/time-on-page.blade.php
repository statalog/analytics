@extends('layouts.app')
@section('title', __('analytics.page_time_on_page'))
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0 font-heading-bold">{{ __('analytics.page_time_on_page') }}</h4>
    @include('components.date-range-picker')
</div>

<div class="pa-card" style="padding:0">
    <div id="top-table"><div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div></div>
</div>
@endsection

@push('scripts')
<script>
function loadData() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.time-on-page.data") }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(function(data) { render(data.pages || []); });
}

function fmtDuration(s) {
    s = parseInt(s) || 0;
    return Math.floor(s / 60) + ':' + String(s % 60).padStart(2, '0');
}

function render(rows) {
    var html = '<table class="pa-table"><thead><tr>';
    html += '<th>{{ __("analytics.col_page_url") }}</th>';
    html += '<th class="text-end">{{ __("analytics.col_avg_time") }}</th>';
    html += '<th class="text-end">{{ __("analytics.col_pageviews") }}</th>';
    html += '</tr></thead><tbody>';
    rows.forEach(function(row) {
        html += '<tr><td style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:400px">' + (row.url || '-').replace(/^https?:\/\//, '') + '</td>';
        html += '<td class="text-num">' + fmtDuration(row.avg_time) + '</td>';
        html += '<td class="text-end">' + (row.pageviews || 0).toLocaleString() + '</td></tr>';
    });
    if (!rows.length) html += '<tr><td colspan="3" class="text-center text-muted">{{ __("analytics.no_data") }}</td></tr>';
    html += '</tbody></table>';
    document.getElementById('top-table').innerHTML = html;
}

document.addEventListener('DOMContentLoaded', loadData);
</script>
@endpush
