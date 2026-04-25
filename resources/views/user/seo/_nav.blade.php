{{-- SEO tools tab bar, included at top of each SEO view --}}
<div class="d-flex gap-1 flex-wrap mb-4" style="border-bottom:1px solid var(--pa-border);padding-bottom:0.75rem">
    @php
        $seoLinks = [
            ['route' => 'user.seo.sitemap',          'icon' => 'map',          'label' => 'Sitemap'],
            ['route' => 'user.seo.robots',            'icon' => 'robot',        'label' => 'Robots.txt'],
            ['route' => 'user.seo.broken-links',      'icon' => 'link-break',   'label' => 'Broken Links'],
            ['route' => 'user.seo.redirect-checker',  'icon' => 'arrow-repeat', 'label' => 'Redirects'],
            ['route' => 'user.seo.meta-tags',         'icon' => 'tags',         'label' => 'Meta Tags'],
        ];
    @endphp
    @foreach($seoLinks as $link)
        <a href="{{ route($link['route']) }}"
           class="nav-link px-3 py-1 {{ request()->routeIs($link['route'] . '*') ? 'active' : '' }}"
           style="border-radius:6px;font-size:0.8125rem;{{ request()->routeIs($link['route'] . '*') ? 'background:var(--pa-primary);color:#fff' : '' }}">
            <i class="bi bi-{{ $link['icon'] }} me-1"></i>{{ $link['label'] }}
        </a>
    @endforeach
</div>
