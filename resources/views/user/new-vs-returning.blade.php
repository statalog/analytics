@extends('layouts.app')
@section('title', __('analytics.page_new_vs_returning'))
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">{{ __('analytics.page_new_vs_returning') }}</h4>
    @include('components.date-range-picker')
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="pa-card" style="height:300px">
            <h6 class="mb-3" style="font-family:'Space Grotesk',sans-serif">Distribution</h6>
            <canvas id="nvr-chart"></canvas>
        </div>
    </div>
    <div class="col-md-8">
        <div class="pa-card" style="padding:0">
            <div id="nvr-table"><div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var nvrChart = null;

function loadData() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.new-vs-returning.data") }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(function(data) { render(data.segments || []); });
}

function render(data) {
    var newVal = parseInt((data.find(function(r) { return r.segment === 'New'; }) || {}).visitors || 0);
    var retVal = parseInt((data.find(function(r) { return r.segment === 'Returning'; }) || {}).visitors || 0);
    var total = newVal + retVal;

    var ctx = document.getElementById('nvr-chart').getContext('2d');
    if (nvrChart) nvrChart.destroy();
    nvrChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['{{ __("analytics.label_new") }}', '{{ __("analytics.label_returning") }}'],
            datasets: [{ data: [newVal, retVal], backgroundColor: ['#0e7dd5', '#38bdf8'], borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });

    var html = '<table class="pa-table"><thead><tr><th>Visitor Type</th><th style="text-align:right">{{ __("analytics.col_visitors") }}</th><th style="text-align:right">Share</th></tr></thead><tbody>';
    [['{{ __("analytics.label_new") }}', newVal], ['{{ __("analytics.label_returning") }}', retVal]].forEach(function(row) {
        var pct = total > 0 ? Math.round(row[1] / total * 100) : 0;
        html += '<tr><td>' + row[0] + '</td><td style="text-align:right">' + row[1].toLocaleString() + '</td><td style="text-align:right">' + pct + '%</td></tr>';
    });
    html += '</tbody></table>';
    document.getElementById('nvr-table').innerHTML = html;
}

document.addEventListener('DOMContentLoaded', loadData);
</script>
@endpush
