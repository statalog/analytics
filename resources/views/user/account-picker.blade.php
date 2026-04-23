<x-guest-layout>
<div class="auth-card" style="max-width:520px">
    <div class="auth-logo">
        <a href="{{ route('home') }}"><img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }}" style="height:42px;width:auto"></a>
    </div>
    <div class="auth-heading">Choose an account</div>
    <div class="auth-subheading">You have access to multiple accounts. Which one would you like to open?</div>

    <div style="margin-top:1.5rem;display:flex;flex-direction:column;gap:0.75rem">
        @if($ownSites->count())
        <form method="POST" action="{{ route('user.account-users.switch') }}">
            @csrf
            <input type="hidden" name="owner_id" value="0">
            <button type="submit" class="auth-btn" style="width:100%;text-align:left;display:flex;align-items:center;gap:0.75rem;padding:0.875rem 1.25rem;background:var(--pa-input-bg);color:var(--pa-text);border:1px solid var(--pa-border)">
                <i class="bi bi-person-circle" style="font-size:1.25rem;color:var(--pa-primary)"></i>
                <div>
                    <div style="font-weight:600">{{ $user->name }} (you)</div>
                    <div style="font-size:0.8125rem;color:var(--pa-text-muted)">{{ $ownSites->count() }} {{ Str::plural('site', $ownSites->count()) }}</div>
                </div>
            </button>
        </form>
        @endif

        @foreach($memberships as $membership)
        <form method="POST" action="{{ route('user.account-users.switch') }}">
            @csrf
            <input type="hidden" name="owner_id" value="{{ $membership->owner_id }}">
            <button type="submit" class="auth-btn" style="width:100%;text-align:left;display:flex;align-items:center;gap:0.75rem;padding:0.875rem 1.25rem;background:var(--pa-input-bg);color:var(--pa-text);border:1px solid var(--pa-border)">
                <i class="bi bi-building" style="font-size:1.25rem;color:var(--pa-primary)"></i>
                <div>
                    <div style="font-weight:600">{{ $membership->owner->name }}</div>
                    <div style="font-size:0.8125rem;color:var(--pa-text-muted)">{{ $membership->owner->email }} &middot; {{ ucfirst($membership->role) }}</div>
                </div>
            </button>
        </form>
        @endforeach
    </div>
</div>
</x-guest-layout>
