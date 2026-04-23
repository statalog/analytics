@extends('layouts.public')
@section('title', $site->name . ' — Analytics')
@section('content')

<div style="min-height:100vh;background:var(--pa-bg);color:var(--pa-text)">

    {{-- Topbar --}}
    <div style="background:var(--pa-card-bg);border-bottom:1px solid var(--pa-border);padding:0.875rem 1.5rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100">
        <div style="display:flex;align-items:center;gap:0.75rem">
            <i class="bi bi-graph-up-arrow" style="color:var(--pa-primary);font-size:1.1rem"></i>
            <span style="font-family:'Space Grotesk',sans-serif;font-weight:700;font-size:1rem">{{ $site->name }}</span>
            <a href="https://{{ $site->domain }}" target="_blank" rel="noopener" style="font-size:0.8125rem;color:var(--pa-text-muted);text-decoration:none">
                {{ $site->domain }} <i class="bi bi-box-arrow-up-right" style="font-size:0.7rem"></i>
            </a>
        </div>

        <div style="display:flex;align-items:center;gap:0.75rem">
            {{-- Date range picker --}}
            <div class="dropdown">
                <button class="btn-pa-outline dropdown-toggle" type="button" id="pubRangeBtn" data-bs-toggle="dropdown" aria-expanded="false" style="font-size:0.8125rem">
                    <i class="bi bi-calendar3 me-1"></i><span id="pub-range-label">Last 7 days</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end pa-dropdown" aria-labelledby="pubRangeBtn">
                    <li><a class="dropdown-item" href="#" onclick="setRange('today','Today');return false">Today</a></li>
                    <li><a class="dropdown-item" href="#" onclick="setRange('yesterday','Yesterday');return false">Yesterday</a></li>
                    <li><a class="dropdown-item" href="#" onclick="setRange('last24h','Last 24 hours');return false">Last 24 hours</a></li>
                    <li><a class="dropdown-item" href="#" onclick="setRange('last7days','Last 7 days');return false">Last 7 days</a></li>
                    <li><a class="dropdown-item" href="#" onclick="setRange('last30days','Last 30 days');return false">Last 30 days</a></li>
                    <li><a class="dropdown-item" href="#" onclick="setRange('this_month','This month');return false">This month</a></li>
                    <li><a class="dropdown-item" href="#" onclick="setRange('last_month','Last month');return false">Last month</a></li>
                </ul>
            </div>

            {{-- Theme toggle --}}
            <button id="pub-theme-btn" onclick="toggleTheme()" style="background:none;border:1px solid var(--pa-border);border-radius:var(--pa-radius);padding:0.375rem 0.625rem;color:var(--pa-text-muted);cursor:pointer">
                <i class="bi bi-moon" id="pub-theme-icon"></i>
            </button>
        </div>
    </div>

    <div style="max-width:1200px;margin:0 auto;padding:1.5rem">

        {{-- Stats row --}}
        <div class="row g-3 mb-4" id="pub-stats-row">
            @foreach(['visitors','sessions','pageviews','bounce','duration'] as $m)
            <div class="col-6 col-md-4 col-lg" id="pub-stat-{{ $m }}">
                <div class="stat-card"><div class="stat-value">-</div><div class="stat-label">-</div></div>
            </div>
            @endforeach
        </div>

        @if(in_array('chart', $sections))
        {{-- Chart --}}
        <div class="pa-card mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="mb-0" style="font-family:'Space Grotesk',sans-serif">Traffic Overview</h6>
                <div class="d-flex gap-2" id="pub-metric-btns">
                    @foreach(['visitors'=>'Visitors','sessions'=>'Sessions','pageviews'=>'Pageviews'] as $m => $l)
                    <button onclick="switchMetric('{{ $m }}')" data-metric="{{ $m }}"
                            class="btn-pa-outline" style="font-size:0.75rem;padding:0.2rem 0.6rem">{{ $l }}</button>
                    @endforeach
                </div>
            </div>
            <div style="height:250px"><canvas id="pub-chart"></canvas></div>
        </div>
        @endif

        <div class="row g-4">
            @if(in_array('pages', $sections))
            <div class="col-lg-6">
                <div class="detail-card">
                    <div class="detail-card-header"><span>Top Pages</span><span>Pageviews</span></div>
                    <div id="pub-pages"><div class="text-center py-3" style="color:var(--pa-text-muted)">Loading...</div></div>
                </div>
            </div>
            @endif

            @if(in_array('sources', $sections))
            <div class="col-lg-6">
                <div class="detail-card">
                    <div class="detail-card-header"><span>Traffic Sources</span><span>Visitors</span></div>
                    <div id="pub-sources"><div class="text-center py-3" style="color:var(--pa-text-muted)">Loading...</div></div>
                </div>
            </div>
            @endif

            @if(in_array('locations', $sections))
            <div class="col-lg-6">
                <div class="detail-card">
                    <div class="detail-card-header"><span>Locations</span><span>Visitors</span></div>
                    <div id="pub-locations"><div class="text-center py-3" style="color:var(--pa-text-muted)">Loading...</div></div>
                </div>
            </div>
            @endif

            @if(in_array('devices', $sections))
            <div class="col-lg-6">
                <div class="detail-card">
                    <div class="detail-card-header"><span>Devices</span><span>Visitors</span></div>
                    <div id="pub-devices"><div class="text-center py-3" style="color:var(--pa-text-muted)">Loading...</div></div>
                </div>
            </div>
            @endif

            @if(in_array('browsers', $sections))
            <div class="col-lg-6">
                <div class="detail-card">
                    <div class="detail-card-header"><span>Browsers</span><span>Visitors</span></div>
                    <div id="pub-browsers"><div class="text-center py-3" style="color:var(--pa-text-muted)">Loading...</div></div>
                </div>
            </div>
            @endif

            @if(in_array('os', $sections))
            <div class="col-lg-6">
                <div class="detail-card">
                    <div class="detail-card-header"><span>Operating Systems</span><span>Visitors</span></div>
                    <div id="pub-os"><div class="text-center py-3" style="color:var(--pa-text-muted)">Loading...</div></div>
                </div>
            </div>
            @endif

            @if(in_array('resolutions', $sections))
            <div class="col-lg-6">
                <div class="detail-card">
                    <div class="detail-card-header"><span>Screen Resolutions</span><span>Visitors</span></div>
                    <div id="pub-resolutions"><div class="text-center py-3" style="color:var(--pa-text-muted)">Loading...</div></div>
                </div>
            </div>
            @endif
        </div>

        <div style="text-align:center;margin-top:2rem;padding-top:1.5rem;border-top:1px solid var(--pa-border)">
            <p style="font-size:0.8125rem;color:var(--pa-text-muted);margin:0">
                Analytics by <a href="{{ url('/') }}" style="color:var(--pa-primary);text-decoration:none;font-weight:600">Statalog</a>
            </p>
        </div>

    </div>
