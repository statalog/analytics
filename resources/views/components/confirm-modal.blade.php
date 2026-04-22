{{--
    Reusable confirm modal.

    Attach to any form or link by setting:
        data-pa-confirm="<modal-id>"
    on the element that would normally submit/navigate. A click opens the
    modal; clicking the confirm button replays the original action (submits
    the form or follows the link).

    Usage:
        <x-confirm-modal
            id="delete-site"
            variant="danger"
            icon="trash"
            title="Delete website?"
            body="All site configuration will be removed. Analytics data retained in ClickHouse."
            confirm-label="Delete"
        />

        <form method="POST" action="...">
            @csrf @method('DELETE')
            <button type="submit" data-pa-confirm="delete-site" class="btn-pa-danger">Delete Website</button>
        </form>

    Props:
        id             — unique modal ID (required)
        variant        — danger | primary | warning | success (default: primary)
        icon           — Bootstrap Icons name without "bi-" prefix (default: question-circle)
        title          — heading text
        body           — body text or HTML (use slot for rich markup)
        confirmLabel   — confirm button label (default: Confirm)
        cancelLabel    — cancel button label (default: Cancel)
--}}
@props([
    'id',
    'variant'      => 'primary',
    'icon'         => 'question-circle',
    'title'        => 'Are you sure?',
    'body'         => null,
    'confirmLabel' => 'Confirm',
    'cancelLabel'  => 'Cancel',
])

@php
    $btnClass = match ($variant) {
        'danger'  => 'btn-pa-danger',
        'warning' => 'btn-pa-primary',
        default   => 'btn-pa-primary',
    };
@endphp

<div class="pa-modal-backdrop" data-pa-modal-backdrop="{{ $id }}" aria-hidden="true"></div>
<div class="pa-modal" data-pa-modal="{{ $id }}" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title" aria-hidden="true">
    <div class="pa-modal-dialog">
        <div class="pa-modal-header">
            <div class="pa-modal-icon {{ $variant }}"><i class="bi bi-{{ $icon }}"></i></div>
            <h5 id="{{ $id }}-title" class="pa-modal-title">{{ $title }}</h5>
            <button type="button" class="pa-modal-close" data-pa-modal-close aria-label="Close">&times;</button>
        </div>
        <div class="pa-modal-body">
            {{ $slot->isEmpty() ? $body : $slot }}
        </div>
        <div class="pa-modal-footer">
            <button type="button" class="btn-pa-outline" data-pa-modal-close>{{ $cancelLabel }}</button>
            <button type="button" class="{{ $btnClass }}" data-pa-modal-confirm="{{ $id }}">{{ $confirmLabel }}</button>
        </div>
    </div>
</div>
