@extends('layouts.app')
@section('title', __('analytics.page_campaigns'))
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">{{ __('analytics.page_campaigns') }}</h4>
    @include('components.date-range-picker', ['botFilter' => false])
</div>

<div class="pa-card" style="padding:0">
    <div id="campaigns-table">
        <div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var __drilldown = null;

function loadCampaigns() {
    var params = new URLSearchParams(window.location.search);
    fetch('{{ route("user.campaigns.data") }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(function(data) { renderCampaigns(data); });
}

function renderCampaigns(rows) {
    if (!rows.length) {
        document.getElementById('campaigns-table').innerHTML = '<div class="text-center py-5" style="color:var(--pa-text-muted)">{{ __("analytics.no_campaign_data") }}</div>';
        return;
    }
    var html = '<table class="pa-table"><thead><tr>';
    html += '<th>{{ __("analytics.col_source") }}</th><th>{{ __("analytics.col_medium") }}</th><th>{{ __("analytics.col_campaign") }}</th>';
    html += '<th style="text-align:right">{{ __("analytics.col_visitors") }}</th><th style="text-align:right">{{ __("analytics.col_sessions") }}</th></tr></thead><tbody>';
    rows.forEach(function(row) {
        html += '<tr style="cursor:pointer" onclick="loadDrilldown(\'' + encodeURIComponent(row.utm_source || '') + '\',\'' + encodeURIComponent(row.utm_medium || '') + '\',\'' + encodeURIComponent(row.utm_campaign || '') + '\')">';
        html += '<td>' + (row.utm_source || '-') + '</td>';
        html += '<td>' + (row.utm_medium || '-') + '</td>';
        html += '<td>' + (row.utm_campaign || '-') + '</td>';
        html += '<td style="text-align:right">' + (row.visitors || 0).toLocaleString() + '</td>';
        html += '<td style="text-align:right">' + (row.sessions || 0).toLocaleString() + '</td>';
        html += '</tr>';
    });
    html += '</tbody></table>';
    document.getElementById('campaigns-table').innerHTML = html;
}

function loadDrilldown(source, medium, campaign) {
    var params = new URLSearchParams(window.location.search);
    if (source) params.set('utm_source', decodeURIComponent(source));
    if (medium) params.set('utm_medium', decodeURIComponent(medium));
    if (campaign) params.set('utm_campaign', decodeURIComponent(campaign));
    fetch('{{ route("user.campaigns.drilldown") }}?' + params.toString())
        .then(function(r) { return r.json(); })
        .then(function(data) { renderDrilldown(data, decodeURIComponent(source), decodeURIComponent(medium), decodeURIComponent(campaign)); });
}

function renderDrilldown(rows, source, medium, campaign) {
    var title = [source, medium, campaign].filter(Boolean).join(' / ');
    var html = '<div style="padding:1rem;border-bottom:1px solid var(--pa-border);display:flex;align-items:center;gap:0.75rem">';
    html += '<button class="btn-pa-outline" style="padding:0.25rem 0.5rem" onclick="loadCampaigns()"><i class="bi bi-arrow-left"></i></button>';
    html += '<span style="font-weight:600">' + title + '</span></div>';
    if (!rows.length) {
        html += '<div class="text-center py-4" style="color:var(--pa-text-muted)">No data</div>';
    } else {
        html += '<table class="pa-table"><thead><tr><th>{{ __("analytics.col_page_url") }}</th><th style="text-align:right">{{ __("analytics.col_visitors") }}</th></tr></thead><tbody>';
        rows.forEach(function(row) {
            html += '<tr><td>' + (row.url || '-').replace(/^https?:\/\//, '') + '</td><td style="text-align:right">' + (row.visitors || 0).toLocaleString() + '</td></tr>';
        });
        html += '</tbody></table>';
    }
    document.getElementById('campaigns-table').innerHTML = html;
}

document.addEventListener('DOMContentLoaded', loadCampaigns);
</script>
@endpush
