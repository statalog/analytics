@extends('layouts.app')
@section('title', __('sites.page_index'))
@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
        <i class="bi bi-globe me-2" style="color:var(--pa-primary)"></i>{{ __('sites.page_index') }}
    </h4>
    <a href="{{ route('user.sites.create') }}" class="btn-pa-primary">
        <i class="bi bi-plus-lg me-1"></i>{{ __('sites.btn_add_site') }}
    </a>
</div>

@if($sites->isEmpty())
<div class="pa-card text-center py-5">
    <i class="bi bi-globe" style="font-size:2.5rem;color:var(--pa-primary);opacity:.35"></i>
    <h5 class="mt-3 mb-2" style="font-family:'Space Grotesk',sans-serif;font-weight:700">{{ __('sites.no_sites') }}</h5>
    <p style="color:var(--pa-text-muted);max-width:360px;margin:0 auto 1.5rem">{{ __('sites.no_sites_cta') }}</p>
    <a href="{{ route('user.sites.create') }}" class="btn-pa-primary">
        <i class="bi bi-plus-lg me-1"></i>{{ __('sites.btn_add_site') }}
    </a>
</div>
@else

{{-- Summary row --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-label">{{ __('sites.stats_today') }}</div>
            <div class="stat-value">{{ number_format($todayTotal) }}</div>
            <div class="stat-label">{{ __('sites.stats_hits') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-label">{{ __('sites.stats_this_month') }}</div>
            <div class="stat-value">{{ number_format($thisMonthTotal) }}</div>
            <div class="stat-label">{{ __('sites.stats_hits') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-label">{{ __('sites.stats_last_month') }}</div>
            <div class="stat-value">{{ number_format($lastMonthTotal) }}</div>
            <div class="stat-label">{{ __('sites.stats_hits') }}</div>
        </div>
    </div>
</div>

<div class="pa-card p-0">
    <div class="table-responsive">
        <table class="pa-table">
            <thead>
                <tr>
                    <th>{{ __('sites.field_name') }}</th>
                    <th>{{ __('sites.field_domain') }}</th>
                    <th class="text-end">{{ __('sites.stats_today') }}</th>
                    <th class="text-end">{{ __('sites.stats_this_month') }}</th>
                    <th class="text-end">{{ __('sites.stats_last_month') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($sites as $site)
                @php $counts = $hitCounts[$site->site_id] ?? ['today' => 0, 'this_month' => 0, 'last_month' => 0]; @endphp
                <tr>
                    <td>
                        <div style="font-weight:600">{{ $site->name }}</div>
                        <div style="font-size:0.75rem;color:var(--pa-text-muted)">{{ $site->site_id }}</div>
                    </td>
                    <td>
                        <a href="https://{{ $site->domain }}" target="_blank" rel="noopener" style="color:var(--pa-text-muted);font-size:0.875rem">
                            {{ $site->domain }} <i class="bi bi-box-arrow-up-right" style="font-size:0.7rem"></i>
                        </a>
                    </td>
                    <td class="text-end">{{ number_format($counts['today']) }}</td>
                    <td class="text-end">{{ number_format($counts['this_month']) }}</td>
                    <td class="text-end">{{ number_format($counts['last_month']) }}</td>
                    <td class="text-end">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            <a href="{{ route('user.dashboard') }}?switch_site={{ $site->site_id }}" class="btn-pa-outline" style="padding:0.25rem 0.5rem;font-size:0.8125rem">
                                <i class="bi bi-bar-chart-line me-1"></i>{{ __('sites.btn_view_stats') }}
                            </a>
                            <a href="{{ route('user.sites.show', $site) }}" class="btn-pa-outline" style="padding:0.25rem 0.5rem;font-size:0.8125rem">
                                <i class="bi bi-gear"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
