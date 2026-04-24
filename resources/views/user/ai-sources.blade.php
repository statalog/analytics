@extends('layouts.app')
@section('title', 'AI Assistants')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h4 class="mb-0 font-heading-bold">
        <i class="bi bi-robot me-2 icon-primary"></i>AI Assistants
    </h4>
    @include('components.date-range-picker', ['botFilter' => false])
</div>
@include('user.partials.referrer-table', [
    'dataRoute'    => 'user.ai-sources.data',
    'emptyMessage' => 'No traffic from AI assistants for the selected period.',
])
@endsection