</div>

@push('scripts')
<script>
var pubToken = '{{ $token }}';
var pubRange = 'last7days';
var pubMetric = 'visitors';
var pubChartObj = null;
var pubChartData = [];

var dataUrl  = '{{ route("public.dashboard.data",  $token) }}';
var chartUrl = '{{ route("public.dashboard.chart", $token) }}';

function setRange(range, label) {
    pubRange = range;
    document.getElementById('pub-range-label').textContent = label;
    loadAll();
}

function switchMetric(m) {
    pubMetric = m;
    document.querySelectorAll('[data-metric]').forEach(function(b) {
        b.style.background = b.dataset.metric === m ? 'var(--pa-primary)' : '';
        b.style.color      = b.dataset.metric === m ? '#fff' : '';
        b.style.borderColor = b.dataset.metric === m ? 'var(--pa-primary)' : '';
    });
    renderChart();
}

function renderChart() {
    var chartEl = document.getElementById('pub-chart');
    if (!chartEl) return;
    var ctx = chartEl.getContext('2d');
    if (pubChartObj) pubChartObj.destroy();
    var labels = pubChartData.map(function(r) { return r.date; });
    var vals   = pubChartData.map(function(r) { return parseInt(r[pubMetric] || 0); });
    pubChartObj = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: pubMetric,
                data: vals,
                borderColor: '#0e7dd5',
                backgroundColor: 'rgba(14,125,213,0.08)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointRadius: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#6B7290', maxTicksLimit: 10 } },
                y: { grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { color: '#6B7290' }, beginAtZero: true }
            }
        }
    });
}

