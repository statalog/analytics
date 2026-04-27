<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-accent="{{ config('statalog.accent', 'emerald') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('app.name')) - {{ __('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/statalog.css') }}">
</head>
<body>
    @php $locales = config('statalog.locales', []); @endphp
    @if(count($locales) > 1)
    <div class="auth-locale-bar">
        <div class="auth-locale" data-dropdown>
            <button type="button" class="auth-locale-trigger" onclick="this.closest('[data-dropdown]').classList.toggle('open')" aria-label="{{ __('app.language') }}">
                <i class="bi bi-translate"></i>
                <span>{{ $locales[app()->getLocale()] ?? strtoupper(app()->getLocale()) }}</span>
                <i class="bi bi-chevron-down auth-locale-chev"></i>
            </button>
            <div class="auth-locale-menu">
                @foreach($locales as $code => $label)
                    <a href="{{ \App\Support\Locale::url($code) }}" class="auth-locale-item @if(app()->getLocale() === $code) is-active @endif" hreflang="{{ str_replace('_', '-', $code) }}">
                        <span>{{ $label }}</span>
                        @if(app()->getLocale() === $code)<i class="bi bi-check2"></i>@endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="auth-wrapper">
        {{ $slot }}
    </div>

    <style>
    .auth-locale-bar {
        position: absolute; top: 1.25rem; right: 1.25rem; z-index: 50;
    }
    .auth-locale { position: relative; }
    .auth-locale-trigger {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.45rem 0.85rem; border-radius: 9999px;
        background: var(--pa-card-bg); border: 1px solid var(--pa-border);
        color: var(--pa-text); font: inherit; font-size: 0.875rem; font-weight: 500;
        cursor: pointer; transition: background .15s, border-color .15s;
    }
    .auth-locale-trigger:hover { background: color-mix(in srgb, var(--pa-text) 4%, var(--pa-card-bg)); border-color: color-mix(in srgb, var(--pa-text) 18%, var(--pa-border)); }
    .auth-locale-trigger:focus-visible { outline: 2px solid var(--pa-primary); outline-offset: 2px; }
    .auth-locale-trigger > .bi-translate { font-size: 0.95rem; opacity: 0.8; }
    .auth-locale-chev { font-size: 0.7rem; opacity: 0.65; transition: transform .15s; }
    .auth-locale.open .auth-locale-chev { transform: rotate(180deg); }
    .auth-locale-menu {
        position: absolute; top: calc(100% + 0.4rem); right: 0;
        background: var(--pa-card-bg); border: 1px solid var(--pa-border); border-radius: 0.6rem;
        min-width: 200px; padding: 0.4rem;
        box-shadow: 0 8px 24px rgba(0,0,0,0.10);
        opacity: 0; pointer-events: none; transform: translateY(-4px);
        transition: opacity .15s, transform .15s;
        z-index: 50;
    }
    .auth-locale.open .auth-locale-menu { opacity: 1; pointer-events: auto; transform: translateY(0); }
    .auth-locale-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.5rem 0.75rem; border-radius: 0.4rem;
        text-decoration: none; color: var(--pa-text); font-size: 0.875rem;
        transition: background .12s;
    }
    .auth-locale-item:hover { background: color-mix(in srgb, var(--pa-primary) 7%, transparent); color: var(--pa-text); }
    .auth-locale-item.is-active { color: var(--pa-primary); font-weight: 600; }
    </style>

    <script>
    document.addEventListener('click', function(e) {
        document.querySelectorAll('[data-dropdown].open').forEach(function(d) {
            if (!d.contains(e.target)) d.classList.remove('open');
        });
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
