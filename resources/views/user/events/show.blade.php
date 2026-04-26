@extends('layouts.app')
@section('title', __('analytics.page_event_detail'))
@section('content')
<div class="d-flex align-items-center gap-3 flex-wrap mb-4">
    <a href="{{ route('user.events') }}" class="btn-pa-outline" style="padding:0.25rem 0.5rem"><i class="bi bi-arrow-left"></i></a>
    <h4 class="mb-0 font-heading-bold">{{ $name }}</h4>
    @include('components.date-range-picker')
</div>

<div class="pa-card mb-4">
    <h6 class="mb-3 font-heading">{{ __('analytics.event_occurrences_over_time') }}</h6>
    <div style="height:250px"><canvas id="event-chart"></canvas></div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="pa-card" style="padding:0">
            <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--pa-border)">
                <h6 class="mb-0 font-heading">{{ __('analytics.event_properties') }}</h6>
            </div>
            <div id="props-table"><div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="pa-card" style="padding:0">
            <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--pa-border)">
                <h6 class="mb-0 font-heading">{{ __('analytics.event_top_pages') }}</h6>
            </div>
            <div id="pages-table"><div class="text-center py-4"><div class="spinner-border spinner-border-sm text-secondary" role="status"></div></div></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var eventChart = null;

function loadData() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.events.show.data", $name) }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(function(data) { render(data); });
}

function render(data) {
    var chartRows = data.chart || [];
    var labels = chartRows.map(function(r) { return r.date; });
    var values = chartRows.map(function(r) { return parseInt(r.count || 0); });
    var ctx = document.getElementById('event-chart').getContext('2d');
    if (eventChart) eventChart.destroy();
    eventChart = new Chart(ctx, {
        type: 'bar',
        data: { labels: labels, datasets: [{ label: @json(__('analytics.event_occurrences')), data: values, backgroundColor: paColor(), borderRadius: 4, barPercentage: 0.6 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } },
            scales: { x: { grid: { display: false }, ticks: { color: '#6B7290' } }, y: { grid: { color: 'rgba(0,0,0,0.06)' }, ticks: { color: '#6B7290' }, beginAtZero: true } }
        }
    });

    var props = data.properties || [];
    var propsHtml = '';
    if (!props.length) {
        propsHtml = '<div class="text-center py-4 text-muted">{{ __("analytics.no_properties") }}</div>';
    } else {
        propsHtml = '<table class="pa-table"><thead><tr><th>{{ __("analytics.col_properties") }}</th><th class="text-end">{{ __("analytics.col_count") }}</th></tr></thead><tbody>';
        props.forEach(function(row) {
            propsHtml += '<tr><td><code class="text-sm">' + (row.properties || '') + '</code></td><td class="text-end">' + (row.cnt || 0).toLocaleString() + '</td></tr>';
        });
        propsHtml += '</tbody></table>';
    }
    document.getElementById('props-table').innerHTML = propsHtml;

    var pages = data.pages || [];
    var pagesHtml = '';
    if (!pages.length) {
        pagesHtml = '<div class="text-center py-4 text-muted">{{ __("analytics.no_data") }}</div>';
    } else {
        pagesHtml = '<table class="pa-table"><thead><tr><th>{{ __("analytics.col_page") }}</th><th class="text-end">{{ __("analytics.col_count") }}</th></tr></thead><tbody>';
        pages.forEach(function(row) {
            pagesHtml += '<tr><td style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:200px">' + (row.url || '').replace(/^https?:\/\//, '') + '</td><td class="text-end">' + (row.cnt || 0).toLocaleString() + '</td></tr>';
        });
        pagesHtml += '</tbody></table>';
    }
    document.getElementById('pages-table').innerHTML = pagesHtml;
}

document.addEventListener('DOMContentLoaded', loadData);
</script>
@endpush
