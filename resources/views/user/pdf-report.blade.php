@extends('layouts.app')
@section('title', __('pdf.page_title'))
@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0 font-heading-bold">
        <i class="bi bi-file-earmark-pdf me-2 icon-primary"></i>{{ __('pdf.page_title') }}
    </h4>
</div>

<p style="color:var(--pa-text-muted);max-width:720px;margin-bottom:1.5rem">
    {{ __('pdf.intro') }}
</p>

<div class="row g-4">
    <div class="col-lg-5">
        <form method="GET" action="{{ route('user.pdf-report.generate') }}">
            <div class="pa-card">
                <h6 class="mb-3 font-heading-bold">{{ __('pdf.configure_report') }}</h6>

                <div class="mb-3">
                    <label class="auth-label">{{ __('pdf.date_range') }}</label>
                    <select name="range" class="pa-input" id="range-select" onchange="toggleCustom(this.value)">
                        <option value="last7days">{{ __('pdf.range_last_7_days') }}</option>
                        <option value="last30days">{{ __('pdf.range_last_30_days') }}</option>
                        <option value="this_month">{{ __('pdf.range_this_month') }}</option>
                        <option value="last_month">{{ __('pdf.range_last_month') }}</option>
                        <option value="custom">{{ __('pdf.range_custom') }}</option>
                    </select>
                </div>

                <div id="custom-range" class="row g-2 mb-3" style="display:none!important">
                    <div class="col-6">
                        <label class="auth-label">{{ __('pdf.from') }}</label>
                        <input type="date" name="from" class="pa-input" id="custom-from">
                    </div>
                    <div class="col-6">
                        <label class="auth-label">{{ __('pdf.to') }}</label>
                        <input type="date" name="to" class="pa-input" id="custom-to" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="auth-label d-block mb-2">{{ __('pdf.sections_to_include') }}</label>
                    <div class="d-flex flex-column gap-2">
                        @foreach([
                            'summary'   => ['icon' => 'grid-1x2',     'label' => __('pdf.section_summary')],
                            'pages'     => ['icon' => 'file-earmark', 'label' => __('pdf.section_pages')],
                            'sources'   => ['icon' => 'diagram-3',    'label' => __('pdf.section_sources')],
                            'locations' => ['icon' => 'geo-alt',      'label' => __('pdf.section_locations')],
                            'devices'   => ['icon' => 'phone',        'label' => __('pdf.section_devices')],
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
                    <i class="bi bi-download me-1"></i>{{ __('pdf.btn_download') }}
                </button>
            </div>
        </form>
    </div>

    <div class="col-lg-7">
        <div class="pa-card" style="background:var(--pa-input-bg);border:none">
            <h6 class="mb-3 font-heading-bold">{{ __('pdf.whats_included') }}</h6>
            <div class="d-flex flex-column gap-3" style="font-size:0.875rem;color:var(--pa-text-muted)">
                <div>
                    <div style="color:var(--pa-text);font-weight:600;margin-bottom:2px"><i class="bi bi-grid-1x2 me-1" style="color:var(--pa-primary)"></i>{{ __('pdf.section_summary') }}</div>
                    {{ __('pdf.summary_desc') }}
                </div>
                <div>
                    <div style="color:var(--pa-text);font-weight:600;margin-bottom:2px"><i class="bi bi-file-earmark me-1" style="color:var(--pa-primary)"></i>{{ __('pdf.section_pages') }}</div>
                    {{ __('pdf.pages_desc') }}
                </div>
                <div>
                    <div style="color:var(--pa-text);font-weight:600;margin-bottom:2px"><i class="bi bi-diagram-3 me-1" style="color:var(--pa-primary)"></i>{{ __('pdf.section_sources') }}</div>
                    {{ __('pdf.sources_desc') }}
                </div>
                <div>
                    <div style="color:var(--pa-text);font-weight:600;margin-bottom:2px"><i class="bi bi-geo-alt me-1" style="color:var(--pa-primary)"></i>{{ __('pdf.section_locations') }}</div>
                    {{ __('pdf.locations_desc') }}
                </div>
                <div>
                    <div style="color:var(--pa-text);font-weight:600;margin-bottom:2px"><i class="bi bi-phone me-1" style="color:var(--pa-primary)"></i>{{ __('pdf.section_devices') }}</div>
                    {{ __('pdf.devices_desc') }}
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
