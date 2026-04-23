<x-guest-layout>
<div class="auth-card" style="max-width:500px">
    <div class="auth-logo">
        <a href="{{ route('home') }}"><img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }}" style="height:42px;width:auto"></a>
    </div>

    @if(!empty($expired))
        <div class="auth-heading">Invitation expired</div>
        <div class="auth-subheading">This invitation link has expired. Ask <strong>{{ $invitation->owner->name }}</strong> to send a new one.</div>

    @elseif(!empty($wrong_email))
        <div class="auth-heading">Wrong account</div>
        <div class="auth-subheading">
            This invitation was sent to <strong>{{ $invitation->email }}</strong> but you're signed in as <strong>{{ $user_email }}</strong>.
        </div>
        <p style="font-size:0.875rem;color:var(--pa-text-muted);margin-top:1rem;text-align:center">
            Sign out and sign in with the correct account, or ask to be re-invited to <strong>{{ $user_email }}</strong>.
        </p>
        <div style="text-align:center;margin-top:1.25rem">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="auth-btn" style="width:auto;padding:0.5rem 1.5rem">Sign out</button>
            </form>
        </div>

    @else
        <div class="auth-heading">You've been invited</div>

        <div style="background:var(--pa-input-bg);border-radius:8px;padding:1rem 1.25rem;margin-bottom:1.5rem;font-size:0.9rem">
            <div style="margin-bottom:0.5rem"><span style="color:var(--pa-text-muted)">Invited by:</span> <strong>{{ $invitation->owner->name }}</strong></div>
            <div style="margin-bottom:0.5rem"><span style="color:var(--pa-text-muted)">Role:</span> <strong>{{ ucfirst($invitation->role) }}</strong>
                @if($invitation->role === 'admin')
                    — can manage everything
                @else
                    — read-only access
                @endif
            </div>
            <div><span style="color:var(--pa-text-muted)">Access:</span>
                @if($invitation->siteIds() !== null)
                    specific sites only
                @else
                    all sites
                @endif
            </div>
        </div>

        @if(auth()->check())
            <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}">
                @csrf
                <button type="submit" class="auth-btn">Accept invitation</button>
            </form>
        @else
            <p style="font-size:0.875rem;color:var(--pa-text-muted);text-align:center;margin-bottom:1.25rem">
                Sign in or create an account with <strong>{{ $invitation->email }}</strong> to accept.
            </p>
            <a href="{{ route('login') }}" class="auth-btn" style="display:block;text-align:center;text-decoration:none;margin-bottom:0.75rem">Sign in</a>
            @if(Route::has('register'))
            <a href="{{ route('register') }}" class="auth-btn" style="display:block;text-align:center;text-decoration:none;background:transparent;border:1px solid var(--pa-border);color:var(--pa-text)">Create account</a>
            @endif
        @endif

        <p style="font-size:0.75rem;color:var(--pa-text-muted);text-align:center;margin-top:1.5rem">
            Expires {{ $invitation->expires_at->format('M j, Y') }}
        </p>
    @endif
</div>
</x-guest-layout>
