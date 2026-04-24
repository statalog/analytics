@extends('layouts.public')
@section('title', 'Protected Dashboard — ' . $site->name)
@section('content')
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:var(--pa-bg);padding:2rem">
    <div style="width:100%;max-width:400px">
        <div style="text-align:center;margin-bottom:2rem">
            <div style="font-family:'Space Grotesk',sans-serif;font-size:1.5rem;font-weight:700;margin-bottom:0.5rem">
                <i class="bi bi-lock-fill me-2 icon-primary"></i>{{ $site->name }}
            </div>
            <p style="color:var(--pa-text-muted);margin:0">This dashboard is password protected.</p>
        </div>

        <div style="background:var(--pa-card-bg);border:1px solid var(--pa-border);border-radius:var(--pa-radius-lg);padding:2rem">
            <form method="POST" action="{{ route('public.dashboard.unlock', $token) }}">
                @csrf
                <div class="mb-3">
                    <label style="font-size:0.875rem;font-weight:600;display:block;margin-bottom:0.375rem">Password</label>
                    <input type="password" name="password" class="pa-input" autofocus required placeholder="Enter password">
                    @error('password')
                    <div style="color:#dc3545;font-size:0.8125rem;margin-top:0.375rem">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn-pa-primary" style="width:100%">
                    <i class="bi bi-unlock me-1"></i> Unlock Dashboard
                </button>
            </form>
        </div>

        <p style="text-align:center;margin-top:1.5rem;font-size:0.8125rem;color:var(--pa-text-muted)">
            Powered by <a href="{{ url('/') }}" style="color:var(--pa-primary);text-decoration:none">Statalog</a>
        </p>
    </div>
</div>
@endsection
