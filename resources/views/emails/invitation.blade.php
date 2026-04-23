<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>You've been invited</title>
<style>
body{margin:0;padding:0;background:#f5f5f5;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;color:#1a1a2e}
.wrap{max-width:520px;margin:40px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08)}
.header{background:#d04a1f;padding:32px 40px;text-align:center}
.header img{height:36px;width:auto}
.body{padding:40px}
.body h2{margin:0 0 8px;font-size:1.375rem;font-weight:700;letter-spacing:-0.01em}
.body p{margin:0 0 16px;color:#555;line-height:1.6;font-size:0.9375rem}
.btn{display:inline-block;background:#d04a1f;color:#fff !important;text-decoration:none;padding:13px 28px;border-radius:8px;font-weight:600;font-size:0.9375rem;margin:8px 0 24px}
.meta{background:#f8f8f8;border-radius:8px;padding:16px 20px;margin-bottom:24px;font-size:0.875rem;color:#555}
.meta strong{color:#1a1a2e}
.footer{padding:24px 40px;border-top:1px solid #eee;font-size:0.8125rem;color:#999}
.footer a{color:#d04a1f;text-decoration:none}
</style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }}">
    </div>
    <div class="body">
        <h2>You've been invited</h2>
        <p><strong>{{ $invitation->owner->name }}</strong> has invited you to access their {{ config('app.name') }} analytics as a <strong>{{ ucfirst($invitation->role) }}</strong>.</p>

        <div class="meta">
            <div><strong>Invited by:</strong> {{ $invitation->owner->name }} ({{ $invitation->owner->email }})</div>
            <div style="margin-top:6px"><strong>Role:</strong> {{ ucfirst($invitation->role) }}</div>
            @if($invitation->siteIds() !== null)
            <div style="margin-top:6px"><strong>Access:</strong> specific sites only</div>
            @else
            <div style="margin-top:6px"><strong>Access:</strong> all sites</div>
            @endif
            <div style="margin-top:6px"><strong>Expires:</strong> {{ $invitation->expires_at->format('M j, Y') }}</div>
        </div>

        <a href="{{ route('invitations.show', $invitation->token) }}" class="btn">Accept invitation</a>

        <p style="font-size:0.8125rem;color:#999">This invitation link expires in 7 days. If you weren't expecting this, you can ignore it.</p>
    </div>
    <div class="footer">
        If the button doesn't work, copy and paste this link:<br>
        <a href="{{ route('invitations.show', $invitation->token) }}">{{ route('invitations.show', $invitation->token) }}</a>
    </div>
</div>
</body>
</html>
