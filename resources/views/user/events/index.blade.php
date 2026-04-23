@extends('layouts.app')
@section('title', __('analytics.page_custom_events'))
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">{{ __('analytics.page_custom_events') }}</h4>
    @include('components.date-range-picker')
</div>

<div class="pa-card" style="padding:0">
    <div id="events-table"><div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div></div>
</div>
@endsection

@push('scripts')
<script>
function loadData() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.events.data") }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(function(data) { render(data); });
}

function render(rows) {
    if (!rows.length) {
        document.getElementById('events-table').innerHTML = '<div class="text-center py-5" style="color:var(--pa-text-muted)">{{ __("analytics.no_events") }}</div>';
        return;
    }
    var html = '<table class="pa-table"><thead><tr>';
    html += '<th>{{ __("analytics.col_event_name") }}</th>';
    html += '<th style="text-align:right">{{ __("analytics.col_total_occurrences") }}</th>';
    html += '<th style="text-align:right">{{ __("analytics.col_unique_visitors") }}</th>';
    html += '<th style="text-align:right">{{ __("analytics.col_first_seen") }}</th>';
    html += '<th style="text-align:right">{{ __("analytics.col_last_seen") }}</th>';
    html += '<th></th></tr></thead><tbody>';
    rows.forEach(function(row) {
        html += '<tr>';
        html += '<td><a href="{{ route("user.events.show", "__NAME__") }}" style="color:var(--pa-primary);text-decoration:none;font-weight:500">'.replace('__NAME__', encodeURIComponent(row.event_name)) + row.event_name + '</a></td>';
        html += '<td style="text-align:right">' + (row.total || 0).toLocaleString() + '</td>';
        html += '<td style="text-align:right">' + (row.unique_visitors || 0).toLocaleString() + '</td>';
        html += '<td style="text-align:right;font-size:0.8125rem;color:var(--pa-text-muted)">' + (row.first_seen || '-') + '</td>';
        html += '<td style="text-align:right;font-size:0.8125rem;color:var(--pa-text-muted)">' + (row.last_seen || '-') + '</td>';
        html += '<td><a href="{{ route("user.events.show", "__NAME__") }}" class="btn-pa-outline" style="padding:0.25rem 0.5rem;font-size:0.75rem">'.replace('__NAME__', encodeURIComponent(row.event_name)) + '<i class="bi bi-arrow-right"></i></a></td>';
        html += '</tr>';
    });
    html += '</tbody></table>';
    document.getElementById('events-table').innerHTML = html;
}

document.addEventListener('DOMContentLoaded', loadData);
</script>
@endpush
