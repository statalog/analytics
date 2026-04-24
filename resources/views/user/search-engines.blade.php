@extends('layouts.app')
@section('title', 'Search Engines')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
        <i class="bi bi-search me-2" style="color:var(--pa-primary)"></i>Search Engines
    </h4>
    @include('components.date-range-picker')
</div>

<div class="pa-card" style="padding:0">
    <div id="se-container">
        <div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div>
    </div>
</div>

@if(view()->exists('cloud::gsc.keywords-card'))
    @include('cloud::gsc.keywords-card')
@endif
@endsection

@push('scripts')
<script>
var ENGINE_LOGOS = {
    'google.com':      'https://www.google.com/favicon.ico',
    'bing.com':        'https://www.bing.com/favicon.ico',
    'duckduckgo.com':  'https://duckduckgo.com/favicon.ico',
    'yahoo.com':       'https://www.yahoo.com/favicon.ico',
    'yandex.ru':       'https://yandex.ru/favicon.ico',
    'yandex.com':      'https://yandex.com/favicon.ico',
    'baidu.com':       'https://www.baidu.com/favicon.ico',
    'ecosia.org':      'https://www.ecosia.org/favicon.ico',
    'qwant.com':       'https://www.qwant.com/favicon.ico',
    'brave.com':       'https://brave.com/favicon.ico',
    'kagi.com':        'https://kagi.com/favicon.ico',
    'startpage.com':   'https://www.startpage.com/favicon.ico',
};

function engineLogo(domain) {
    var src = ENGINE_LOGOS[domain];
    if (!src) src = 'https://' + domain + '/favicon.ico';
    return '<img src="' + src + '" width="16" height="16" style="border-radius:2px;margin-right:6px;object-fit:contain" onerror="this.style.display=\'none\'">';
}

function fmtDuration(sec) {
    sec = parseInt(sec) || 0;
    if (sec <= 0) return '0s';
    var m = Math.floor(sec / 60), s = sec % 60;
    return m > 0 ? m + 'm ' + String(s).padStart(2,'0') + 's' : s + 's';
}

function renderEngines(data) {
    if (!data || data.length === 0) {
        document.getElementById('se-container').innerHTML =
            '<div class="text-center py-5" style="color:var(--pa-text-muted)">No search engine traffic for selected period</div>';
        return;
    }

    var totalVisits = data.reduce(function(s, r) { return s + r.visits; }, 0);

    var html = '<table class="pa-table" style="width:100%"><thead><tr>';
    html += '<th>Search Engine</th>';
    html += '<th style="text-align:right">Visits</th>';
    html += '<th style="text-align:right">Share</th>';
    html += '<th style="text-align:right">Pageviews</th>';
    html += '<th style="text-align:right">Pages / Visit</th>';
    html += '<th style="text-align:right">Avg Duration</th>';
    html += '<th style="text-align:right">Bounce Rate</th>';
    html += '</tr></thead><tbody>';

    data.forEach(function(row) {
        var share = totalVisits > 0 ? Math.round(row.visits / totalVisits * 100) : 0;
        html += '<tr>';
        html += '<td><span style="display:inline-flex;align-items:center">' + engineLogo(row.engine) + row.engine + '</span></td>';
        html += '<td style="text-align:right;font-weight:600">' + row.visits.toLocaleString() + '</td>';
        html += '<td style="text-align:right">';
        html += '<div style="display:flex;align-items:center;justify-content:flex-end;gap:8px">';
        html += '<div style="width:80px;height:6px;background:var(--pa-border);border-radius:3px;overflow:hidden"><div style="width:' + share + '%;height:100%;background:var(--pa-primary);border-radius:3px"></div></div>';
        html += '<span style="min-width:32px;text-align:right">' + share + '%</span>';
        html += '</div></td>';
        html += '<td style="text-align:right">' + row.pageviews.toLocaleString() + '</td>';
        html += '<td style="text-align:right">' + row.pages_per_visit + '</td>';
        html += '<td style="text-align:right">' + fmtDuration(row.avg_duration) + '</td>';
        html += '<td style="text-align:right">' + row.bounce_rate + '%</td>';
        html += '</tr>';
    });

    html += '</tbody></table>';
    document.getElementById('se-container').innerHTML = html;
}

function load() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.search-engines.data") }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(renderEngines);
}

document.addEventListener('DOMContentLoaded', load);
document.addEventListener('dateRangeChanged', load);
</script>
@endpush
