@extends('layouts.app')
@section('title', __('analytics.page_entry_exit'))
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0 font-heading-bold">{{ __('analytics.page_entry_exit') }}</h4>
    @include('components.date-range-picker', ['botFilter' => false])
</div>

<div class="row g-4" id="entry-exit-content">
    <div class="col-12 text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div>
</div>
@endsection

@push('scripts')
<script>
function loadData() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.entry-exit.data") }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(function(data) { render(data); });
}

function renderTable(rows, labelCol, valueCol, valueLabel) {
    var html = '<table class="pa-table"><thead><tr><th>{{ __("analytics.col_page_url") }}</th><th class="text-end">' + valueLabel + '</th></tr></thead><tbody>';
    rows.forEach(function(row) {
        html += '<tr><td style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:300px">' + (row[labelCol] || '-').replace(/^https?:\/\//, '') + '</td>';
        html += '<td class="text-end">' + (row[valueCol] || 0).toLocaleString() + '</td></tr>';
    });
    if (!rows.length) html += '<tr><td colspan="2" class="text-center text-muted">{{ __("analytics.no_data") }}</td></tr>';
    html += '</tbody></table>';
    return html;
}

function render(data) {
    document.getElementById('entry-exit-content').innerHTML =
        '<div class="col-lg-6"><div class="pa-card" style="padding:0"><div style="padding:1rem 1.25rem;border-bottom:1px solid var(--pa-border)"><h6 class="mb-0 font-heading">{{ __("analytics.tab_entry_pages") }}</h6></div>' +
        renderTable(data.entry || [], 'entry_page', 'entries', '{{ __("analytics.col_visits") }}') + '</div></div>' +
        '<div class="col-lg-6"><div class="pa-card" style="padding:0"><div style="padding:1rem 1.25rem;border-bottom:1px solid var(--pa-border)"><h6 class="mb-0 font-heading">{{ __("analytics.tab_exit_pages") }}</h6></div>' +
        renderTable(data.exit || [], 'exit_page', 'exits', '{{ __("analytics.col_exits") }}') + '</div></div>';
}

document.addEventListener('DOMContentLoaded', loadData);
</script>
@endpush
