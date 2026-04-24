@extends('layouts.app')
@section('title', __('analytics.page_live_stats'))
@section('content')
<div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">{{ __('analytics.page_live_stats') }}</h4>
    <span class="live-badge"><span class="pulse"></span> <span id="live-count">0</span> {{ __('analytics.live_visitors_online') }}</span>
    @if($site->track_subdomains)
    <select class="form-select form-select-sm" id="subdomain-filter" style="width:auto;min-width:200px" onchange="onSubdomainChange()">
        <option value="">All domains</option>
    </select>
    @endif
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6"><div id="stat-30min"></div></div>
    <div class="col-md-6"><div id="stat-60min"></div></div>
</div>

<div class="pa-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0" style="font-family:'Space Grotesk',sans-serif">{{ __('analytics.live_visitors_per_minute') }}</h6>
        <div class="d-flex align-items-center gap-2">
            <span id="updated-at" style="font-size:0.75rem;color:var(--pa-text-muted)"></span>
            <button class="date-range-btn active" id="btn-30" onclick="setChartInterval(30)">30 min</button>
            <button class="date-range-btn" id="btn-60" onclick="setChartInterval(60)">60 min</button>
        </div>
    </div>
    <div style="position:relative;height:200px"><canvas id="live-chart"></canvas></div>
</div>

<div class="pa-card">
    <h6 style="font-family:'Space Grotesk',sans-serif" class="mb-3">{{ __('analytics.live_recent_visits') }}</h6>
    <div class="table-responsive">
        <table class="pa-table" style="width:100%">
            <thead><tr>
                <th style="width:16px"></th>
                <th>{{ __('analytics.live_col_time') }}</th>
                <th>{{ __('analytics.live_col_page') }}</th>
                <th>{{ __('analytics.live_col_location') }}</th>
                <th>{{ __('analytics.live_col_device') }}</th>
                <th>{{ __('analytics.live_col_browser') }}</th>
                <th>{{ __('analytics.live_col_source') }}</th>
            </tr></thead>
            <tbody id="recent-visits"></tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
var liveChart = null;
var chartInterval = 30;
var lastData = null;
var __hideCities = @json($hideCities ?? false);
var __currentHostname = '';
var __trackSubdomains = @json($site->track_subdomains ?? false);
var __t = {
    visitors30: @json(__('analytics.live_visitors_in_30_min')),
    visitors60: @json(__('analytics.live_visitors_in_60_min')),
    visitors:   @json(__('analytics.metric_visitors')),
    direct:     @json(__('analytics.live_source_direct')),
    noRecent:   @json(__('analytics.live_no_recent_visits')),
};

function getLiveReferrer(referrer) {
    if (!referrer) return __t.direct;
    var domain = referrer.replace(/^https?:\/\//, '').split('/')[0].replace(/^www\./, '');
    var d = domain.toLowerCase();
    var icon = '<i class="bi bi-globe2 me-1"></i>';
    if (d.indexOf('google') !== -1)    icon = '<i class="bi bi-google me-1"></i>';
    else if (d.indexOf('facebook') !== -1 || d === 'fb.com') icon = '<i class="bi bi-facebook me-1"></i>';
    else if (d.indexOf('twitter') !== -1 || d === 't.co')    icon = '<i class="bi bi-twitter-x me-1"></i>';
    else if (d.indexOf('linkedin') !== -1) icon = '<i class="bi bi-linkedin me-1"></i>';
    else if (d.indexOf('github') !== -1)   icon = '<i class="bi bi-github me-1"></i>';
    return icon + domain;
}

function formatTimestamp(ts) {
    if (!ts) return '';
    var d = new Date(ts.replace(' ', 'T') + 'Z');
    var now = new Date();
    var diffSec = Math.floor((now - d) / 1000);
    var diffMin = Math.floor(diffSec / 60);
    if (diffSec < 60) return '<span style="font-weight:500">Just now</span>';
    if (diffMin < 60) return '<span style="font-weight:500">' + diffMin + 'm ago</span><span style="display:block;font-size:0.75rem;color:var(--pa-text-muted)">' + d.toLocaleTimeString() + '</span>';
    return d.toLocaleTimeString();
}

function renderChart(chartDataArr, minutes) {
    var now = new Date();
    var slotLabels = [], slotData = [];
    var chartMap = {};
    chartDataArr.forEach(function(d) { if (d.minute) chartMap[d.minute.substring(0,16)] = parseInt(d.visitors || 0); });
    for (var i = minutes - 1; i >= 0; i--) {
        var t = new Date(now.getTime() - i * 60000);
        var key = t.getUTCFullYear() + '-' + String(t.getUTCMonth()+1).padStart(2,'0') + '-' + String(t.getUTCDate()).padStart(2,'0') + ' ' + String(t.getUTCHours()).padStart(2,'0') + ':' + String(t.getUTCMinutes()).padStart(2,'0');
        slotLabels.push(String(t.getHours()).padStart(2,'0') + ':' + String(t.getMinutes()).padStart(2,'0'));
        slotData.push(chartMap[key] || 0);
    }
    var ctx = document.getElementById('live-chart').getContext('2d');
    if (liveChart) liveChart.destroy();
    liveChart = new Chart(ctx, {
        type: 'bar',
        data: { labels: slotLabels, datasets: [{ label: __t.visitors, data: slotData, backgroundColor: '#0e7dd5', borderRadius: 4, barPercentage: 0.6 }] },
        options: { responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#6B7290', maxTicksLimit: 10 } },
                y: { grid: { color: 'rgba(0,0,0,0.06)' }, ticks: { color: '#6B7290', callback: function(v) { return Number.isInteger(v) ? v : undefined; } }, beginAtZero: true }
            }
        }
    });
}

