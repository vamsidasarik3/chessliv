@extends('layouts.admin')

@section('title', 'System Health & Reliability Status - ChessLiv Admin')

@section('admin_content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="font-heading fs-3 text-white m-0">
            <i class="bi bi-heart-pulse-fill text-warning me-2"></i>System Health &amp; Reliability Status
        </h2>
        <p class="text-secondary small m-0">
            Real-time diagnostics for MySQL connectivity, feed syndication cadence, background queue integrity, and local assets.
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-warning btn-sm" onclick="window.location.reload();">
            <i class="bi bi-arrow-clockwise me-1"></i>Refresh Diagnostics
        </button>
    </div>
</div>

{{-- Overall Health Banner --}}
<div class="alert {{ $overallHealthy ? 'alert-success bg-success bg-opacity-10 border-success' : 'alert-danger bg-danger bg-opacity-10 border-danger' }} p-4 mb-4 shadow-sm">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="fs-1 {{ $overallHealthy ? 'text-success' : 'text-danger' }}">
                <i class="bi {{ $overallHealthy ? 'bi-shield-fill-check' : 'bi-exclamation-octagon-fill' }}"></i>
            </div>
            <div>
                <h4 class="font-heading fs-5 mb-1 {{ $overallHealthy ? 'text-success' : 'text-danger' }}">
                    {{ $overallHealthy ? 'All Systems Operational' : 'Action Required on System Components' }}
                </h4>
                <p class="small text-secondary mb-0">
                    Database connection is active ({{ $dbStatus['latency_ms'] }}ms latency), {{ $sourcesSummary['enabled'] }} of {{ $sourcesSummary['total'] }} news feeds enabled, and {{ $queueStatus['failed_jobs_count'] }} failed queue jobs.
                </p>
            </div>
        </div>
        <div>
            <span class="badge {{ $overallHealthy ? 'bg-success' : 'bg-danger' }} fs-6 px-3 py-2 font-monospace">
                {{ $overallHealthy ? 'SYSTEM HEALTH: 100%' : 'STATUS: DEGRADED' }}
            </span>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- 1. Database Connectivity Card --}}
    <div class="col-lg-6">
        <div class="card admin-card h-100 shadow">
            <div class="card-header bg-black bg-opacity-40 border-secondary py-3 d-flex justify-content-between align-items-center">
                <h5 class="fs-6 fw-bold text-white m-0">
                    <i class="bi bi-database-check text-warning me-2"></i>Database Connectivity
                </h5>
                <span class="badge {{ $dbStatus['connected'] ? 'bg-success' : 'bg-danger' }}">
                    {{ $dbStatus['connected'] ? 'CONNECTED' : 'DISCONNECTED' }}
                </span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-sm-4 text-center p-3 bg-black rounded border border-secondary border-opacity-50">
                        <span class="text-secondary small d-block">Latency</span>
                        <strong class="fs-4 text-success font-monospace">{{ $dbStatus['latency_ms'] ?? 'N/A' }} ms</strong>
                    </div>
                    <div class="col-sm-4 text-center p-3 bg-black rounded border border-secondary border-opacity-50">
                        <span class="text-secondary small d-block">Driver / DB</span>
                        <strong class="fs-6 text-white font-monospace">{{ strtoupper($dbStatus['driver']) }} • {{ $dbStatus['database'] }}</strong>
                    </div>
                    <div class="col-sm-4 text-center p-3 bg-black rounded border border-secondary border-opacity-50">
                        <span class="text-secondary small d-block">Server Version</span>
                        <strong class="fs-6 text-white font-monospace">{{ Str::limit($dbStatus['version'], 15) }}</strong>
                    </div>
                </div>

                <h6 class="text-secondary small text-uppercase fw-bold mb-2">Table Row Counts</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-dark table-borderless mb-0 small">
                        <tbody>
                            <tr>
                                <td class="text-secondary">News Articles</td>
                                <td class="text-end font-monospace fw-bold text-white">{{ number_format($dbStatus['counts']['articles']) }}</td>
                            </tr>
                            <tr>
                                <td class="text-secondary">Interactive Chess Games</td>
                                <td class="text-end font-monospace fw-bold text-warning">{{ number_format($dbStatus['counts']['chess_games']) }}</td>
                            </tr>
                            <tr>
                                <td class="text-secondary">Editorial Categories</td>
                                <td class="text-end font-monospace fw-bold text-white">{{ number_format($dbStatus['counts']['categories']) }}</td>
                            </tr>
                            <tr>
                                <td class="text-secondary">News Sources</td>
                                <td class="text-end font-monospace fw-bold text-white">{{ number_format($dbStatus['counts']['news_sources']) }}</td>
                            </tr>
                            <tr>
                                <td class="text-secondary">Import History Logs</td>
                                <td class="text-end font-monospace fw-bold text-white">{{ number_format($dbStatus['counts']['import_histories']) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Last Successful Import & Sync Cadence --}}
    <div class="col-lg-6">
        <div class="card admin-card h-100 shadow">
            <div class="card-header bg-black bg-opacity-40 border-secondary py-3 d-flex justify-content-between align-items-center">
                <h5 class="fs-6 fw-bold text-white m-0">
                    <i class="bi bi-clock-history text-warning me-2"></i>Last Successful Import
                </h5>
                <a href="{{ route('admin.history.index') }}" class="btn btn-sm btn-outline-secondary py-0">View All Runs</a>
            </div>
            <div class="card-body p-4">
                @if($lastSuccessfulImport)
                    <div class="p-3 bg-black rounded border border-success border-opacity-50 mb-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle mb-1">Last Clean Run</span>
                                <h6 class="text-white fw-bold m-0">{{ $lastSuccessfulImport->source_name }}</h6>
                            </div>
                            <span class="font-monospace text-secondary small">
                                {{ $lastSuccessfulImport->completed_at ? $lastSuccessfulImport->completed_at->diffForHumans() : 'Just now' }}
                            </span>
                        </div>
                        <div class="row g-2 small text-secondary">
                            <div class="col-sm-4">
                                <span class="text-muted d-block">Imported:</span>
                                <strong class="text-success font-monospace">{{ $lastSuccessfulImport->articles_imported }} articles</strong>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-muted d-block">Deduplicated:</span>
                                <strong class="text-secondary font-monospace">{{ $lastSuccessfulImport->duplicates_skipped }} skipped</strong>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-muted d-block">Duration:</span>
                                <strong class="text-white font-monospace">{{ $lastSuccessfulImport->formatted_duration }}</strong>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-secondary small mb-3">
                        <i class="bi bi-info-circle me-1"></i>No successful import recorded yet. Run a manual refresh from the Feeds console.
                    </div>
                @endif

                @if($lastAnyImport && $lastAnyImport->status === 'failed')
                    <div class="p-3 bg-black rounded border border-danger border-opacity-50">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-danger">Most Recent Run Failed</span>
                            <span class="text-muted small">{{ $lastAnyImport->started_at->diffForHumans() }}</span>
                        </div>
                        <div class="text-white fw-bold small">{{ $lastAnyImport->source_name }}</div>
                        <p class="text-danger small mb-0 mt-1 font-monospace">
                            {{ Str::limit(is_array($lastAnyImport->error_messages) ? implode('; ', $lastAnyImport->error_messages) : $lastAnyImport->error_messages, 120) }}
                        </p>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top border-secondary small">
                    <span class="text-secondary">Configured HTTP Request Timeout:</span>
                    <strong class="text-warning font-monospace">{{ $runtime['http_timeout'] }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-1 small">
                    <span class="text-secondary">Configured HTTP Retry Limit:</span>
                    <strong class="text-warning font-monospace">{{ $runtime['http_retries'] }} attempts</strong>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 3. News Sources Reliability Table --}}
