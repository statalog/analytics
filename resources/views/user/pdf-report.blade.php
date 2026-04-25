@extends('layouts.app')
@section('title', 'PDF Report')
@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0 font-heading-bold">
        <i class="bi bi-file-earmark-pdf me-2 icon-primary"></i>PDF Report
    </h4>
</div>

<p style="color:var(--pa-text-muted);max-width:720px;margin-bottom:1.5rem">
    Generate a PDF snapshot of your analytics data for any date range. Choose the sections you want to include and download instantly.
</p>

<div class="row g-4">
    <div class="col-lg-5">
        <form method="GET" action="{{ route('user.pdf-report.generate') }}">
            <div class="pa-card">
                <h6 class="mb-3 font-heading-bold">Configure report</h6>

                <div class="mb-3">
                    <label class="auth-label">Date range</label>
                    <select name="range" class="pa-input" id="range-select" onchange="toggleCustom(this.value)">
                        <option value="last7days">Last 7 days</option>
                        <option value="last30days">Last 30 days</option>
                        <option value="this_month">This month</option>
                        <option value="last_month">Last month</option>
                        <option value="custom">Custom range…</option>
                    </select>
                </div>

                <div id="custom-range" class="row g-2 mb-3" style="display:none!important">
                    <div class="col-6">
                        <label class="auth-label">From</label>
                        <input type="date" name="from" class="pa-input" id="custom-from">
                    </div>
                    <div class="col-6">
                        <label class="auth-label">To</label>
                        <input type="date" name="to" class="pa-input" id="custom-to" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="auth-label d-block mb-2">Sections to include</label>
                    <div class="d-flex flex-column gap-2">
                        @foreach([
                            'summary'   => ['icon' => 'grid-1x2',     'label' => 'Summary stats'],
                            'pages'     => ['icon' => 'file-earmark', 'label' => 'Top pages'],
                            'sources'   => ['icon' => 'diagram-3',    'label' => 'Traffic sources'],
                            'locations' => ['icon' => 'geo-alt',      'label' => 'Locations'],
                            'devices'   => ['icon' => 'phone',        'label' => 'Devices & browsers'],
                        ] as $key => $item)
                        <label class="d-flex align-items-center justify-content-between gap-2" style="cursor:pointer;font-size:0.875rem;color:var(--pa-text);padding:0.375rem 0;border-bottom:1px solid var(--pa-border)">
                            <span class="d-flex align-items-center gap-2">
                                <i class="bi bi-{{ $item['icon'] }}" style="color:var(--pa-primary);width:1rem;text-align:center"></i>
                                {{ $item['label'] }}
                            </span>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="sections[]" value="{{ $key }}" checked
                                       style="width:2.25rem;height:1.25rem;cursor:pointer;accent-color:var(--pa-primary)">
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn-pa-primary w-100">
                    <i class="bi bi-download me-1"></i>Download PDF
                </button>
            </div>
        </form>
    </div>

    <div class="col-lg-7">
        <div class="pa-card" style="background:var(--pa-input-bg);border:none">
            <h6 class="mb-3 font-heading-bold">What's included</h6>
            <div class="d-flex flex-column gap-3" style="font-size:0.875rem;color:var(--pa-text-muted)">
                <div>
                    <div style="color:var(--pa-text);font-weight:600;margin-bottom:2px"><i class="bi bi-grid-1x2 me-1" style="color:var(--pa-primary)"></i>Summary stats</div>
                    Visitors, sessions, pageviews, bounce rate, and average visit duration for the selected period with comparison to the previous period.
                </div>
                <div>
                    <div style="color:var(--pa-text);font-weight:600;margin-bottom:2px"><i class="bi bi-file-earmark me-1" style="color:var(--pa-primary)"></i>Top pages</div>
                    The 15 most visited pages ranked by pageview count.
                </div>
                <div>
                    <div style="color:var(--pa-text);font-weight:600;margin-bottom:2px"><i class="bi bi-diagram-3 me-1" style="color:var(--pa-primary)"></i>Traffic sources</div>
                    Where visitors came from — direct, search engines, social networks, and referrers.
                </div>
                <div>
                    <div style="color:var(--pa-text);font-weight:600;margin-bottom:2px"><i class="bi bi-geo-alt me-1" style="color:var(--pa-primary)"></i>Locations</div>
                    Top countries by unique visitor count.
                </div>
                <div>
                    <div style="color:var(--pa-text);font-weight:600;margin-bottom:2px"><i class="bi bi-phone me-1" style="color:var(--pa-primary)"></i>Devices & browsers</div>
                    Device type breakdown (desktop, mobile, tablet) and top browsers.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleCustom(val) {
    var el = document.getElementById('custom-range');
    if (val === 'custom') {
        el.style.removeProperty('display');
        document.getElementById('custom-from').value = new Date(Date.now() - 30*86400000).toISOString().slice(0,10);
    } else {
        el.style.cssText = 'display:none!important';
    }
}
</script>
@endsection
