@extends('layouts.app')
@section('title', 'Historical data — ' . $site->name)
@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('user.ga-import') }}" class="btn-pa-outline" style="padding:0.25rem 0.5rem"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h4 class="mb-0 font-heading-bold">{{ $site->name }}</h4>
        <div class="text-sm-muted">Historical data imported from Google Analytics</div>
    </div>
</div>

@php
    $totalVisitors  = $daily->sum('visitors');
    $totalPageviews = $daily->sum('pageviews');
    $totalSessions  = $daily->sum('sessions');
    $avgBounce      = $daily->count() ? round($daily->avg('bounce_rate'), 1) : 0;
@endphp

<div class="row g-3 mb-4" style="max-width:1000px">
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="stat-value">{{ number_format($totalVisitors) }}</div><div class="stat-label">Visitors</div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="stat-value">{{ number_format($totalPageviews) }}</div><div class="stat-label">Pageviews</div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="stat-value">{{ number_format($totalSessions) }}</div><div class="stat-label">Sessions</div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="stat-value">{{ $avgBounce }}%</div><div class="stat-label">Avg bounce rate</div></div>
    </div>
</div>

@if($daily->count())
<div class="pa-card mb-4" style="max-width:1000px">
    <h6 class="mb-3 font-heading">Pageviews per day</h6>
    <div style="height:220px"><canvas id="daily-chart"></canvas></div>
</div>
@endif

<div class="row g-4" style="max-width:1000px">
    <div class="col-md-6">
        <div class="pa-card">
            <h6 class="mb-3 font-heading">Top pages</h6>
            <table class="pa-table">
                <thead><tr><th>Page</th><th class="text-end">Pageviews</th></tr></thead>
                <tbody>
                    @forelse($pages as $p)
                        <tr><td>{{ \Illuminate\Support\Str::limit($p->page_path, 55) }}</td><td class="text-end">{{ number_format($p->pageviews) }}</td></tr>
                    @empty
                        <tr><td colspan="2" class="text-center py-3 text-muted">No data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="pa-card">
            <h6 class="mb-3 font-heading">Top sources</h6>
            <table class="pa-table">
                <thead><tr><th>Source</th><th class="text-end">Visitors</th></tr></thead>
                <tbody>
                    @forelse($sources as $s)
                        <tr><td>{{ $s->source ?: '(direct)' }}</td><td class="text-end">{{ number_format($s->visitors) }}</td></tr>
                    @empty
                        <tr><td colspan="2" class="text-center py-3 text-muted">No data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="pa-card">
            <h6 class="mb-3 font-heading">Top countries</h6>
            <table class="pa-table">
                <thead><tr><th>Country</th><th class="text-end">Visitors</th></tr></thead>
                <tbody>
                    @forelse($countries as $c)
                        <tr><td>{{ $c->country ?: 'Unknown' }}</td><td class="text-end">{{ number_format($c->visitors) }}</td></tr>
                    @empty
                        <tr><td colspan="2" class="text-center py-3 text-muted">No data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($daily->count())
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const labels = {!! json_encode($daily->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M j'))->values()) !!};
    const data   = {!! json_encode($daily->pluck('pageviews')->values()) !!};
    new Chart(document.getElementById('daily-chart').getContext('2d'), {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Pageviews', data, backgroundColor: 'var(--pa-primary)', borderRadius: 3, barPercentage: 0.7 }] },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#6B7290', maxTicksLimit: 12 } },
                y: { grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { color: '#6B7290' }, beginAtZero: true }
            }
        }
    });
});
</script>
@endpush
@endif
@endsection
