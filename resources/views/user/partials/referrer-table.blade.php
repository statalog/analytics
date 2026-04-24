{{--
    Shared referrer breakdown table.
    Expects: $dataRoute (route name), $emptyMessage (string)
--}}
<div class="pa-card" style="padding:0">
    <div id="ref-container">
        <div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div>
    </div>
</div>

@push('scripts')
<script>
function refFavicon(domain) {
    return '<img src="https://' + domain + '/favicon.ico" width="16" height="16" style="border-radius:2px;margin-right:6px;object-fit:contain;vertical-align:-2px" onerror="this.style.display=\'none\'">';
}

function refFmtDuration(sec) {
    sec = parseInt(sec) || 0;
    if (sec <= 0) return '0s';
    var m = Math.floor(sec / 60), s = sec % 60;
    return m > 0 ? m + 'm ' + String(s).padStart(2,'0') + 's' : s + 's';
}

function renderRefTable(data) {
    if (!data || data.length === 0) {
        document.getElementById('ref-container').innerHTML =
            '<div class="text-center py-5" style="color:var(--pa-text-muted)">{{ $emptyMessage }}</div>';
        return;
    }

    var totalVisits = data.reduce(function(s, r) { return s + r.visits; }, 0);

    var html = '<table class="pa-table" style="width:100%"><thead><tr>';
    html += '<th>Source</th>';
    html += '<th style="text-align:right">Visits</th>';
    html += '<th style="text-align:right">Share</th>';
    html += '<th style="text-align:right">Pageviews</th>';
    html += '<th style="text-align:right">Pages / Visit</th>';
    html += '<th style="text-align:right">Avg Duration</th>';
    html += '<th style="text-align:right">Bounce Rate</th>';
    html += '</tr></thead><tbody>';

    data.forEach(function(row) {
        var domain = row.domain || row.engine || '';
        var share  = totalVisits > 0 ? Math.round(row.visits / totalVisits * 100) : 0;
        html += '<tr>';
        html += '<td><span style="display:inline-flex;align-items:center">' + refFavicon(domain) + domain + '</span></td>';
        html += '<td style="text-align:right;font-weight:600">' + row.visits.toLocaleString() + '</td>';
        html += '<td style="text-align:right"><div style="display:flex;align-items:center;justify-content:flex-end;gap:8px"><div style="width:80px;height:6px;background:var(--pa-border);border-radius:3px;overflow:hidden"><div style="width:' + share + '%;height:100%;background:var(--pa-primary);border-radius:3px"></div></div><span style="min-width:32px;text-align:right">' + share + '%</span></div></td>';
        html += '<td style="text-align:right">' + row.pageviews.toLocaleString() + '</td>';
        html += '<td style="text-align:right">' + row.pages_per_visit + '</td>';
        html += '<td style="text-align:right">' + refFmtDuration(row.avg_duration) + '</td>';
        html += '<td style="text-align:right">' + row.bounce_rate + '%</td>';
        html += '</tr>';
    });

    html += '</tbody></table>';
    document.getElementById('ref-container').innerHTML = html;
}

function loadRefData() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route($dataRoute) }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(renderRefTable);
}

document.addEventListener('DOMContentLoaded', loadRefData);
document.addEventListener('dateRangeChanged', loadRefData);
</script>
@endpush
