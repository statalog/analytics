@extends('layouts.app')
@section('title', __('analytics.page_bots'))
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0 font-heading-bold">
        <i class="bi bi-robot me-2 icon-primary"></i>{{ __('analytics.page_bots') }}
    </h4>
    @include('components.date-range-picker', ['botFilter' => false])
</div>

<p style="color:var(--pa-text-muted);margin-bottom:1.5rem">
    {{ __('analytics.bots_intro') }}
</p>

<div id="bots-content"><div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div></div>
@endsection

@push('scripts')
<script>
let chart = null;
var __t = {
    trackingOff:      @json(__('analytics.bot_tracking_off')),
    trackingOffDesc:  @json(__('analytics.bot_tracking_off_desc_html')),
    websiteSettings:  @json(__('analytics.website_settings')),
    botHits:          @json(__('analytics.bot_hits')),
    humanHits:        @json(__('analytics.human_hits')),
    botShare:         @json(__('analytics.bot_share')),
    topBot:           @json(__('analytics.top_bot')),
    activityOverTime: @json(__('analytics.bot_activity_over_time')),
    byBot:            @json(__('analytics.by_bot')),
    topPagesByBots:   @json(__('analytics.top_pages_by_bots')),
    noBotTraffic:     @json(__('analytics.no_bot_traffic')),
    noBotPageviews:   @json(__('analytics.no_bot_pageviews')),
    colBot:           @json(__('analytics.col_bot')),
    colHits:          @json(__('analytics.col_hits')),
    colUnique:        @json(__('analytics.col_unique')),
    colPage:          @json(__('analytics.col_page')),
    colDistinctBots:  @json(__('analytics.col_distinct_bots')),
    settingsUrl:      @json(route('user.sites.show', $site)),
};

function loadData() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.bots.data") }}?' + params.toString())
        .then(r => r.json())
        .then(render);
}

