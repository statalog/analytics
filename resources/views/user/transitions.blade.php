@extends('layouts.app')
@section('title', 'Page Transitions')
@section('content')

<div class="d-flex align-items-center gap-2 mb-4">
    <h4 class="mb-0 font-heading-bold">
        <i class="bi bi-shuffle me-2 icon-primary"></i>Page Transitions
    </h4>
</div>

<div class="pa-card mb-3">
    <div class="d-flex gap-2 flex-wrap align-items-center">
        <select id="url-input" style="max-width:520px;min-width:320px"></select>
        <button onclick="runCheck()" class="btn-pa-primary" id="btn-check">
            <i class="bi bi-play-fill me-1"></i>Analyse
        </button>
    </div>
    <div class="text-sm-muted mt-2">
        Shows the pages visitors came from and went to for any page on your site.
    </div>
</div>

<div id="result"></div>

@endsection

@push('scripts')
<script>
var TRANSITIONS_URL = '{{ route("user.transitions.data") }}';

function pct(hits, total) {
    if (!total) return 0;
    return Math.round(hits / total * 100);
}

function shortUrl(url) {
    try {
        var u = new URL(url);
        var p = u.pathname + (u.search ? u.search : '');
        return p.length > 55 ? p.substring(0, 52) + '…' : p;
    } catch(e) { return url.length > 55 ? url.substring(0, 52) + '…' : url; }
}

function pageRow(item, total, color) {
    var p = pct(item.hits, total);
    return '<div class="d-flex align-items-center gap-2 mb-2" title="' + item.url + '">'
        + '<div style="flex:1;min-width:0">'
        + '<div style="font-size:0.8125rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">' + shortUrl(item.url) + '</div>'
        + '<div style="height:4px;border-radius:2px;background:var(--pa-border);margin-top:3px">'
        + '<div style="width:' + p + '%;height:100%;border-radius:2px;background:' + color + '"></div>'
        + '</div>'
        + '</div>'
        + '<span style="font-size:0.8125rem;font-weight:600;color:' + color + ';min-width:36px;text-align:right">' + p + '%</span>'
        + '</div>';
}

