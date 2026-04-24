@extends('layouts.app')
@section('title', 'Core Web Vitals')
@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
        <i class="bi bi-map me-2" style="color:var(--pa-primary)"></i>SEO Tools
    </h4>
</div>
@include('user.seo._nav')

@if(!$hasKey)
<div class="pa-card" style="border:1px dashed var(--pa-border)">
    <div class="d-flex gap-3 align-items-start">
        <i class="bi bi-key fs-4" style="color:var(--pa-primary)"></i>
        <div>
            <h6 class="mb-1" style="font-family:'Space Grotesk',sans-serif;font-weight:700">API key required</h6>
            <p style="font-size:0.875rem;color:var(--pa-text-muted);margin-bottom:0.75rem">
                Core Web Vitals uses the free Google PageSpeed Insights API. Add your key to <code>.env</code>:
            </p>
            <pre style="background:var(--pa-surface);padding:0.75rem;border-radius:6px;font-size:0.8125rem;margin:0">GOOGLE_PAGESPEED_KEY=your_key_here</pre>
            <p style="font-size:0.8125rem;color:var(--pa-text-muted);margin-top:0.5rem;margin-bottom:0">
                Get a free key at <a href="https://console.cloud.google.com" target="_blank" rel="noopener">Google Cloud Console</a> → APIs &amp; Services → PageSpeed Insights API → Credentials.
            </p>
        </div>
    </div>
</div>
@else
<div class="pa-card mb-3">
    <div class="d-flex gap-2 flex-wrap align-items-center">
        <input type="text" id="url-input" class="form-control" placeholder="https://example.com"
               value="https://{{ $site->domain }}" style="max-width:400px"
               onkeydown="if(event.key==='Enter')runCheck()">
        <select id="strategy" class="form-select form-select-sm" style="width:auto">
            <option value="mobile">Mobile</option>
            <option value="desktop">Desktop</option>
        </select>
        <button onclick="runCheck()" class="btn btn-sm btn-primary" id="btn-check">
            <i class="bi bi-play-fill me-1"></i>Analyse
        </button>
    </div>
</div>

<div id="result"></div>
@endif
@endsection

@push('scripts')
<script>
var THRESHOLDS = {
    lcp:  { good: 2500,  poor: 4000,  unit: 'ms', label: 'LCP',  name: 'Largest Contentful Paint' },
    fid:  { good: 100,   poor: 300,   unit: 'ms', label: 'FID',  name: 'First Input Delay' },
    cls:  { good: 0.1,   poor: 0.25,  unit: '',   label: 'CLS',  name: 'Cumulative Layout Shift' },
    inp:  { good: 200,   poor: 500,   unit: 'ms', label: 'INP',  name: 'Interaction to Next Paint' },
    fcp:  { good: 1800,  poor: 3000,  unit: 'ms', label: 'FCP',  name: 'First Contentful Paint' },
    ttfb: { good: 800,   poor: 1800,  unit: 'ms', label: 'TTFB', name: 'Time to First Byte' },
};

function catColor(cat) {
    return cat === 'FAST' || cat === 'GOOD' ? '#22c55e' : cat === 'AVERAGE' || cat === 'NEEDS_IMPROVEMENT' ? '#f59e0b' : '#ef4444';
}
function catLabel(cat) {
    return cat === 'FAST' || cat === 'GOOD' ? 'Good' : cat === 'AVERAGE' || cat === 'NEEDS_IMPROVEMENT' ? 'Needs Improvement' : 'Poor';
}
function fmtVal(key, val) {
    if (key === 'cls') return parseFloat(val).toFixed(3);
    if (typeof val === 'number' && val >= 1000) return (val / 1000).toFixed(1) + 's';
    return Math.round(val) + 'ms';
}

