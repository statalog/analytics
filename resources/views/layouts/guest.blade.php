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
    <div class="auth-wrapper">
        {{ $slot }}
        @php $locales = config('statalog.locales', []); @endphp
        @if(count($locales) > 1)
        <form method="POST" action="{{ route('locale.set', '_CODE_') }}" id="guest-locale-form" class="text-center mt-3" style="display:none">@csrf</form>
        <div class="text-center mt-3" style="font-size:0.8125rem;color:var(--pa-text-muted)">
            <i class="bi bi-translate me-1"></i>
            <select id="guest-locale-select" class="form-select form-select-sm d-inline-block w-auto" style="font-size:0.8125rem">
                @foreach($locales as $code => $label)
                    <option value="{{ $code }}" @selected(app()->getLocale() === $code)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <script>
        (function() {
            var sel = document.getElementById('guest-locale-select');
            var form = document.getElementById('guest-locale-form');
            sel.addEventListener('change', function() {
                form.action = form.action.replace('_CODE_', encodeURIComponent(sel.value));
                form.submit();
            });
        })();
        </script>
        @endif
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
