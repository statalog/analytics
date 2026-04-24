@extends('layouts.app')
@section('title', 'Websites')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
        <i class="bi bi-link-45deg me-2" style="color:var(--pa-primary)"></i>Websites
    </h4>
    @include('components.date-range-picker', ['botFilter' => false])
</div>
@include('user.partials.referrer-table', [
    'dataRoute'    => 'user.websites.data',
    'emptyMessage' => 'No referral traffic from other websites for the selected period.',
])
@endsection
