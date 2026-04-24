@extends('layouts.app')
@section('title', __('sites.page_index'))
@section('content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <h4 class="mb-0 font-heading-bold">
        <i class="bi bi-globe me-2 icon-primary"></i>{{ __('sites.page_index') }}
    </h4>
    <div class="d-flex align-items-center gap-2">
        @if($sites->count())
            @include('components.date-range-picker')
        @endif
        <a href="{{ route('user.sites.create') }}" class="btn-pa-primary">
            <i class="bi bi-plus-lg me-1"></i>{{ __('sites.btn_add_site') }}
        </a>
    </div>
</div>

@if($sites->isEmpty())
<div class="pa-card text-center py-5">
    <i class="bi bi-globe" style="font-size:2.5rem;color:var(--pa-primary);opacity:.35"></i>
    <h5 class="mt-3 mb-2 font-heading-bold">{{ __('sites.no_sites') }}</h5>
    <p style="color:var(--pa-text-muted);max-width:360px;margin:0 auto 1.5rem">{{ __('sites.no_sites_cta') }}</p>
    <a href="{{ route('user.sites.create') }}" class="btn-pa-primary">
        <i class="bi bi-plus-lg me-1"></i>{{ __('sites.btn_add_site') }}
    </a>
</div>
@else

{{-- Totals across all sites --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:1rem;margin-bottom:2rem">
    @php
    $cards = [
        ['label' => 'Total Visitors',  'value' => number_format($totalVisitors),  'icon' => 'people',  'trend' => $totalTrend],
        ['label' => 'Total Sessions',  'value' => number_format($totalSessions),  'icon' => 'cursor',  'trend' => null],
        ['label' => 'Total Pageviews', 'value' => number_format($totalPageviews), 'icon' => 'eye',     'trend' => null],
        ['label' => 'Sites Tracked',   'value' => count($siteStats),              'icon' => 'globe',   'trend' => null],
    ];
    @endphp
    @foreach($cards as $card)
    <div class="pa-card" style="padding:1rem 1.125rem">
        <div class="d-flex justify-content-between align-items-start">
            <div style="font-size:0.75rem;color:var(--pa-text-muted);font-weight:600;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:0.375rem">{{ $card['label'] }}</div>
            <i class="bi bi-{{ $card['icon'] }} icon-muted"></i>
        </div>
        <div style="font-size:1.5rem;font-weight:700;font-family:'Space Grotesk',sans-serif;line-height:1.1">{{ $card['value'] }}</div>
        @if($card['trend'] !== null)
        <div style="margin-top:0.25rem;font-size:0.75rem;font-weight:600;color:{{ $card['trend'] >= 0 ? 'var(--pa-success)' : 'var(--pa-danger)' }}">
            <i class="bi bi-arrow-{{ $card['trend'] >= 0 ? 'up' : 'down' }}-short"></i> {{ abs($card['trend']) }}% vs previous
        </div>
        @endif
    </div>
    @endforeach
</div>

<div style="font-size:0.8125rem;font-weight:600;color:var(--pa-text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.75rem">Your Websites</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1rem">
    @foreach($siteStats as $stat)
    @php $site = $stat['site']; $trend = $stat['trend']; @endphp
    <div class="pa-card" style="position:relative">
        <a href="{{ route('user.dashboard') }}?switch_site={{ $site->site_id }}" class="stretched-link" style="text-decoration:none" aria-label="Open {{ $site->name }} dashboard"></a>
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div style="min-width:0">
                <div style="font-weight:600;font-family:'Space Grotesk',sans-serif;font-size:0.9375rem;color:var(--pa-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $site->name }}</div>
                <div class="text-sm-muted">{{ $site->domain }}</div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-shrink-0 ms-2" style="position:relative;z-index:2">
                @if($stat['visitors'] > 0 && $trend !== null)
                <span style="font-size:0.7rem;font-weight:700;padding:0.2rem 0.45rem;border-radius:0.3rem;background:{{ $trend >= 0 ? 'rgba(34,197,94,0.15)' : 'rgba(239,68,68,0.15)' }};color:{{ $trend >= 0 ? 'var(--pa-success)' : 'var(--pa-danger)' }}">
                    {{ $trend >= 0 ? '+' : '' }}{{ $trend }}%
                </span>
                @endif
                <a href="{{ route('user.sites.show', $site) }}" class="btn-pa-outline" style="padding:0.2rem 0.45rem;font-size:0.75rem;position:relative;z-index:2" title="Site settings">
                    <i class="bi bi-gear"></i>
                </a>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:0.5rem;margin-top:0.75rem;padding-top:0.75rem;border-top:1px solid var(--pa-border)">
            @foreach([['Visitors', number_format($stat['visitors']), 'people'], ['Sessions', number_format($stat['sessions']), 'cursor'], ['Pageviews', number_format($stat['pageviews']), 'eye']] as [$lbl, $val, $ico])
            <div style="text-align:center">
                <div style="font-size:1.0625rem;font-weight:700;font-family:'Space Grotesk',sans-serif;line-height:1.1;color:var(--pa-text)">{{ $val }}</div>
                <div style="font-size:0.7rem;color:var(--pa-text-muted);margin-top:0.1rem"><i class="bi bi-{{ $ico }}" style="font-size:0.65rem"></i> {{ $lbl }}</div>
            </div>
            @endforeach
        </div>
        @if(!$site->is_active)
        <div style="margin-top:0.625rem;font-size:0.75rem;color:var(--pa-warning)"><i class="bi bi-pause-circle me-1"></i>Tracking paused</div>
        @endif
    </div>
    @endforeach
</div>
@endif
@endsection
