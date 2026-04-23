@extends('layouts.app')
@section('title', 'Import in progress')
@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('user.ga-import') }}" class="btn-pa-outline" style="padding:0.25rem 0.5rem"><i class="bi bi-arrow-left"></i></a>
    <h4 class="mb-0" style="font-family:'Space Grotesk',sans-serif;font-weight:700">Importing from Google Analytics</h4>
</div>

<div class="pa-card" style="max-width:640px">
    <div id="status-row" class="d-flex align-items-center gap-2 mb-3" style="font-weight:600">
        <span id="status-icon"><div class="spinner-border spinner-border-sm" role="status"></div></span>
        <span id="status-text">{{ ucfirst($import->status) }}</span>
    </div>

    <div style="height:10px;background:var(--pa-input-bg);border-radius:6px;overflow:hidden;margin-bottom:0.5rem">
        <div id="progress-bar" style="height:100%;width:{{ $import->progressPercent() }}%;background:var(--pa-primary);transition:width 0.3s"></div>
    </div>
    <div id="progress-label" style="font-size:0.8125rem;color:var(--pa-text-muted)">
        {{ $import->processed_days }} / {{ $import->total_days }} days processed
    </div>

    <div id="error-box" style="margin-top:1rem;padding:0.75rem 1rem;background:color-mix(in srgb, var(--pa-danger) 10%, transparent);color:var(--pa-danger);border-radius:var(--pa-radius);display:none;font-size:0.875rem"></div>

    <div id="done-actions" class="d-flex gap-2 mt-3" style="display:none !important">
        <a href="{{ route('user.ga-import.summary', $import->site) }}" class="btn-pa-primary">
            <i class="bi bi-bar-chart me-1"></i>View imported data
        </a>
        <a href="{{ route('user.ga-import') }}" class="btn-pa-outline">Back to imports</a>
    </div>
</div>

@push('scripts')
<script>
const importId = {{ $import->id }};
const dataUrl  = "{{ route('user.ga-import.progress.data', $import) }}";

function refresh() {
    fetch(dataUrl, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(d => {
            document.getElementById('progress-bar').style.width = d.percent + '%';
            document.getElementById('progress-label').textContent = d.processed_days + ' / ' + d.total_days + ' days processed';

            let label = d.status.charAt(0).toUpperCase() + d.status.slice(1);
            if (d.status === 'running') label = 'Importing — ' + d.percent + '%';
            document.getElementById('status-text').textContent = label;

            if (d.status === 'completed') {
                document.getElementById('status-icon').innerHTML = '<i class="bi bi-check-circle-fill" style="color:var(--pa-success);font-size:1.25rem"></i>';
                document.getElementById('status-text').textContent = 'Completed';
                document.getElementById('done-actions').style.display = 'flex';
                document.getElementById('progress-bar').style.background = 'var(--pa-success)';
                return;
            }
            if (d.status === 'failed') {
                document.getElementById('status-icon').innerHTML = '<i class="bi bi-exclamation-triangle-fill" style="color:var(--pa-danger);font-size:1.25rem"></i>';
                document.getElementById('status-text').textContent = 'Failed';
                document.getElementById('progress-bar').style.background = 'var(--pa-danger)';
                if (d.error) {
                    const err = document.getElementById('error-box');
                    err.textContent = d.error;
                    err.style.display = 'block';
                }
                return;
            }

            setTimeout(refresh, 2000);
        })
        .catch(() => setTimeout(refresh, 5000));
}

document.addEventListener('DOMContentLoaded', refresh);
</script>
@endpush
@endsection
