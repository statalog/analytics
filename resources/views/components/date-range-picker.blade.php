@php
    $currentRange = (request('from') && request('to')) ? null : request('range', 'last7days');
    $currentHostname = request('hostname', '');
    $currentBots = request('bots', '');
    $currentSite = null;
    $currentSiteId = session('current_site_id');
    if ($currentSiteId && auth()->check()) {
        $currentSite = auth()->user()->sites()->where('site_id', $currentSiteId)->first();
    }
    $showBotFilter = $botFilter ?? true;
    $siteTracksBots = $showBotFilter && (bool) ($currentSite?->track_bots ?? false);
    $siteTracksSubdomains = (bool) ($currentSite?->track_subdomains ?? false);
    $ranges = [
        'today'      => __('analytics.range_today'),
        'yesterday'  => __('analytics.range_yesterday'),
        'last24h'    => __('analytics.range_last_24h'),
        'last7days'  => __('analytics.range_last_7_days'),
        'last30days' => __('analytics.range_last_30_days'),
        'this_month' => __('analytics.range_this_month'),
        'last_month' => __('analytics.range_last_month'),
    ];
@endphp
<div class="date-range-picker">
    @if($siteTracksBots)
    <div class="btn-group text-sm" role="group" aria-label="Bot filter">
        @php
            $botsOptions = [
                ''     => ['label' => 'Humans', 'icon' => 'person', 'title' => 'Human visitors only (default)'],
                '1'    => ['label' => 'All',    'icon' => 'stack',  'title' => 'Humans + bots'],
                'only' => ['label' => 'Bots',   'icon' => 'robot',  'title' => 'Bots only'],
            ];
        @endphp
        @foreach($botsOptions as $val => $opt)
            @php $active = ($currentBots === $val) || ($val === '' && $currentBots === ''); @endphp
            <a href="{{ request()->fullUrlWithQuery(['bots' => $val ?: null]) }}"
               class="btn {{ $active ? 'btn-pa-primary' : 'btn-pa-outline' }}"
               style="padding:0.35rem 0.6rem;font-size:0.8125rem"
               title="{{ $opt['title'] }}">
                <i class="bi bi-{{ $opt['icon'] }}"></i> {{ $opt['label'] }}
            </a>
        @endforeach
    </div>
    @endif

    @if($siteTracksSubdomains)
    <div id="hostname-filter-wrap" style="display:none">
        <select id="hostname-filter" class="pa-input" style="font-size:0.8125rem;padding:0.35rem 0.6rem;width:auto;min-width:160px">
            <option value="">{{ __('app.filter_all_subdomains') }}</option>
        </select>
    </div>
    @endif

    <div class="dropdown">
        <button class="date-range-btn dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-calendar3 me-1"></i>
            @if(request('from') && request('to'))
                {{ request('from') }} - {{ request('to') }}
            @else
                {{ $ranges[$currentRange] ?? __('analytics.range_last_7_days') }}
            @endif
        </button>
        <ul class="dropdown-menu pa-dropdown" style="min-width:180px">
            @foreach($ranges as $key => $label)
            <li>
                <a class="dropdown-item {{ $currentRange === $key ? 'active' : '' }}"
                   href="{{ request()->fullUrlWithQuery(['range' => $key, 'from' => null, 'to' => null, 'hostname' => $currentHostname ?: null]) }}">
                    {{ $label }}
                </a>
            </li>
            @endforeach
            <li><hr class="dropdown-divider"></li>
            <li class="px-3 py-2">
                <input type="text" id="custom-date-range" class="pa-input text-sm" placeholder="{{ __('analytics.range_custom') }}">
            </li>
        </ul>
    </div>
</div>

@once
@push('scripts')
@if($siteTracksSubdomains)
<script>
(function () {
    var hostnamesUrl = @json(route('user.dashboard.hostnames'));

    function getParam(name) {
        return new URLSearchParams(window.location.search).get(name) || '';
    }

    function loadHostnames() {
        var params = new URLSearchParams(window.location.search);
        fetch(hostnamesUrl + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!Array.isArray(data) || data.length < 2) return;
                var wrap = document.getElementById('hostname-filter-wrap');
                var select = document.getElementById('hostname-filter');
                var current = getParam('hostname');
                data.forEach(function(row) {
                    var opt = document.createElement('option');
                    opt.value = row.hostname;
                    opt.textContent = row.hostname + ' (' + row.hits + ')';
                    if (row.hostname === current) opt.selected = true;
                    select.appendChild(opt);
                });
                wrap.style.display = '';
                select.addEventListener('change', function() {
                    var url = new URL(window.location.href);
                    if (this.value) { url.searchParams.set('hostname', this.value); }
                    else { url.searchParams.delete('hostname'); }
                    window.location.href = url.toString();
                });
            })
            .catch(function() {});
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadHostnames);
    } else {
        loadHostnames();
    }
})();
</script>
@endif
@endpush
@endonce
