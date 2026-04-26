@extends('layouts.app')
@section('title', __('ga-import.page_index'))
@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0 font-heading-bold">
        <i class="bi bi-cloud-download me-2 icon-primary"></i>{{ __('ga-import.page_index') }}
    </h4>
</div>

<p style="color:var(--pa-text-muted);max-width:720px;margin-bottom:1.5rem">
    {{ __('ga-import.intro') }}
</p>

@if(!$configured)
<div class="pa-card" style="border-left:3px solid var(--pa-warning);max-width:720px">
    <div class="fw-semibold mb-2"><i class="bi bi-exclamation-triangle me-1 text-warning"></i>{{ __('ga-import.oauth_not_configured_title') }}</div>
    <div style="font-size:0.875rem;color:var(--pa-text-muted);line-height:1.65">
        {{ __('ga-import.oauth_not_configured_body', ['redirect' => url('/account/ga-import/callback')]) }}
    </div>
</div>
@elseif(!$connected)
<div class="pa-card" style="max-width:720px">
    <div class="fw-semibold mb-2">{{ __('ga-import.step1_title') }}</div>
    <div style="font-size:0.875rem;color:var(--pa-text-muted);margin-bottom:1rem">
        {{ __('ga-import.step1_body') }}
    </div>
    <form method="POST" action="{{ route('user.ga-import.connect') }}">
        @csrf
        <button type="submit" class="btn-pa-primary">
            <i class="bi bi-google me-1"></i>{{ __('ga-import.btn_connect') }}
        </button>
    </form>
</div>
@else
<div class="row g-4" style="max-width:900px">
    <div class="col-md-8">
        <div class="pa-card">
            <div style="font-family:'Space Grotesk',sans-serif;font-weight:700;margin-bottom:0.5rem">
                <i class="bi bi-check-circle-fill me-1 text-success"></i>{{ __('ga-import.connected_title') }}
            </div>
            <div style="font-size:0.875rem;color:var(--pa-text-muted);margin-bottom:1.25rem">{{ __('ga-import.connected_body') }}</div>
            <a href="{{ route('user.ga-import.select') }}" class="btn-pa-primary">
                <i class="bi bi-arrow-right me-1"></i>{{ __('ga-import.btn_continue') }}
            </a>
            <form method="POST" action="{{ route('user.ga-import.disconnect') }}" class="d-inline ms-2">
                @csrf @method('DELETE')
                <button type="submit" class="btn-pa-outline">{{ __('ga-import.btn_disconnect') }}</button>
            </form>
        </div>
    </div>
    <div class="col-md-4">
        <div class="pa-card">
            <div style="font-family:'Space Grotesk',sans-serif;font-weight:700;margin-bottom:0.5rem;font-size:0.9rem">{{ __('ga-import.whats_imported_title') }}</div>
            <ul style="list-style:none;padding:0;margin:0;font-size:0.85rem;line-height:1.9;color:var(--pa-text-muted)">
                <li><i class="bi bi-check2 me-1 icon-primary"></i> {{ __('ga-import.whats_imported_1') }}</li>
                <li><i class="bi bi-check2 me-1 icon-primary"></i> {{ __('ga-import.whats_imported_2') }}</li>
                <li><i class="bi bi-check2 me-1 icon-primary"></i> {{ __('ga-import.whats_imported_3') }}</li>
                <li><i class="bi bi-check2 me-1 icon-primary"></i> {{ __('ga-import.whats_imported_4') }}</li>
                <li style="margin-top:0.4rem"><i class="bi bi-info-circle me-1 text-muted"></i> {{ __('ga-import.whats_imported_5') }}</li>
            </ul>
        </div>
    </div>
</div>
@endif

@if($imports->count())
<h6 class="mt-5 mb-3 font-heading-bold">{{ __('ga-import.recent_imports') }}</h6>
<div class="pa-card p-0" style="max-width:900px">
    <table class="pa-table">
        <thead>
            <tr><th>{{ __('ga-import.col_site') }}</th><th>{{ __('ga-import.col_ga_property') }}</th><th>{{ __('ga-import.col_range') }}</th><th>{{ __('ga-import.col_status') }}</th><th>{{ __('ga-import.col_progress') }}</th><th></th></tr>
        </thead>
        <tbody>
            @foreach($imports as $i)
            <tr>
                <td class="fw-medium">{{ $i->site?->name ?? '—' }}</td>
                <td class="text-sm-muted">{{ $i->ga_property_name ?? $i->ga_property_id }}</td>
                <td class="text-sm-muted">{{ $i->from_date?->format('M j, Y') }} – {{ $i->to_date?->format('M j, Y') }}</td>
                <td>
                    @php
                        $color = match($i->status) {
                            'completed' => 'var(--pa-success)',
                            'failed'    => 'var(--pa-danger)',
                            'running'   => 'var(--pa-primary)',
                            default     => 'var(--pa-text-muted)',
                        };
                    @endphp
                    <span style="color:{{ $color }};font-weight:600;font-size:0.8125rem">{{ ucfirst($i->status) }}</span>
                </td>
                <td style="min-width:130px">
                    <div style="height:6px;background:var(--pa-input-bg);border-radius:4px;overflow:hidden">
                        <div style="height:100%;width:{{ $i->progressPercent() }}%;background:{{ $color }}"></div>
                    </div>
                    <div style="font-size:0.7rem;color:var(--pa-text-muted);margin-top:0.25rem">{{ __('ga-import.days_progress', ['processed' => $i->processed_days, 'total' => $i->total_days]) }}</div>
                </td>
                <td class="text-end">
                    @if($i->status === 'completed' && $i->site)
                        <a href="{{ route('user.ga-import.summary', $i->site) }}" class="btn-pa-outline" style="padding:0.2rem 0.6rem;font-size:0.8125rem">{{ __('ga-import.btn_view') }}</a>
                    @elseif(in_array($i->status, ['queued', 'running']))
                        <a href="{{ route('user.ga-import.progress', $i) }}" class="btn-pa-outline" style="padding:0.2rem 0.6rem;font-size:0.8125rem">{{ __('ga-import.btn_progress') }}</a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