function setChartInterval(minutes) {
    chartInterval = minutes;
    document.getElementById('btn-30').classList.toggle('active', minutes === 30);
    document.getElementById('btn-60').classList.toggle('active', minutes === 60);
    if (lastData) renderChart(lastData.chart || [], minutes);
}

function onSubdomainChange() {
    __currentHostname = (document.getElementById('subdomain-filter') || {}).value || '';
    loadLiveData();
}

function loadLiveData() {
    var url = '{{ route("user.live.data") }}';
    if (__currentHostname) url += '?hostname=' + encodeURIComponent(__currentHostname);
    fetch(url)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            lastData = data;
            document.getElementById('live-count').textContent = data.count30 || 0;
            document.getElementById('stat-30min').innerHTML = '<div class="stat-card mt-2"><div class="stat-value">' + (data.count30 || 0) + '</div><div class="stat-label">' + __t.visitors30 + '</div></div>';
            document.getElementById('stat-60min').innerHTML = '<div class="stat-card mt-2"><div class="stat-value">' + (data.count60 || 0) + '</div><div class="stat-label">' + __t.visitors60 + '</div></div>';
            renderChart(data.chart || [], chartInterval);

            if (__trackSubdomains && data.subdomains && data.subdomains.length > 0) {
                var sel = document.getElementById('subdomain-filter');
                if (sel) {
                    var prev = sel.value;
                    sel.innerHTML = '<option value="">All domains</option>';
                    data.subdomains.forEach(function(h) {
                        var opt = document.createElement('option');
                        opt.value = h; opt.textContent = h;
                        if (h === prev) opt.selected = true;
                        sel.appendChild(opt);
                    });
                }
            }

            var now = new Date();
            document.getElementById('updated-at').textContent = 'Updated ' + now.toLocaleTimeString();

            var rows = '';
            (data.recent || []).forEach(function(v) {
                var ts = v.timestamp ? new Date(v.timestamp.replace(' ', 'T') + 'Z') : null;
                var isLive = ts && (Date.now() - ts.getTime()) < 300000;
                var deviceIcon = v.device_type === 'mobile' ? 'phone' : v.device_type === 'tablet' ? 'tablet' : 'laptop';
                rows += '<tr>';
                rows += '<td>' + (isLive ? '<span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#22C55E"></span>' : '') + '</td>';
                rows += '<td>' + formatTimestamp(v.timestamp) + '</td>';
                rows += '<td style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:200px">' + (v.url || '').replace(/^https?:\/\//, '') + '</td>';
                rows += '<td><span style="display:inline-flex;align-items:center;gap:5px">' + (v.country ? '<img src="/img/flags/' + v.country.toLowerCase() + '.svg" width="20" height="20" style="border-radius:2px;object-fit:cover;flex-shrink:0" onerror="this.style.display=\'none\'">' : '') + (v.country || '') + (!__hideCities && v.city ? ', ' + v.city : '') + '</span></td>';
                rows += '<td><i class="bi bi-' + deviceIcon + ' me-1"></i>' + (v.device_type || 'Desktop') + '</td>';
                rows += '<td>' + (v.browser || '') + '</td>';
                rows += '<td>' + getLiveReferrer(v.referrer) + '</td>';
                rows += '</tr>';
            });
            document.getElementById('recent-visits').innerHTML = rows || '<tr><td colspan="7" class="text-center" style="color:var(--pa-text-muted)">' + __t.noRecent + '</td></tr>';
        });
}

document.addEventListener('DOMContentLoaded', function() { loadLiveData(); setInterval(loadLiveData, 10000); });
</script>
@endpush
