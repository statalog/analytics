@extends('layouts.app')
@section('title', 'Search Engines')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">
        <i class="bi bi-search me-2" style="color:var(--pa-primary)"></i>Search Engines
    </h4>
    @include('components.date-range-picker')
</div>
@include('user.partials.referrer-table', [
    'dataRoute'    => 'user.search-engines.data',
    'emptyMessage' => 'No search engine traffic for the selected period.',
])

@if(view()->exists('cloud::gsc.keywords-card'))
    @include('cloud::gsc.keywords-card')
@endif
@endsection
