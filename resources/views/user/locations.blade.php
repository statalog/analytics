@extends('layouts.app')
@section('title', 'Locations')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
        <i class="bi bi-geo-alt me-2" style="color:var(--pa-primary)"></i>Locations
    </h4>
    @include('components.date-range-picker')
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="pa-card p-0">
            <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--pa-border)">
                <h6 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">Countries</h6>
            </div>
            <div id="countries-table"><div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="pa-card p-0">
            <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--pa-border)">
                <h6 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">Cities</h6>
            </div>
            <div id="cities-table"><div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function loadData() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.locations.data") }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(function(data) {
            renderTable('countries-table', data.countries, 'country', 'visitors');
            renderCities(data.cities);
        });
}

function bar(pct) {
    return '<div style="background:var(--pa-input-bg);border-radius:3px;height:4px;margin-top:3px">'
         + '<div style="background:var(--pa-primary);height:100%;width:' + Math.min(100, pct) + '%;border-radius:3px"></div></div>';
}

function renderTable(id, rows, labelKey, valueKey) {
    if (!rows || !rows.length) {
        document.getElementById(id).innerHTML = '<div class="text-center py-4" style="color:var(--pa-text-muted)">No data.</div>';
        return;
    }
    var max = rows.reduce(function(m, r) { return Math.max(m, +r[valueKey]); }, 1);
    var html = '<table class="pa-table"><thead><tr><th>Location</th><th style="text-align:right">Visitors</th></tr></thead><tbody>';
    rows.forEach(function(r) {
        var pct = Math.round((+r[valueKey] / max) * 100);
        html += '<tr><td><div style="font-weight:500">' + escHtml(r[labelKey] || '—') + '</div>' + bar(pct) + '</td>'
              + '<td style="text-align:right;font-variant-numeric:tabular-nums">' + (+r[valueKey]).toLocaleString() + '</td></tr>';
    });
    html += '</tbody></table>';
    document.getElementById(id).innerHTML = html;
}

function renderCities(rows) {
    if (!rows || !rows.length) {
        document.getElementById('cities-table').innerHTML = '<div class="text-center py-4" style="color:var(--pa-text-muted)">No city data.</div>';
        return;
    }
    var max = rows.reduce(function(m, r) { return Math.max(m, +r.visitors); }, 1);
    var html = '<table class="pa-table"><thead><tr><th>City</th><th>Country</th><th style="text-align:right">Visitors</th></tr></thead><tbody>';
    rows.forEach(function(r) {
        var pct = Math.round((+r.visitors / max) * 100);
        html += '<tr><td><div style="font-weight:500">' + escHtml(r.city) + '</div>' + bar(pct) + '</td>'
              + '<td style="color:var(--pa-text-muted);font-size:0.8125rem">' + escHtml(r.country) + '</td>'
              + '<td style="text-align:right;font-variant-numeric:tabular-nums">' + (+r.visitors).toLocaleString() + '</td></tr>';
    });
    html += '</tbody></table>';
    document.getElementById('cities-table').innerHTML = html;
}

function escHtml(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

document.addEventListener('DOMContentLoaded', loadData);
</script>
@endpush
