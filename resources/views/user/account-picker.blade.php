<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose account — {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/statalog.css') }}">
    <style>
    body { background: var(--pa-bg); min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; font-family: 'Inter', sans-serif; padding: 2rem 1rem; }
    .picker-wrap { width: 100%; max-width: 520px; }
    .picker-logo { display: block; margin: 0 auto 2rem; }
    .picker-title { font-family: 'Space Grotesk', sans-serif; font-size: 1.5rem; font-weight: 700; text-align: center; margin-bottom: 0.375rem; }
    .picker-sub { color: var(--pa-text-muted); font-size: 0.9375rem; text-align: center; margin-bottom: 2rem; }
    .account-card { display: flex; align-items: center; gap: 1rem; width: 100%; text-align: left; padding: 1rem 1.25rem; background: var(--pa-card-bg); border: 1px solid var(--pa-border); border-radius: 0.875rem; cursor: pointer; transition: border-color .15s, box-shadow .15s; margin-bottom: 0.75rem; }
    .account-card:hover { border-color: var(--pa-primary); box-shadow: 0 4px 16px rgba(0,0,0,0.07); }
    .account-avatar { width: 2.75rem; height: 2.75rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.1rem; flex-shrink: 0; color: #fff; }
    .account-name { font-weight: 600; font-size: 1rem; color: var(--pa-text); line-height: 1.3; }
    .account-meta { font-size: 0.8125rem; color: var(--pa-text-muted); margin-top: 0.125rem; }
    .account-arrow { margin-left: auto; color: var(--pa-text-muted); font-size: 1rem; flex-shrink: 0; }
    .account-badge { font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.125rem 0.5rem; border-radius: 20px; margin-left: 0.5rem; vertical-align: middle; }
    .badge-you { background: color-mix(in srgb, var(--pa-primary) 12%, transparent); color: var(--pa-primary); }
    .badge-admin { background: color-mix(in srgb, #f59e0b 12%, transparent); color: #b45309; }
    .badge-viewer { background: color-mix(in srgb, #94a3b8 15%, transparent); color: var(--pa-text-muted); }
    </style>
</head>
<body>
<div class="picker-wrap">
    <a href="{{ route('home') }}">
        <img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }}" class="picker-logo">
    </a>

    <div class="picker-title">Choose an account</div>
    <div class="picker-sub">Logged in as <strong>{{ $user->email }}</strong></div>

    {{-- Own account --}}
    <form method="POST" action="{{ route('user.account-users.switch') }}">
        @csrf
        <input type="hidden" name="owner_id" value="0">
        <button type="submit" class="account-card" style="border:none">
            <div class="account-avatar" style="background: var(--pa-primary)">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <div class="account-name">
                    {{ $user->name }}
                    <span class="account-badge badge-you">Your account</span>
                </div>
                <div class="account-meta">
                    {{ $user->email }}
                    @if($ownSites->count())
                        &middot; {{ $ownSites->count() }} {{ Str::plural('site', $ownSites->count()) }}
                    @else
                        &middot; No sites yet
                    @endif
                </div>
            </div>
            <i class="bi bi-chevron-right account-arrow"></i>
        </button>
    </form>

    {{-- Member accounts --}}
    @foreach($memberships as $membership)
    @php
        $owner = $membership->owner;
        $colors = ['#6366f1','#0ea5e9','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899'];
        $color  = $colors[crc32($owner->email) % count($colors)];
        $siteCount = $owner->sites->count();
    @endphp
    <form method="POST" action="{{ route('user.account-users.switch') }}">
        @csrf
        <input type="hidden" name="owner_id" value="{{ $membership->owner_id }}">
        <button type="submit" class="account-card" style="border:none">
            <div class="account-avatar" style="background: {{ $color }}">
                {{ strtoupper(substr($owner->name, 0, 1)) }}
            </div>
            <div>
                <div class="account-name">
                    {{ $owner->name }}
                    <span class="account-badge {{ $membership->role === 'admin' ? 'badge-admin' : 'badge-viewer' }}">
                        {{ ucfirst($membership->role) }}
                    </span>
                </div>
                <div class="account-meta">
                    {{ $owner->email }}
                    &middot; {{ $siteCount }} {{ Str::plural('site', $siteCount) }}
                </div>
            </div>
            <i class="bi bi-chevron-right account-arrow"></i>
        </button>
    </form>
    @endforeach

    <div style="text-align:center;margin-top:1.5rem">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="background:none;border:none;color:var(--pa-text-muted);font-size:0.875rem;cursor:pointer;text-decoration:underline">
                Log out
            </button>
        </form>
    </div>
</div>
</body>
</html>
