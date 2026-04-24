@extends('layouts.app')
@section('title', 'Social Networks')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0 font-heading-bold">
        <i class="bi bi-share me-2 icon-primary"></i>Social Networks
    </h4>
    @include('components.date-range-picker', ['botFilter' => false])
</div>
@include('user.partials.referrer-table', [
    'dataRoute'    => 'user.social-networks.data',
    'emptyMessage' => 'No social network traffic for the selected period.',
])
@endsection
