@extends('layouts.admin')

@section('title', 'Import History & Review Failed Imports - ChessLiv Admin')

@section('admin_content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="font-heading fs-3 text-white m-0">
            <i class="bi bi-clock-history text-warning me-2"></i>Import History &amp; Failure Review
        </h2>
        <p class="text-secondary small m-0">
            Audit feed ingestion logs, examine execution durations, track duplicate filtering, and review failure traces.
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.ingestion.index') }}" class="btn btn-warning btn-sm fw-bold">
            <i class="bi bi-arrow-repeat me-1"></i>Run Ingestion
        </a>
    </div>
</div>

{{-- Summary Metric Cards --}}
<div class="row g-3 mb-4">
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="stat-card">
            <span class="text-secondary small text-uppercase fw-semibold">Total Runs</span>
            <div class="stat-num text-white mt-1">{{ number_format($stats['total_runs']) }}</div>
            <div class="text-muted small mt-1">Audit log count</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="stat-card">
            <span class="text-secondary small text-uppercase fw-semibold">Successful</span>
            <div class="stat-num text-success mt-1">{{ number_format($stats['successful_runs']) }}</div>
            <div class="text-muted small mt-1">Zero errors</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="stat-card">
            <span class="text-secondary small text-uppercase fw-semibold">Failed Runs</span>
            <div class="stat-num text-danger mt-1">{{ number_format($stats['failed_runs']) }}</div>
            <div class="text-muted small mt-1">Requires review</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="stat-card">
            <span class="text-secondary small text-uppercase fw-semibold">Partial Success</span>
            <div class="stat-num text-warning mt-1">{{ number_format($stats['partial_runs']) }}</div>
            <div class="text-muted small mt-1">Imported with items skipped</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="stat-card">
            <span class="text-secondary small text-uppercase fw-semibold">Total Imported</span>
            <div class="stat-num text-info mt-1">{{ number_format($stats['total_imported']) }}</div>
            <div class="text-muted small mt-1">Published articles</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="stat-card">
            <span class="text-secondary small text-uppercase fw-semibold">Duplicates Skipped</span>
            <div class="stat-num text-secondary mt-1">{{ number_format($stats['total_skipped']) }}</div>
            <div class="text-muted small mt-1">Deduplicated by SHA-256</div>
        </div>
    </div>
</div>

{{-- Filter Toolbar --}}
<div class="card admin-card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.history.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm bg-dark text-white border-secondary">
                    <option value="all">All Run Statuses</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed (Success)</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed Only</option>
                    <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial Success</option>
                    <option value="running" {{ request('status') === 'running' ? 'selected' : '' }}>Currently Running</option>
                </select>
            </div>
            <div class="col-md-4">
                <select name="source_id" class="form-select form-select-sm bg-dark text-white border-secondary">
                    <option value="">All News Sources</option>
                    @foreach($sources as $src)
                        <option value="{{ $src->id }}" {{ request('source_id') == $src->id ? 'selected' : '' }}>
                            {{ $src->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <div class="form-check form-switch pt-1">
                    <input class="form-check-input" type="checkbox" name="failed_only" value="1" id="failedOnlySwitch" {{ request('failed_only') ? 'checked' : '' }}>
                    <label class="form-check-label small text-secondary" for="failedOnlySwitch">Show Failures &amp; Errors Only</label>
                </div>
            </div>
            <div class="col-md-2 text-md-end">
                <button type="submit" class="btn btn-sm btn-outline-warning w-100">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Import History Table --}}
<div class="card admin-card mb-4 shadow">
    <div class="card-header bg-black bg-opacity-40 border-secondary d-flex justify-content-between align-items-center py-3">
        <h5 class="fs-6 fw-bold text-white m-0">
            <i class="bi bi-table me-2 text-warning"></i>Ingestion Runs Log ({{ $histories->total() }} Total Events)
        </h5>
        @if(request()->anyFilled(['status', 'source_id', 'failed_only']))
            <a href="{{ route('admin.history.index') }}" class="btn btn-sm btn-outline-secondary py-0">Reset Filters</a>
        @endif
    </div>

    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0 small">
            <thead class="table-dark text-secondary text-uppercase" style="font-size: 0.75rem;">
                <tr>
                    <th>#</th>
                    <th>News Source</th>
                    <th>Status</th>
                    <th>Trigger</th>
                    <th>Started At</th>
                    <th>Duration</th>
                    <th class="text-center">Fetched</th>
                    <th class="text-center">Imported</th>
                    <th class="text-center">Skipped</th>
                    <th class="text-center">Errors</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($histories as $h)
                    <tr>
                        <td class="text-muted">{{ $h->id }}</td>
                        <td>
                            <strong class="text-white">{{ $h->source_name }}</strong>
                            @if($h->newsSource)
                                <div class="text-muted" style="font-size: 0.7rem;">{{ Str::limit($h->newsSource->feed_url, 40) }}</div>
                            @endif
                        </td>
                        <td>
                            @if($h->status === 'completed')
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="bi bi-check-circle me-1"></i>Completed
                                </span>
                            @elseif($h->status === 'failed')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                    <i class="bi bi-x-circle me-1"></i>Failed
                                </span>
                            @elseif($h->status === 'partial')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                    <i class="bi bi-exclamation-circle me-1"></i>Partial
                                </span>
                            @else
                                <span class="badge bg-info-subtle text-info border border-info-subtle">
                                    <i class="bi bi-arrow-repeat spin me-1"></i>Running
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary font-monospace">{{ strtoupper($h->trigger_type) }}</span>
                        </td>
                        <td class="font-monospace text-secondary">
                            {{ $h->started_at ? $h->started_at->format('M d, Y H:i:s') : 'N/A' }}
                        </td>
                        <td class="font-monospace text-light">
                            {{ $h->formatted_duration }}
                        </td>
                        <td class="text-center font-monospace">{{ $h->articles_fetched }}</td>
                        <td class="text-center font-monospace text-success fw-bold">{{ $h->articles_imported }}</td>
                        <td class="text-center font-monospace text-secondary">{{ $h->duplicates_skipped }}</td>
                        <td class="text-center">
                            @if($h->errors_count > 0)
                                <span class="badge bg-danger text-white">{{ $h->errors_count }}</span>
                            @else
                                <span class="text-muted">0</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($h->errors_count > 0 || !empty($h->error_messages))
                                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 btn-view-errors" 
                                        data-source="{{ $h->source_name }}"
                                        data-errors='@json($h->error_messages)'>
                                    <i class="bi bi-bug me-1"></i>Review Errors
                                </button>
                            @else
                                <span class="text-muted small">OK</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center py-5 text-secondary">
                            <i class="bi bi-clock-history fs-3 d-block mb-2"></i>
                            No import logs found matching your filter criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($histories->hasPages())
        <div class="card-footer bg-black bg-opacity-25 border-secondary py-3">
            {{ $histories->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

{{-- Error Details Modal --}}
<div class="modal fade" id="errorsModal" tabindex="-1" aria-labelledby="errorsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary shadow-lg">
            <div class="modal-header border-secondary">
                <h5 class="modal-title font-heading fs-6 text-danger" id="errorsModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Failed Import Review: <span id="errorModalSource"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-secondary small mb-2">The following error stack and parsing failure traces were captured during ingestion:</p>
                <div class="p-3 bg-black rounded border border-secondary font-monospace small text-danger" id="errorModalBody" style="max-height: 380px; overflow-y: auto; white-space: pre-wrap;">
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    $('.btn-view-errors').on('click', function() {
        var source = $(this).data('source');
        var errors = $(this).data('errors');
        
        $('#errorModalSource').text(source);
        
        var formatted = '';
        if (Array.isArray(errors)) {
            formatted = errors.map(function(err, idx) {
                return '[' + (idx + 1) + '] ' + err;
            }).join('\n\n');
        } else if (typeof errors === 'object') {
            formatted = JSON.stringify(errors, null, 2);
        } else {
            formatted = errors || 'No detailed error message captured.';
        }
        
        $('#errorModalBody').text(formatted);
        new bootstrap.Modal(document.getElementById('errorsModal')).show();
    });
});
</script>
@endpush
