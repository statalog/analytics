@php
    $isViewer = app()->bound('statalog.active_account_user')
        && app('statalog.active_account_user')?->isViewer();
    $sites = auth()->user()->sites ?? collect();
    $currentSiteId = session('current_site_id');
    $currentSite = $currentSiteId ? $sites->firstWhere('site_id', $currentSiteId) : $sites->first();

    $audienceRoutes = ['user.visitor-log*','user.pages*','user.locations*','user.devices*','user.time-of-day*'];
    $audienceOpen   = collect($audienceRoutes)->contains(fn($p) => request()->routeIs($p));

    $acquisitionRoutes = ['user.channels*','user.search-engines*','user.websites*','user.social-networks*','user.ai-sources*','user.campaigns*'];
    $acquisitionOpen   = collect($acquisitionRoutes)->contains(fn($p) => request()->routeIs($p));

    $behaviourRoutes = ['user.entry-exit*','user.visit-depth*','user.new-vs-returning*','user.time-on-page*','user.performance*','user.transitions*'];
    $behaviourOpen   = collect($behaviourRoutes)->contains(fn($p) => request()->routeIs($p));

    $seoRoutes = ['user.seo.*'];
    $seoOpen   = collect($seoRoutes)->contains(fn($p) => request()->routeIs($p));
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
            <option value="__manage__">{{ __('app.nav_manage_websites') }}</option>
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

        {{-- Audience dropdown --}}
        <div class="nav-group {{ $audienceOpen ? 'open' : '' }}" data-nav-group>
            <div class="nav-group-toggle" onclick="toggleNavGroup(this.parentElement)">
                <i class="bi bi-people nav-gi"></i> Audience
                <i class="bi bi-chevron-right nav-arrow"></i>
            </div>
            <div class="nav-group-items">
                <a href="{{ route('user.visitor-log') }}" class="nav-link {{ request()->routeIs('user.visitor-log*') ? 'active' : '' }}">
                    <i class="bi bi-person-lines-fill"></i> Visitors
                </a>
                <a href="{{ route('user.pages') }}" class="nav-link {{ request()->routeIs('user.pages*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i> Pages
                </a>
                <a href="{{ route('user.locations') }}" class="nav-link {{ request()->routeIs('user.locations*') ? 'active' : '' }}">
                    <i class="bi bi-geo-alt"></i> Visitor Locations
                </a>
                <a href="{{ route('user.devices') }}" class="nav-link {{ request()->routeIs('user.devices*') ? 'active' : '' }}">
                    <i class="bi bi-laptop"></i> Devices
                </a>
                <a href="{{ route('user.time-of-day') }}" class="nav-link {{ request()->routeIs('user.time-of-day*') ? 'active' : '' }}">
                    <i class="bi bi-clock"></i> Time of Day
                </a>
            </div>
        </div>

        {{-- Acquisition dropdown --}}
        <div class="nav-group {{ $acquisitionOpen ? 'open' : '' }}" data-nav-group>
            <div class="nav-group-toggle" onclick="toggleNavGroup(this.parentElement)">
                <i class="bi bi-diagram-3 nav-gi"></i> Acquisition
                <i class="bi bi-chevron-right nav-arrow"></i>
            </div>
            <div class="nav-group-items">
                <a href="{{ route('user.channels') }}" class="nav-link {{ request()->routeIs('user.channels*') ? 'active' : '' }}">
                    <i class="bi bi-grid"></i> All Channels
                </a>
                <a href="{{ route('user.search-engines') }}" class="nav-link {{ request()->routeIs('user.search-engines*') ? 'active' : '' }}">
                    <i class="bi bi-search"></i> Search Engines
                </a>
                <a href="{{ route('user.websites') }}" class="nav-link {{ request()->routeIs('user.websites*') ? 'active' : '' }}">
                    <i class="bi bi-link-45deg"></i> Websites
                </a>
                <a href="{{ route('user.social-networks') }}" class="nav-link {{ request()->routeIs('user.social-networks*') ? 'active' : '' }}">
                    <i class="bi bi-share"></i> Social Networks
                </a>
                <a href="{{ route('user.ai-sources') }}" class="nav-link {{ request()->routeIs('user.ai-sources*') ? 'active' : '' }}">
                    <i class="bi bi-robot"></i> AI Assistants
                </a>
                <a href="{{ route('user.campaigns') }}" class="nav-link {{ request()->routeIs('user.campaigns*') ? 'active' : '' }}">
                    <i class="bi bi-megaphone"></i> {{ __('app.nav_campaigns') }}
                </a>
            </div>
        </div>

        {{-- Behaviour dropdown --}}
        <div class="nav-group {{ $behaviourOpen ? 'open' : '' }}" data-nav-group>
            <div class="nav-group-toggle" onclick="toggleNavGroup(this.parentElement)">
                <i class="bi bi-activity nav-gi"></i> Behaviour
                <i class="bi bi-chevron-right nav-arrow"></i>
            </div>
            <div class="nav-group-items">
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
                    <i class="bi bi-hourglass-split"></i> {{ __('app.nav_time_on_page') }}
                </a>
                <a href="{{ route('user.performance') }}" class="nav-link {{ request()->routeIs('user.performance*') ? 'active' : '' }}">
                    <i class="bi bi-speedometer"></i> Performance
                </a>
                <a href="{{ route('user.transitions') }}" class="nav-link {{ request()->routeIs('user.transitions*') ? 'active' : '' }}">
                    <i class="bi bi-shuffle"></i> Page Transitions
                </a>
            </div>
        </div>
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
        <div class="nav-section-title">{{ __('app.nav_section_tools') }}</div>
        <a href="{{ route('user.errors') }}" class="nav-link {{ request()->routeIs('user.errors*') ? 'active' : '' }}">
            <i class="bi bi-bug"></i> Error tracking
        </a>
        <a href="{{ route('user.bots') }}" class="nav-link {{ request()->routeIs('user.bots*') ? 'active' : '' }}">
            <i class="bi bi-robot"></i> Bots
        </a>

        {{-- SEO Tools dropdown --}}
        <div class="nav-group {{ $seoOpen ? 'open' : '' }}" data-nav-group>
            <div class="nav-group-toggle" onclick="toggleNavGroup(this.parentElement)">
                <i class="bi bi-map nav-gi"></i> SEO Tools
                <i class="bi bi-chevron-right nav-arrow"></i>
            </div>
            <div class="nav-group-items">
                <a href="{{ route('user.seo.sitemap') }}" class="nav-link {{ request()->routeIs('user.seo.sitemap*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-2"></i> Sitemap
                </a>
                <a href="{{ route('user.seo.robots') }}" class="nav-link {{ request()->routeIs('user.seo.robots*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-code"></i> Robots.txt
                </a>
                <a href="{{ route('user.seo.broken-links') }}" class="nav-link {{ request()->routeIs('user.seo.broken-links*') ? 'active' : '' }}">
                    <i class="bi bi-link-45deg"></i> Broken Links
                </a>
                <a href="{{ route('user.seo.redirect-checker') }}" class="nav-link {{ request()->routeIs('user.seo.redirect-checker*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-left-right"></i> Redirect Checker
                </a>
                <a href="{{ route('user.seo.meta-tags') }}" class="nav-link {{ request()->routeIs('user.seo.meta-tags*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i> Meta Tags
                </a>
            </div>
        </div>

        <a href="{{ route('user.pdf-report') }}" class="nav-link {{ request()->routeIs('user.pdf-report*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-pdf"></i> PDF Report
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

    @if(!$isViewer || session('is_demo'))
    <div class="nav-section">
        <div class="nav-section-title">{{ __('app.nav_section_account') }}</div>
        <a href="{{ route('user.configuration') }}" class="nav-link {{ request()->routeIs('user.configuration*') || request()->routeIs('user.ga-import*') || request()->routeIs('user.general*') ? 'active' : '' }}">
            <i class="bi bi-gear-wide-connected"></i> Configuration
        </a>
    </div>
    @endif
</aside>

<script>
function toggleNavGroup(el) {
    el.classList.toggle('open');
}
</script>