function runCheck() {
    var url      = document.getElementById('url-input').value.trim();
    var strategy = document.getElementById('strategy').value;
    if (!url) return;
    document.getElementById('btn-check').disabled = true;
    document.getElementById('btn-check').innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Analysing…';
    document.getElementById('result').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-secondary" role="status"></div><div class="mt-2" style="font-size:0.875rem;color:var(--pa-text-muted)">Running PageSpeed analysis… this takes ~10–20 seconds.</div></div>';

    fetch('{{ route("user.seo.core-web-vitals.check") }}?url=' + encodeURIComponent(url) + '&strategy=' + strategy)
        .then(r => r.json())
        .then(data => {
            document.getElementById('btn-check').disabled = false;
            document.getElementById('btn-check').innerHTML = '<i class="bi bi-play-fill me-1"></i>Analyse';

            if (data.error) { document.getElementById('result').innerHTML = '<div class="pa-card"><div class="alert alert-danger mb-0">' + data.error + '</div></div>'; return; }

            // Score gauge
            var sc = data.score;
            var scColor = sc >= 90 ? '#22c55e' : sc >= 50 ? '#f59e0b' : '#ef4444';
            var html = '<div class="row g-3 mb-3">';
            html += '<div class="col-md-3"><div class="pa-card text-center"><div style="font-size:2.5rem;font-weight:800;color:' + scColor + '">' + sc + '</div><div style="font-size:0.8125rem;color:var(--pa-text-muted)">Performance Score</div><div style="font-size:0.75rem;color:var(--pa-text-muted);text-transform:capitalize">' + data.strategy + '</div></div></div>';

            // Field data cards
            if (data.has_field_data) {
                html += '<div class="col-md-9"><div class="pa-card"><div class="d-flex align-items-center gap-2 mb-3"><h6 class="mb-0">Field Data</h6><span class="badge bg-secondary" style="font-size:0.7rem">Real users (p75)</span></div><div class="row g-2">';
                Object.entries(data.field).forEach(function([key, f]) {
                    var t = THRESHOLDS[key];
                    if (!t) return;
                    var color = catColor(f.category);
                    html += '<div class="col-6 col-md-4"><div style="padding:0.75rem;background:var(--pa-surface);border-radius:8px;border-left:3px solid ' + color + '">';
                    html += '<div style="font-size:0.75rem;color:var(--pa-text-muted)">' + t.name + '</div>';
                    html += '<div style="font-size:1.25rem;font-weight:700;color:' + color + '">' + fmtVal(key, f.value) + '</div>';
                    html += '<div style="font-size:0.7rem;color:' + color + '">' + catLabel(f.category) + '</div>';
                    html += '</div></div>';
                });
                html += '</div></div></div>';
            } else {
                html += '<div class="col-md-9"><div class="pa-card d-flex align-items-center gap-2"><i class="bi bi-info-circle text-secondary"></i><span style="font-size:0.875rem;color:var(--pa-text-muted)">No field data available — your site may not have enough traffic in the Chrome UX Report yet. Lab data is shown below.</span></div></div>';
            }
            html += '</div>';

            // Lab data
            html += '<div class="pa-card"><h6 class="mb-3">Lab Data <span class="badge bg-secondary ms-1" style="font-size:0.7rem">Simulated</span></h6><div class="row g-2">';
            var labMap = { lcp: 'lcp', fcp: 'fcp', ttfb: 'ttfb', cls: 'cls', tbt: 'tbt', si: 'si' };
            var labLabels = { lcp: 'LCP', fcp: 'FCP', ttfb: 'TTFB', cls: 'CLS', tbt: 'Total Blocking Time', si: 'Speed Index' };
            Object.entries(data.lab).forEach(function([key, val]) {
                if (!val) return;
                html += '<div class="col-6 col-md-4"><div style="padding:0.75rem;background:var(--pa-surface);border-radius:8px">';
                html += '<div style="font-size:0.75rem;color:var(--pa-text-muted)">' + (labLabels[key] || key) + '</div>';
                html += '<div style="font-size:1.1rem;font-weight:600">' + val + '</div>';
                html += '</div></div>';
            });
            html += '</div></div>';

            document.getElementById('result').innerHTML = html;
        });
}
</script>
@endpush