function render(data) {
    var host = document.getElementById('bots-content');

    if (data.track_bots_disabled) {
        host.innerHTML = '<div class="pa-card text-center py-5">' +
            '<i class="bi bi-toggle-off" style="font-size:2rem;color:var(--pa-primary);opacity:.4"></i>' +
            '<h5 class="mt-3 font-heading-bold">' + __t.trackingOff + '</h5>' +
            '<p style="color:var(--pa-text-muted);max-width:440px;margin:0.75rem auto 1.5rem">' + __t.trackingOffDesc + '</p>' +
            '<a href="' + __t.settingsUrl + '" class="btn-pa-primary"><i class="bi bi-gear me-1"></i>' + __t.websiteSettings + '</a>' +
            '</div>';
        return;
    }

    var botTotal = data.totals.bot_hits || 0;
    var humanTotal = data.totals.human_hits || 0;
    var combined = botTotal + humanTotal;
    var botPct = combined > 0 ? Math.round((botTotal / combined) * 100) : 0;

    var topBot = (data.by_bot[0] || {}).name || '—';

    var html = '';

    // Stat strip
    html += '<div class="row g-3 mb-4">';
    html += statCard(__t.botHits, botTotal.toLocaleString(), 'robot');
    html += statCard(__t.humanHits, humanTotal.toLocaleString(), 'person');
    html += statCard(__t.botShare, botPct + '%', 'pie-chart');
    html += statCard(__t.topBot, topBot, 'trophy');
    html += '</div>';

    // Chart
    html += '<div class="pa-card mb-4"><h6 class="mb-3 font-heading-bold">' + __t.activityOverTime + '</h6><canvas id="chart" height="80"></canvas></div>';

    // By bot table
    html += '<div class="row g-3">';
    html += '<div class="col-lg-6"><div class="pa-card p-0"><div style="padding:1rem 1.25rem;border-bottom:1px solid var(--pa-border)"><h6 class="mb-0 font-heading-bold">' + __t.byBot + '</h6></div>';
    if (!data.by_bot.length) {
        html += '<div class="text-center py-4 text-muted">' + __t.noBotTraffic + '</div>';
    } else {
        html += '<table class="pa-table"><thead><tr><th>' + __t.colBot + '</th><th class="text-end">' + __t.colHits + '</th><th class="text-end">' + __t.colUnique + '</th></tr></thead><tbody>';
        var maxHits = data.by_bot[0].hits || 1;
        data.by_bot.forEach(function(b) {
            var w = Math.round((+b.hits / maxHits) * 100);
            html += '<tr>';
            html += '<td><div style="display:flex;align-items:center;gap:0.5rem"><div style="flex:1"><div class="fw-medium">' + escapeHtml(b.name || '—') + '</div><div style="background:var(--pa-input-bg);border-radius:3px;height:4px;margin-top:0.25rem"><div style="background:var(--pa-primary);height:100%;width:' + w + '%;border-radius:3px"></div></div></div></div></td>';
            html += '<td class="text-num">' + (+b.hits).toLocaleString() + '</td>';
            html += '<td class="text-num-muted">' + (+b.visitors).toLocaleString() + '</td>';
            html += '</tr>';
        });
        html += '</tbody></table>';
    }
    html += '</div></div>';

    // Top pages
    html += '<div class="col-lg-6"><div class="pa-card p-0"><div style="padding:1rem 1.25rem;border-bottom:1px solid var(--pa-border)"><h6 class="mb-0 font-heading-bold">' + __t.topPagesByBots + '</h6></div>';
    if (!data.top_pages.length) {
        html += '<div class="text-center py-4 text-muted">' + __t.noBotPageviews + '</div>';
    } else {
        html += '<table class="pa-table"><thead><tr><th>' + __t.colPage + '</th><th class="text-end">' + __t.colHits + '</th><th class="text-end">' + __t.colDistinctBots + '</th></tr></thead><tbody>';
        data.top_pages.forEach(function(p) {
            html += '<tr>';
            html += '<td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="' + escapeHtml(p.url) + '"><code style="font-size:0.8125rem;background:transparent;padding:0">' + escapeHtml(p.url) + '</code></td>';
            html += '<td class="text-num">' + (+p.hits).toLocaleString() + '</td>';
            html += '<td class="text-num-muted">' + (+p.distinct_bots).toLocaleString() + '</td>';
            html += '</tr>';
        });
        html += '</tbody></table>';
    }
    html += '</div></div>';
    html += '</div>';

    host.innerHTML = html;

    // Render chart
    if (data.over_time && data.over_time.length) {
        var days = [...new Set(data.over_time.map(r => r.day))].sort();
        var bots = [...new Set(data.over_time.map(r => r.bot_name))].filter(b => b).slice(0, 8);
        var palette = ['#d04a1f', '#1fa36a', '#5865f2', '#f59e0b', '#8b5cf6', '#ec4899', '#0ea5e9', '#6b7280'];
        var datasets = bots.map(function(bot, i) {
            return {
                label: bot,
                data: days.map(d => {
                    var m = data.over_time.find(r => r.day === d && r.bot_name === bot);
                    return m ? +m.hits : 0;
                }),
                backgroundColor: palette[i % palette.length],
                borderColor: palette[i % palette.length],
                tension: 0.25,
                fill: false,
            };
        });
        if (chart) chart.destroy();
        chart = new Chart(document.getElementById('chart'), {
            type: 'line',
            data: { labels: days, datasets: datasets },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    }
}

function statCard(label, value, icon) {
    return '<div class="col-6 col-md-3"><div class="pa-card">' +
        '<div class="d-flex justify-content-between align-items-start mb-2">' +
        '<div style="font-size:0.875rem;color:var(--pa-text-muted)">' + label + '</div>' +
        '<i class="bi bi-' + icon + '" style="font-size:1.375rem;color:var(--pa-primary);opacity:0.7"></i>' +
        '</div>' +
        '<div style="font-size:1.5rem;font-weight:700;font-family:\'Space Grotesk\',sans-serif;line-height:1.1">' + escapeHtml(value) + '</div>' +
        '</div></div>';
}

function escapeHtml(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

document.addEventListener('DOMContentLoaded', loadData);
</script>
@endpush
