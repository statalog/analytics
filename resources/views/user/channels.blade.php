@extends('layouts.app')
@section('title', 'Channels')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
        <i class="bi bi-diagram-3 me-2" style="color:var(--pa-primary)"></i>Channels
    </h4>
    @include('components.date-range-picker', ['botFilter' => false])
</div>

<div class="pa-card" style="padding:0">
    <div id="channels-container">
        <div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var channelData = [];

var CHANNEL_ICONS = {
    'Search':       'bi-search',
    'Social':       'bi-share',
    'AI Assistants':'bi-robot',
    'Campaigns':    'bi-megaphone',
    'Websites':     'bi-link-45deg',
    'Direct':       'bi-arrow-right-circle',
};
var CHANNEL_COLORS = {
    'Search':       '#3B82F6',
    'Social':       '#F59E0B',
    'AI Assistants':'#8B5CF6',
    'Campaigns':    '#10B981',
    'Websites':     '#6B7280',
    'Direct':       '#EC4899',
};

function fmtDuration(sec) {
    sec = parseInt(sec) || 0;
    if (sec <= 0) return '0s';
    var m = Math.floor(sec / 60), s = sec % 60;
    return m > 0 ? m + 'm ' + String(s).padStart(2,'0') + 's' : s + 's';
}

function renderChannels(data) {
    if (!data || data.length === 0) {
        document.getElementById('channels-container').innerHTML =
            '<div class="text-center py-5" style="color:var(--pa-text-muted)">No data for selected period</div>';
        return;
    }

    var html = '<table class="pa-table" style="width:100%">';
    html += '<thead><tr>';
    html += '<th style="width:32px"></th>';
    html += '<th>Channel</th>';
    html += '<th style="text-align:right">Visits</th>';
    html += '<th style="text-align:right">Pageviews</th>';
    html += '<th style="text-align:right">Pages / Visit</th>';
    html += '<th style="text-align:right">Avg Duration</th>';
    html += '<th style="text-align:right">Bounce Rate</th>';
    html += '</tr></thead><tbody id="channels-body">';

    data.forEach(function(row, idx) {
        var icon  = CHANNEL_ICONS[row.channel] || 'bi-globe2';
        var color = CHANNEL_COLORS[row.channel] || '#6B7280';
        var hasSub = row.sources && row.sources.length > 0;

        html += '<tr class="channel-row" style="cursor:' + (hasSub ? 'pointer' : 'default') + '" onclick="' + (hasSub ? 'toggleChannel(' + idx + ')' : '') + '">';
        html += '<td style="text-align:center">';
        if (hasSub) {
            html += '<span id="toggle-' + idx + '" style="display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;border-radius:4px;background:var(--pa-border);font-size:0.75rem;transition:transform 0.2s"><i class="bi bi-plus"></i></span>';
        }
        html += '</td>';
        html += '<td><span style="display:inline-flex;align-items:center;gap:8px">';
        html += '<span style="width:28px;height:28px;border-radius:6px;background:' + color + '20;display:inline-flex;align-items:center;justify-content:center;color:' + color + ';font-size:0.875rem"><i class="bi ' + icon + '"></i></span>';
        html += '<strong>' + row.channel + '</strong>';
        html += '</span></td>';
        html += '<td style="text-align:right;font-weight:600">' + (row.visits || 0).toLocaleString() + '</td>';
        html += '<td style="text-align:right">' + (row.pageviews || 0).toLocaleString() + '</td>';
        html += '<td style="text-align:right">' + (row.pages_per_visit || 0) + '</td>';
        html += '<td style="text-align:right">' + fmtDuration(row.avg_duration) + '</td>';
        html += '<td style="text-align:right">' + (row.bounce_rate || 0) + '%</td>';
        html += '</tr>';

        if (hasSub) {
            html += '<tr id="sub-' + idx + '" style="display:none"><td colspan="7" style="padding:0">';
            html += '<table class="pa-table" style="width:100%;background:var(--pa-surface)">';
            html += '<thead><tr style="background:var(--pa-surface)">';
            html += '<th style="width:32px"></th><th style="padding-left:48px">Source</th>';
            html += '<th style="text-align:right">Visits</th><th style="text-align:right">Pageviews</th>';
            html += '<th style="text-align:right">Pages / Visit</th><th style="text-align:right">Avg Duration</th>';
            html += '<th style="text-align:right">Bounce Rate</th></tr></thead><tbody>';

            row.sources.forEach(function(src) {
                html += '<tr>';
                html += '<td></td>';
                html += '<td style="padding-left:48px">';
                html += '<a href="https://' + src.label + '" target="_blank" rel="noopener" style="color:var(--pa-primary);text-decoration:none" onclick="event.stopPropagation()">' + src.label + '</a>';
                html += '</td>';
                html += '<td style="text-align:right">' + (src.visits || 0).toLocaleString() + '</td>';
                html += '<td style="text-align:right">' + (src.pageviews || 0).toLocaleString() + '</td>';
                html += '<td style="text-align:right">' + (src.pages_per_visit || 0) + '</td>';
                html += '<td style="text-align:right">' + fmtDuration(src.avg_duration) + '</td>';
                html += '<td style="text-align:right">' + (src.bounce_rate || 0) + '%</td>';
                html += '</tr>';
            });

            html += '</tbody></table></td></tr>';
        }
    });

    html += '</tbody></table>';
    document.getElementById('channels-container').innerHTML = html;
}

function toggleChannel(idx) {
    var sub    = document.getElementById('sub-' + idx);
    var toggle = document.getElementById('toggle-' + idx);
    if (!sub) return;
    var open = sub.style.display !== 'none';
    sub.style.display = open ? 'none' : '';
    if (toggle) {
        toggle.innerHTML = open ? '<i class="bi bi-plus"></i>' : '<i class="bi bi-dash"></i>';
    }
}

function loadChannels() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.channels.data") }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(function(data) {
            channelData = data;
            renderChannels(data);
        });
}

document.addEventListener('DOMContentLoaded', loadChannels);
document.addEventListener('dateRangeChanged', loadChannels);
</script>
@endpush
