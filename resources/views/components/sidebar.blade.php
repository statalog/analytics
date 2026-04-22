@php
    $sites = auth()->user()->sites ?? collect();
    $currentSiteId = session('current_site_id');
    $currentSite = $currentSiteId ? $sites->firstWhere('site_id', $currentSiteId) : $sites->first();
@endphp
<aside class="sidebar">
    <a href="{{ route('user.overview') }}" class="sidebar-brand">
        <img src="{{ asset('img/logo-backend.png') }}" alt="{{ __('app.name') }}">
    </a>

    @if($sites->count())
    <div class="site-switcher">
        <select onchange="var v=this.value;if(v==='__manage__'){window.location.href='{{ route('user.sites.index') }}';}else{window.location.href='{{ route('user.dashboard') }}?switch_site='+v;}">
            @foreach($sites as $site)
                <option value="{{ $site->site_id }}" {{ $currentSite && $currentSite->site_id === $site->site_id ? 'selected' : '' }}>
                    {{ $site->name }}
                </option>
            @endforeach
            <option disabled>- - - - - - - - -</option>
            <option value="__manage__">Manage Websites</option>
        </select>
    </div>
    @endif

    <div class="nav-section">
        <a href="{{ route('user.sites.index') }}" class="nav-link {{ request()->routeIs('user.sites*') ? 'active' : '' }}">
            <i class="bi bi-globe2"></i> {{ __('app.nav_websites') }}
        </a>
        <a href="{{ route('user.overview') }}" class="nav-link {{ request()->routeIs('user.overview*') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i> {{ __('app.nav_overview') }}
        </a>
    </div>

    <div class="nav-section">
        <div class="nav-section-title">{{ __('app.nav_section_analytics') }}</div>
        <a href="{{ route('user.dashboard') }}" class="nav-link {{ request()->routeIs('user.dashboard*') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> {{ __('app.nav_dashboard') }}
        </a>
        <a href="{{ route('user.live') }}" class="nav-link {{ request()->routeIs('user.live*') ? 'active' : '' }}">
            <i class="bi bi-broadcast"></i> {{ __('app.nav_live') }}
        </a>
        <a href="{{ route('user.campaigns') }}" class="nav-link {{ request()->routeIs('user.campaigns*') ? 'active' : '' }}">
            <i class="bi bi-megaphone"></i> {{ __('app.nav_campaigns') }}
        </a>
        <a href="{{ route('user.entry-exit') }}" class="nav-link {{ request()->routeIs('user.entry-exit*') ? 'active' : '' }}">
            <i class="bi bi-door-open"></i> {{ __('app.nav_entry_exit') }}
        </a>
    </div>

    {{-- Cloud package injects extra sections here (AI Insights, Teams, Billing). --}}
    @if(view()->exists('cloud::partials.sidebar'))
        @include('cloud::partials.sidebar')
    @endif

    <div class="nav-section">
        <div class="nav-section-title">{{ __('app.nav_section_account') }}</div>
        <a href="{{ route('user.settings') }}" class="nav-link {{ request()->routeIs('user.settings*') ? 'active' : '' }}">
            <i class="bi bi-sliders"></i> {{ __('app.nav_settings') }}
        </a>
    </div>
</aside>