function runCheck() {
    var sel = document.getElementById('url-input');
    var url = (sel.tomselect ? sel.tomselect.getValue() : sel.value || '').trim();
    if (!url) return;

    var btn = document.getElementById('btn-check');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Analysing…';
    document.getElementById('result').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-secondary" role="status"></div></div>';

    var qs = window.location.search;
    fetch(TRANSITIONS_URL + '?url=' + encodeURIComponent(url) + '&' + qs.replace(/^\?/, ''))
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-play-fill me-1"></i>Analyse';

            if (data.error) {
                document.getElementById('result').innerHTML = '<div class="pa-card"><div class="alert alert-danger mb-0">' + data.error + '</div></div>';
                return;
            }

            if (!data.total) {
                document.getElementById('result').innerHTML = '<div class="pa-card"><div class="text-center py-3 text-muted">No data found for this page in the selected date range.</div></div>';
                return;
            }

            var total    = data.total;
            var entries  = data.entries  || 0;
            var exits    = data.exits    || 0;
            var fromPages = data.fromPages || [];
            var toPages   = data.toPages  || [];

            var entryPct = pct(entries, total);
            var exitPct  = pct(exits,   total);

            // Left column
            var leftHtml = '<div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--pa-text-muted);margin-bottom:0.75rem">Came from</div>';
            if (entries > 0) {
                leftHtml += '<div class="d-flex align-items-center gap-2 mb-2">'
                    + '<div style="flex:1">'
                    + '<div style="font-size:0.8125rem;color:var(--pa-text-muted);font-style:italic">Direct / Entry</div>'
                    + '<div style="height:4px;border-radius:2px;background:var(--pa-border);margin-top:3px">'
                    + '<div style="width:' + entryPct + '%;height:100%;border-radius:2px;background:#6b7280"></div>'
                    + '</div>'
                    + '</div>'
                    + '<span style="font-size:0.8125rem;font-weight:600;color:#6b7280;min-width:36px;text-align:right">' + entryPct + '%</span>'
                    + '</div>';
            }
            fromPages.forEach(function(item) { leftHtml += pageRow(item, total, 'var(--pa-primary)'); });
            if (!fromPages.length && !entries) {
                leftHtml += '<div class="text-sm-muted">No data</div>';
            }

            // Centre
            var shortPage = shortUrl(url);
            var centreHtml = '<div class="text-center">'
                + '<div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--pa-text-muted);margin-bottom:0.75rem">Selected page</div>'
                + '<div style="padding:1rem;border:2px solid var(--pa-primary);border-radius:10px;margin-bottom:1rem">'
                + '<div style="font-size:0.8125rem;font-weight:600;word-break:break-all;margin-bottom:0.5rem" title="' + url + '">' + shortPage + '</div>'
                + '<div style="font-size:1.5rem;font-weight:800;color:var(--pa-primary)">' + total.toLocaleString() + '</div>'
                + '<div class="text-xs-muted">pageviews</div>'
                + '</div>'
                + '<div class="text-sm-muted">'
                + '<div><span style="font-weight:600;color:#6b7280">' + entryPct + '%</span> direct entries</div>'
                + '<div><span style="font-weight:600;color:#ef4444">' + exitPct + '%</span> exits</div>'
                + '</div>'
                + '</div>';

            // Right column
            var rightHtml = '<div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--pa-text-muted);margin-bottom:0.75rem">Went to</div>';
            if (exits > 0) {
                rightHtml += '<div class="d-flex align-items-center gap-2 mb-2">'
                    + '<div style="flex:1">'
                    + '<div style="font-size:0.8125rem;color:#ef4444;font-style:italic">Exit (left site)</div>'
                    + '<div style="height:4px;border-radius:2px;background:var(--pa-border);margin-top:3px">'
                    + '<div style="width:' + exitPct + '%;height:100%;border-radius:2px;background:#ef4444"></div>'
                    + '</div>'
                    + '</div>'
                    + '<span style="font-size:0.8125rem;font-weight:600;color:#ef4444;min-width:36px;text-align:right">' + exitPct + '%</span>'
                    + '</div>';
            }
            toPages.forEach(function(item) { rightHtml += pageRow(item, total, '#10b981'); });
            if (!toPages.length && !exits) {
                rightHtml += '<div class="text-sm-muted">No data</div>';
            }

            var html = '<div class="pa-card">'
                + '<div class="row g-4">'
                + '<div class="col-md-4">' + leftHtml + '</div>'
                + '<div class="col-md-4 d-flex align-items-center justify-content-center" style="border-left:1px solid var(--pa-border);border-right:1px solid var(--pa-border)">' + centreHtml + '</div>'
                + '<div class="col-md-4">' + rightHtml + '</div>'
                + '</div>'
                + '</div>';

            document.getElementById('result').innerHTML = html;
        })
        .catch(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-play-fill me-1"></i>Analyse';
            document.getElementById('result').innerHTML = '<div class="pa-card"><div class="alert alert-danger mb-0">Request failed. Please try again.</div></div>';
        });
}

document.addEventListener('DOMContentLoaded', function() {
    var searchUrl = @json(route('user.transitions.search'));
    var preUrl = new URLSearchParams(window.location.search).get('url');

    var ts = new TomSelect('#url-input', {
        maxOptions: 25,
        openOnFocus: false,
        placeholder: 'Type to search pages…',
        load: function(query, callback) {
            if (!query.length) return callback();
            fetch(searchUrl + '?q=' + encodeURIComponent(query))
                .then(function(r) { return r.json(); })
                .then(callback)
                .catch(function() { callback(); });
        },
        onChange: function(value) { if (value) runCheck(); },
    });

    // Pre-fill if navigating from another page
    if (preUrl) {
        ts.addOption({ value: preUrl, text: preUrl.replace(/^https?:\/\//, '') });
        ts.setValue(preUrl, true);
        runCheck();
    }
});
</script>
@endpush
