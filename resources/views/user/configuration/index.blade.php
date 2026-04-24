@extends('layouts.app')
@section('title', 'Configuration')
@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0 font-heading-bold">
        <i class="bi bi-gear-wide-connected me-2 icon-primary"></i>Configuration
    </h4>
</div>

<p style="color:var(--pa-text-muted);max-width:680px;margin-bottom:1.5rem">
    Integrations and advanced tooling for your account. Add new sources of data or connect external services.
</p>

@php
    $cards = [
        [
            'icon'   => 'sliders',
            'title'  => 'General',
            'desc'   => 'Privacy settings — exclude IP addresses from tracking and control city-level geolocation.',
            'url'    => route('user.general'),
            'status' => null,
        ],
        [
            'icon'   => 'people',
            'title'  => 'Account users',
            'desc'   => 'Grant admins and viewers access to your account. Admins manage everything; viewers are read-only.',
            'url'    => route('user.account-users.index'),
            'status' => null,
        ],
        [
            'icon'   => 'cloud-download',
            'title'  => 'Google Analytics Import',
            'desc'   => 'Pull historical pageviews, visitors and top pages from GA4 into Statalog. Keep your past numbers when you switch.',
            'url'    => route('user.ga-import'),
            'status' => null,
        ],
        // Future cards go here — webhooks, Slack, Zapier, API keys, etc.
    ];
@endphp

<div class="row g-3" style="max-width:1000px">
    @foreach($cards as $card)
    <div class="col-md-6 col-lg-4">
        <a href="{{ $card['url'] }}" class="pa-card h-100 d-block text-decoration-none" style="transition:border-color .15s,transform .15s;cursor:pointer"
           onmouseover="this.style.borderColor='var(--pa-primary)';this.style.transform='translateY(-2px)'"
           onmouseout="this.style.borderColor='';this.style.transform=''">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div style="width:2.5rem;height:2.5rem;background:color-mix(in srgb, var(--pa-primary) 12%, transparent);color:var(--pa-primary);border-radius:0.5rem;display:flex;align-items:center;justify-content:center;font-size:1.1rem">
                    <i class="bi bi-{{ $card['icon'] }}"></i>
                </div>
                <h6 class="mb-0 font-heading-bold">{{ $card['title'] }}</h6>
            </div>
            <p style="font-size:0.875rem;color:var(--pa-text-muted);line-height:1.5;margin-bottom:0.75rem">{{ $card['desc'] }}</p>
            <div style="display:flex;align-items:center;justify-content:space-between">
                @if($card['status'])
                    <span style="font-size:0.75rem;color:var(--pa-success);font-weight:600"><i class="bi bi-check-circle-fill me-1"></i>{{ $card['status'] }}</span>
                @else
                    <span></span>
                @endif
                <span style="color:var(--pa-primary);font-size:0.875rem;font-weight:500">Open <i class="bi bi-arrow-right"></i></span>
            </div>
        </a>
    </div>
    @endforeach

    @if(view()->exists('cloud::partials.configuration-cards'))
        @include('cloud::partials.configuration-cards')
    @endif
</div>
@endsection
