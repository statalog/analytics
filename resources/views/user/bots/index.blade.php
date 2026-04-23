@extends('layouts.app')
@section('title', 'Bots')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
        <i class="bi bi-robot me-2" style="color:var(--pa-primary)"></i>Bots
    </h4>
    @include('components.date-range-picker')
</div>

<p style="color:var(--pa-text-muted);max-width:720px;margin-bottom:1.5rem">
    Crawlers and bots indexing or scraping your site: search engines, AI models, SEO tools, link-preview fetchers, and headless browsers. Always kept separate from your human analytics.
</p>

<div id="bots-content"><div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div></div>
@endsection

@push('scripts')
<script>
let chart = null;

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
            '<h5 class="mt-3" style="font-family:\'Space Grotesk\',sans-serif;font-weight:700">Bot tracking is off</h5>' +
            '<p style="color:var(--pa-text-muted);max-width:440px;margin:0.75rem auto 1.5rem">Enable <strong>Store bot traffic</strong> on your website settings to start capturing crawler activity. Bots stay excluded from your regular analytics.</p>' +
            '<a href="{{ route("user.sites.show", $site) }}" class="btn-pa-primary"><i class="bi bi-gear me-1"></i>Website settings</a>' +
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
    html += statCard('Bot hits', botTotal.toLocaleString(), 'robot');
    html += statCard('Human hits', humanTotal.toLocaleString(), 'person');
    html += statCard('Bot share', botPct + '%', 'pie-chart');
    html += statCard('Top bot', topBot, 'trophy');
    html += '</div>';

    // Chart
    html += '<div class="pa-card mb-4"><h6 class="mb-3" style="font-family:\'Space Grotesk\',sans-serif;font-weight:700">Bot activity over time</h6><canvas id="chart" height="80"></canvas></div>';

    // By bot table
    html += '<div class="row g-3">';
    html += '<div class="col-lg-6"><div class="pa-card p-0"><div style="padding:1rem 1.25rem;border-bottom:1px solid var(--pa-border)"><h6 class="mb-0" style="font-family:\'Space Grotesk\',sans-serif;font-weight:700">By bot</h6></div>';
    if (!data.by_bot.length) {
        html += '<div class="text-center py-4" style="color:var(--pa-text-muted)">No bot traffic in this range.</div>';
    } else {
        html += '<table class="pa-table"><thead><tr><th>Bot</th><th style="text-align:right">Hits</th><th style="text-align:right">Unique</th></tr></thead><tbody>';
        var maxHits = data.by_bot[0].hits || 1;
        data.by_bot.forEach(function(b) {
            var w = Math.round((+b.hits / maxHits) * 100);
            html += '<tr>';
            html += '<td><div style="display:flex;align-items:center;gap:0.5rem"><div style="flex:1"><div style="font-weight:500">' + escapeHtml(b.name || '—') + '</div><div style="background:var(--pa-input-bg);border-radius:3px;height:4px;margin-top:0.25rem"><div style="background:var(--pa-primary);height:100%;width:' + w + '%;border-radius:3px"></div></div></div></div></td>';
            html += '<td style="text-align:right;font-variant-numeric:tabular-nums">' + (+b.hits).toLocaleString() + '</td>';
            html += '<td style="text-align:right;font-variant-numeric:tabular-nums;color:var(--pa-text-muted)">' + (+b.visitors).toLocaleString() + '</td>';
            html += '</tr>';
        });
        html += '</tbody></table>';
    }
    html += '</div></div>';

    // Top pages
    html += '<div class="col-lg-6"><div class="pa-card p-0"><div style="padding:1rem 1.25rem;border-bottom:1px solid var(--pa-border)"><h6 class="mb-0" style="font-family:\'Space Grotesk\',sans-serif;font-weight:700">Top pages hit by bots</h6></div>';
    if (!data.top_pages.length) {
        html += '<div class="text-center py-4" style="color:var(--pa-text-muted)">No bot pageviews in this range.</div>';
    } else {
        html += '<table class="pa-table"><thead><tr><th>Page</th><th style="text-align:right">Hits</th><th style="text-align:right">Distinct bots</th></tr></thead><tbody>';
        data.top_pages.forEach(function(p) {
            html += '<tr>';
            html += '<td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="' + escapeHtml(p.url) + '"><code style="font-size:0.8125rem;background:transparent;padding:0">' + escapeHtml(p.url) + '</code></td>';
            html += '<td style="text-align:right;font-variant-numeric:tabular-nums">' + (+p.hits).toLocaleString() + '</td>';
            html += '<td style="text-align:right;font-variant-numeric:tabular-nums;color:var(--pa-text-muted)">' + (+p.distinct_bots).toLocaleString() + '</td>';
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
        '<div class="d-flex justify-content-between align-items-start">' +
        '<div style="font-size:0.75rem;color:var(--pa-text-muted)">' + label + '</div>' +
        '<i class="bi bi-' + icon + '" style="color:var(--pa-text-muted);opacity:0.5;font-size:0.9rem"></i>' +
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
