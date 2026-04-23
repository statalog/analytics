<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-accent="{{ config('statalog.accent', 'emerald') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('app.name')) - {{ __('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <script>(function(){var t=localStorage.getItem('sa-theme')||'light';document.documentElement.setAttribute('data-theme',t);})();</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="{{ asset('css/statalog.css') }}">
    @stack('styles')
</head>
<body>
    @include('components.sidebar')
    <div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="theme-toggle d-lg-none" onclick="toggleSidebar()" aria-label="Menu">
                <i class="bi bi-list" style="font-size:1.4rem"></i>
            </button>
            <a href="{{ route('home') }}" class="topbar-brand">{{ __('app.name') }}</a>
        </div>
        <div class="d-flex align-items-center gap-3">
            <button class="theme-toggle" onclick="toggleTheme()" id="theme-btn" title="Toggle theme">
                <i class="bi bi-sun-fill" id="theme-icon"></i>
            </button>
            <div class="dropdown">
                <button class="btn-pa-outline dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown" style="padding:0.375rem 0.75rem">
                    <i class="bi bi-person-circle"></i>
                    <span style="font-size:0.875rem;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ auth()->user()->name }}</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end pa-account-dropdown">
                    <div class="pa-account-header">
                        <div>
                            <div style="font-weight:600;font-size:0.9rem;color:var(--pa-text)">{{ auth()->user()->name }}</div>
                            <div style="font-size:0.8rem;color:var(--pa-text-muted)">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    <a href="{{ route('user.profile.edit') }}" class="dropdown-item">
                        <i class="bi bi-person"></i> Profile
                    </a>
                    <a href="{{ route('user.settings') }}" class="dropdown-item">
                        <i class="bi bi-sliders"></i> Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right"></i> {{ __('auth.btn_logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        @if(session()->has('impersonator_id'))
        <div style="background:#92400e;color:#fff;padding:0.5rem 1rem;display:flex;align-items:center;justify-content:space-between;font-size:0.8125rem">
            <span><i class="bi bi-incognito me-2"></i>You are impersonating <strong>{{ auth()->user()?->email }}</strong></span>
            <form method="POST" action="{{ route('cloud.admin.leave-impersonation') }}" class="m-0">
                @csrf
                <button type="submit" style="background:rgba(255,255,255,0.2);color:#fff;border:1px solid rgba(255,255,255,0.4);border-radius:4px;padding:0.2rem 0.75rem;font-size:0.75rem;font-weight:600;cursor:pointer">
                    Leave impersonation
                </button>
            </form>
        </div>
        @endif
        <div class="content-inner">
            @if(session('success'))
                <div class="pa-alert success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="pa-alert danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="pa-alert danger">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
    function toggleSidebar() {
        var sidebar = document.querySelector('.sidebar');
        var overlay = document.getElementById('sidebar-overlay');
        var isOpen = sidebar.classList.toggle('show');
        overlay.classList.toggle('show', isOpen);
    }
    function closeSidebar() {
        document.querySelector('.sidebar').classList.remove('show');
        document.getElementById('sidebar-overlay').classList.remove('show');
    }
    function toggleTheme() {
        var html = document.documentElement;
        var current = html.getAttribute('data-theme') || 'light';
        var next = current === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', next);
        localStorage.setItem('sa-theme', next);
        updateThemeIcon(next);
    }
    function updateThemeIcon(theme) {
        var icon = document.getElementById('theme-icon');
        if (icon) icon.className = theme === 'dark' ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
    }
    document.addEventListener('DOMContentLoaded', function() {
        var theme = localStorage.getItem('sa-theme') || 'light';
        updateThemeIcon(theme);

        if (window.innerWidth < 992) {
            document.querySelectorAll('.sidebar .nav-link').forEach(function(link) {
                link.addEventListener('click', closeSidebar);
            });
        }

        var customRange = document.getElementById('custom-date-range');
        if (customRange) {
            flatpickr(customRange, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                onChange: function(dates) {
                    if (dates.length === 2) {
                        var from = dates[0].toISOString().split('T')[0];
                        var to   = dates[1].toISOString().split('T')[0];
                        var url = new URL(window.location);
                        url.searchParams.set('from', from);
                        url.searchParams.set('to', to);
                        url.searchParams.delete('range');
                        window.location = url;
                    }
                }
            });
        }
    });
    </script>

    {{-- Reusable confirmation modal system. Any element with
         data-pa-confirm="<modal-id>" opens the matching <x-confirm-modal> and
         only submits/navigates after the user clicks Confirm. --}}
    <script>
    (function() {
        var pending = null;

        function openModal(id) {
            var m = document.querySelector('[data-pa-modal="' + id + '"]');
            var b = document.querySelector('[data-pa-modal-backdrop="' + id + '"]');
            if (!m || !b) return;
            b.classList.add('is-open');
            m.classList.add('is-open');
            m.setAttribute('aria-hidden', 'false');
            document.body.classList.add('pa-modal-open');
        }

        function closeModal(id) {
            var m = document.querySelector('[data-pa-modal="' + id + '"]');
            var b = document.querySelector('[data-pa-modal-backdrop="' + id + '"]');
            if (!m || !b) return;
            b.classList.remove('is-open');
            m.classList.remove('is-open');
            m.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('pa-modal-open');
            pending = null;
        }

        function closeAll() {
            document.querySelectorAll('.pa-modal.is-open').forEach(function(m) {
                closeModal(m.getAttribute('data-pa-modal'));
            });
        }

        // Intercept clicks on any element that requests confirmation.
        document.addEventListener('click', function(e) {
            var trigger = e.target.closest('[data-pa-confirm]');
            if (trigger) {
                e.preventDefault();
                pending = trigger;
                openModal(trigger.getAttribute('data-pa-confirm'));
                return;
            }

            // Confirm button: replay the original action.
            var confirmBtn = e.target.closest('[data-pa-modal-confirm]');
            if (confirmBtn && pending) {
                var id = confirmBtn.getAttribute('data-pa-modal-confirm');
                var t  = pending;
                pending = null;
                closeModal(id);

                if (t.tagName === 'A') {
                    window.location.href = t.href;
                } else if (t.form) {
                    t.form.submit();
                } else if (t.tagName === 'FORM') {
                    t.submit();
                } else if (t.dataset.paConfirmUrl) {
                    window.location.href = t.dataset.paConfirmUrl;
                }
                return;
            }

            // Close button or backdrop click.
            var closeBtn = e.target.closest('[data-pa-modal-close]');
            if (closeBtn) {
                closeAll();
                return;
            }
            if (e.target.classList.contains('pa-modal-backdrop')) {
                closeAll();
            }
        });

        // Esc closes.
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeAll();
        });

        // Expose imperative API: window.paModal.open('id') / close('id').
        window.paModal = { open: openModal, close: closeModal, closeAll: closeAll };
    })();
    </script>

    @stack('scripts')
</body>
</html>