<div class="card admin-card mb-4 shadow">
    <div class="card-header bg-black bg-opacity-40 border-secondary py-3 d-flex justify-content-between align-items-center">
        <h5 class="fs-6 fw-bold text-white m-0">
            <i class="bi bi-broadcast text-warning me-2"></i>News Sources Health &amp; Cadence ({{ $sourcesSummary['enabled'] }} Active / {{ $sourcesSummary['total'] }} Total)
        </h5>
        <a href="{{ route('admin.ingestion.index') }}" class="btn btn-sm btn-outline-warning py-0">Manage Feeds</a>
    </div>
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0 small">
            <thead class="table-dark text-secondary text-uppercase" style="font-size: 0.75rem;">
                <tr>
                    <th>Source Name</th>
                    <th>Feed URL</th>
                    <th>Frequency</th>
                    <th>Last Ingestion</th>
                    <th class="text-center">Articles</th>
                    <th class="text-center">State</th>
                    <th>Error Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sources as $s)
                    <tr>
                        <td>
                            <strong class="text-white">{{ $s->name }}</strong>
                            <div class="text-muted" style="font-size: 0.7rem;">{{ $s->slug }}</div>
                        </td>
                        <td class="font-monospace text-secondary">
                            <a href="{{ $s->feed_url }}" target="_blank" class="text-decoration-none text-secondary">
                                {{ Str::limit($s->feed_url, 45) }}
                                <i class="bi bi-box-arrow-up-right ms-1" style="font-size: 0.65rem;"></i>
                            </a>
                        </td>
                        <td>Every {{ $s->fetch_frequency_minutes }} mins</td>
                        <td class="font-monospace text-secondary">
                            {{ $s->last_fetched_at ? $s->last_fetched_at->diffForHumans() : 'Never' }}
                        </td>
                        <td class="text-center font-monospace">{{ $s->articles_count }}</td>
                        <td class="text-center">
                            @if($s->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Enabled</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Disabled</span>
                            @endif
                        </td>
                        <td>
                            @if($s->last_error)
                                <span class="badge bg-danger-subtle text-danger" title="{{ $s->last_error }}">
                                    <i class="bi bi-exclamation-triangle me-1"></i>Error: {{ Str::limit($s->last_error, 35) }}
                                </span>
                            @else
                                <span class="text-success small"><i class="bi bi-check2 text-success me-1"></i>Clean</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="row g-4">
    {{-- 4. Queue & Failed Jobs Card --}}
    <div class="col-lg-6">
        <div class="card admin-card h-100 shadow">
            <div class="card-header bg-black bg-opacity-40 border-secondary py-3 d-flex justify-content-between align-items-center">
                <h5 class="fs-6 fw-bold text-white m-0">
                    <i class="bi bi-cpu text-warning me-2"></i>Background Queue &amp; Failed Jobs
                </h5>
                <span class="badge {{ $queueStatus['failed_jobs_count'] === 0 ? 'bg-success' : 'bg-danger' }}">
                    {{ $queueStatus['failed_jobs_count'] }} FAILED JOBS
                </span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-6 p-3 bg-black rounded border border-secondary border-opacity-50 text-center">
                        <span class="text-secondary small d-block">Failed Jobs</span>
                        <strong class="fs-4 {{ $queueStatus['failed_jobs_count'] === 0 ? 'text-success' : 'text-danger' }} font-monospace">
                            {{ $queueStatus['failed_jobs_count'] }}
                        </strong>
                    </div>
                    <div class="col-6 p-3 bg-black rounded border border-secondary border-opacity-50 text-center">
                        <span class="text-secondary small d-block">Pending Queue Jobs</span>
                        <strong class="fs-4 text-white font-monospace">
                            {{ $queueStatus['pending_jobs_count'] }}
                        </strong>
                    </div>
                </div>

                @if($queueStatus['recent_failed']->isNotEmpty())
                    <h6 class="text-danger small fw-bold mb-2"><i class="bi bi-exclamation-circle me-1"></i>Recent Failed Job Traces:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-dark small mb-0">
                            <thead>
                                <tr>
                                    <th>Connection</th>
                                    <th>Queue</th>
                                    <th>Failed At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($queueStatus['recent_failed'] as $fj)
                                    <tr>
                                        <td>{{ $fj->connection }}</td>
                                        <td>{{ $fj->queue }}</td>
                                        <td class="font-monospace text-muted">{{ $fj->failed_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-success small mb-0"><i class="bi bi-check-circle me-1"></i>No queue worker job failures recorded.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- 5. Local Assets & Runtime Health Card --}}
    <div class="col-lg-6">
        <div class="card admin-card h-100 shadow">
            <div class="card-header bg-black bg-opacity-40 border-secondary py-3">
                <h5 class="fs-6 fw-bold text-white m-0">
                    <i class="bi bi-file-earmark-check text-warning me-2"></i>Local Assets &amp; Runtime Environment
                </h5>
            </div>
            <div class="card-body p-4">
                <h6 class="text-secondary small text-uppercase fw-bold mb-2">Chess Library Local Assets (No NPM/Vite)</h6>
                <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-secondary border-opacity-25 small">
                    <span class="text-light">chess.js (v0.10.3)</span>
                    <span class="badge {{ $assetChecks['chess_js'] ? 'bg-success' : 'bg-danger' }}">
                        {{ $assetChecks['chess_js'] ? 'LOCALLY HOSTED' : 'MISSING' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-secondary border-opacity-25 small">
                    <span class="text-light">chessboard.js (v1.0.0)</span>
                    <span class="badge {{ $assetChecks['chessboard_js'] ? 'bg-success' : 'bg-danger' }}">
                        {{ $assetChecks['chessboard_js'] ? 'LOCALLY HOSTED' : 'MISSING' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-secondary border-opacity-25 small">
                    <span class="text-light">chessboard.css (v1.0.0)</span>
                    <span class="badge {{ $assetChecks['chessboard_css'] ? 'bg-success' : 'bg-danger' }}">
                        {{ $assetChecks['chessboard_css'] ? 'LOCALLY HOSTED' : 'MISSING' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-secondary border-opacity-25 small">
                    <span class="text-light">Wikipedia Pieces (12 Pieces PNG)</span>
                    <span class="badge {{ $assetChecks['pieces_dir'] ? 'bg-success' : 'bg-danger' }}">
                        {{ $assetChecks['pieces_dir'] ? 'LOCALLY HOSTED' : 'MISSING' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-1 mb-3 small">
                    <span class="text-light">SVG Image Fallback Placeholder</span>
                    <span class="badge {{ $assetChecks['placeholder_img'] ? 'bg-success' : 'bg-danger' }}">
                        {{ $assetChecks['placeholder_img'] ? 'PRESENT' : 'MISSING' }}
                    </span>
                </div>

                <h6 class="text-secondary small text-uppercase fw-bold mb-2">PHP &amp; Framework Environment</h6>
                <div class="row g-2 small text-secondary">
                    <div class="col-6">PHP Version: <strong class="text-white">{{ $runtime['php_version'] }}</strong></div>
                    <div class="col-6">Laravel: <strong class="text-white">{{ $runtime['laravel_version'] }}</strong></div>
                    <div class="col-6">Environment: <strong class="text-white">{{ $runtime['environment'] }}</strong></div>
                    <div class="col-6">Memory Usage: <strong class="text-white font-monospace">{{ $runtime['memory_usage_mb'] }} MB</strong></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
