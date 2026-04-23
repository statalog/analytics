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
        <a href="{{ route('user.visit-depth') }}" class="nav-link {{ request()->routeIs('user.visit-depth*') ? 'active' : '' }}">
            <i class="bi bi-layers"></i> {{ __('app.nav_visit_depth') }}
        </a>
        <a href="{{ route('user.new-vs-returning') }}" class="nav-link {{ request()->routeIs('user.new-vs-returning*') ? 'active' : '' }}">
            <i class="bi bi-arrow-left-right"></i> {{ __('app.nav_new_vs_returning') }}
        </a>
        <a href="{{ route('user.time-on-page') }}" class="nav-link {{ request()->routeIs('user.time-on-page*') ? 'active' : '' }}">
            <i class="bi bi-clock"></i> {{ __('app.nav_time_on_page') }}
        </a>
    </div>

    <div class="nav-section">
        <div class="nav-section-title">{{ __('app.nav_section_conversion') }}</div>
        <a href="{{ route('user.funnels.index') }}" class="nav-link {{ request()->routeIs('user.funnels*') ? 'active' : '' }}">
            <i class="bi bi-funnel"></i> {{ __('app.nav_funnels') }}
        </a>
        <a href="{{ route('user.goals.index') }}" class="nav-link {{ request()->routeIs('user.goals*') ? 'active' : '' }}">
            <i class="bi bi-bullseye"></i> {{ __('app.nav_goals') }}
        </a>
        <a href="{{ route('user.events') }}" class="nav-link {{ request()->routeIs('user.events*') ? 'active' : '' }}">
            <i class="bi bi-lightning"></i> {{ __('app.nav_events') }}
        </a>
    </div>

    <div class="nav-section">
        <div class="nav-section-title">Tools</div>
        <a href="{{ route('user.errors') }}" class="nav-link {{ request()->routeIs('user.errors*') ? 'active' : '' }}">
            <i class="bi bi-bug"></i> Error tracking
        </a>
        {{-- Cloud injects AI Insights, Email reports, Heatmaps, etc. here. --}}
        @if(view()->exists('cloud::partials.tools'))
            @include('cloud::partials.tools')
        @endif
    </div>

    {{-- Cloud package injects Account-area sections (Billing, Admin). --}}
    @if(view()->exists('cloud::partials.sidebar'))
        @include('cloud::partials.sidebar')
    @endif

    <div class="nav-section">
        <div class="nav-section-title">{{ __('app.nav_section_account') }}</div>
        <a href="{{ route('user.configuration') }}" class="nav-link {{ request()->routeIs('user.configuration*') || request()->routeIs('user.ga-import*') || request()->routeIs('user.account-users*') || request()->routeIs('user.general*') ? 'active' : '' }}">
            <i class="bi bi-gear-wide-connected"></i> Configuration
        </a>
    </div>
</aside>