function renderList(elId, rows, keyField, valField, total) {
    var el = document.getElementById(elId);
    if (!el) return;
    if (!rows || !rows.length) { el.innerHTML = '<div class="text-center py-3" style="color:var(--pa-text-muted);font-size:.875rem">No data</div>'; return; }
    var html = '';
    rows.slice(0, 10).forEach(function(r) {
        var pct = total > 0 ? Math.round((r[valField] / total) * 100) : 0;
        html += '<div class="detail-row">' +
            '<div class="detail-bar-wrap"><div class="detail-bar" style="width:' + pct + '%"></div>' +
            '<span class="detail-label">' + (r[keyField] || 'Unknown') + '</span></div>' +
            '<span class="detail-value">' + Number(r[valField]).toLocaleString() + '</span>' +
            '</div>';
    });
    el.innerHTML = html;
}

function loadAll() {
    var qs = '?range=' + pubRange;

    fetch(dataUrl + qs)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var stats = data.stats || [];
            stats.forEach(function(s) {
                var el = document.getElementById('pub-stat-' + s.metric);
                if (!el) return;
                var trendHtml = '';
                if (s.trend !== undefined && s.trend !== null) {
                    var up = s.trend >= 0;
                    trendHtml = '<div style="font-size:.75rem;color:' + (up ? '#22c55e' : '#ef4444') + '">' +
                        '<i class="bi bi-arrow-' + (up ? 'up' : 'down') + '"></i> ' + Math.abs(s.trend) + '%</div>';
                }
                el.innerHTML = '<div class="stat-card"><div class="stat-icon mb-1"><i class="bi bi-' + s.icon + '"></i></div>' +
                    '<div class="stat-value">' + s.value + '</div>' +
                    '<div class="stat-label">' + s.label + '</div>' + trendHtml + '</div>';
            });

            var total = (data.stats.find(function(s) { return s.metric === 'visitors'; }) || {}).raw || 0;

            renderList('pub-pages',       data.topPages    || [], 'page',       'pageviews', 0);
            renderList('pub-sources',     data.sources     || [], 'source',     'visitors',  0);
            renderList('pub-locations',   data.locations   || [], 'country',    'visitors',  0);
            renderList('pub-devices',     data.devices     || [], 'device',     'visitors',  0);
            renderList('pub-browsers',    data.browsers    || [], 'browser',    'visitors',  0);
            renderList('pub-os',          data.os          || [], 'os',         'visitors',  0);
            renderList('pub-resolutions', data.resolutions || [], 'resolution', 'visitors',  0);
        });

    fetch(chartUrl + qs)
        .then(function(r) { return r.json(); })
        .then(function(rows) {
            pubChartData = rows;
            renderChart();
        });
}

function toggleTheme() {
    var cur = document.documentElement.getAttribute('data-theme') || 'light';
    var next = cur === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('sa-theme', next);
    document.getElementById('pub-theme-icon').className = next === 'dark' ? 'bi bi-sun' : 'bi bi-moon';
}

document.addEventListener('DOMContentLoaded', function() {
    var saved = localStorage.getItem('sa-theme') || 'light';
    document.documentElement.setAttribute('data-theme', saved);
    document.getElementById('pub-theme-icon').className = saved === 'dark' ? 'bi bi-sun' : 'bi bi-moon';
    switchMetric('visitors');
    loadAll();
});
</script>
@endpush
@endsection
