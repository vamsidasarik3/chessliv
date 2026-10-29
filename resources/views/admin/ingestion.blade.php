<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>News Ingestion Console - ChessLiv Administration</title>

    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chessliv.css') }}">
    <style>
        .admin-nav {
            background: #090d16;
            border-bottom: 2px solid var(--chess-accent);
        }
        .stat-card {
            background: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 10px;
            padding: 20px;
            transition: var(--transition-fast);
        }
        .stat-card:hover {
            border-color: #d97706;
            transform: translateY(-2px);
        }
        .table-admin {
            --bs-table-bg: transparent;
            font-size: 0.9rem;
        }
        .hash-code {
            font-family: monospace;
            font-size: 0.78rem;
            color: #94a3b8;
        }
    </style>
</head>
<body class="bg-dark text-light">

    {{-- Admin Top Navigation --}}
    <nav class="navbar navbar-expand-xl admin-nav py-2 sticky-top shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand text-white fw-bold d-flex align-items-center gap-2" href="{{ route('admin.ingestion.index') }}">
                <span class="fs-4 text-warning"><i class="bi bi-shield-check"></i></span>
                <span class="font-heading fs-4">CHESS<span class="text-warning">LIV</span></span>
                <span class="badge bg-warning text-dark ms-1 small fw-bold">ADMIN</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminIngestionNav" aria-controls="adminIngestionNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminIngestionNav">
                <ul class="navbar-nav me-auto mb-2 mb-xl-0 gap-1 ms-3">
                    <li class="nav-item">
                        <a class="nav-link px-3 py-1 rounded text-dark bg-warning fw-bold" href="{{ route('admin.ingestion.index') }}">
                            <i class="bi bi-rss me-1"></i>Feeds &amp; Ingestion
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-1 rounded text-light" href="{{ route('admin.history.index') }}">
                            <i class="bi bi-clock-history me-1"></i>Import History &amp; Failures
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-1 rounded text-light" href="{{ route('admin.categories.index') }}">
                            <i class="bi bi-folder2-open me-1"></i>Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-1 rounded text-light" href="{{ route('admin.games.index') }}">
                            <i class="bi bi-controller me-1"></i>Games Metadata
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-1 rounded text-light" href="{{ route('admin.health.index') }}">
                            <i class="bi bi-heart-pulse me-1"></i>System Health
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3 ms-auto">
                    <a href="{{ route('home') }}" class="btn btn-sm btn-outline-light" target="_blank">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Public Site
                    </a>
                    <div class="d-none d-md-block text-end">
                        <div class="small fw-bold text-white">{{ Auth::user()->name }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">{{ Auth::user()->email }}</div>
                    </div>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Logout">
                            <i class="bi bi-power"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Container --}}
    <div class="container-fluid px-4 py-4">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Real-time AJAX feedback alert placeholder --}}
        <div id="ajaxAlert" class="alert d-none" role="alert"></div>

        {{-- Header Bar --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h1 class="font-heading fs-3 fw-bold mb-1">
                    <i class="bi bi-rss-fill text-warning me-2"></i>Chess News Ingestion &amp; Syndication
                </h1>
                <p class="text-muted mb-0 small">
                    Manage official RSS/Atom feeds, trigger manual updates, and monitor canonical deduplication.
                </p>
            </div>
            <div class="d-flex gap-2 mt-3 mt-md-0">
                <button type="button" id="btnRefreshAll" class="btn btn-warning fw-bold px-3 py-2">
                    <i class="bi bi-arrow-clockwise me-1"></i>Refresh All Active Feeds
                </button>
                <button type="button" class="btn btn-outline-secondary px-3 py-2" data-bs-toggle="modal" data-bs-target="#addSourceModal">
                    <i class="bi bi-plus-circle me-1"></i>Add News Source
                </button>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-semibold">Feed Sources</div>
                            <div class="fs-3 fw-bold text-white mt-1">{{ $stats['active_sources'] }} <span class="fs-6 text-muted">/ {{ $stats['total_sources'] }} Active</span></div>
                        </div>
                        <div class="fs-2 text-primary"><i class="bi bi-broadcast-pin"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-semibold">Total Articles</div>
                            <div class="fs-3 fw-bold text-white mt-1">{{ number_format($stats['total_articles']) }}</div>
                        </div>
                        <div class="fs-2 text-success"><i class="bi bi-newspaper"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-semibold">Syndicated via RSS</div>
                            <div class="fs-3 fw-bold text-warning mt-1">{{ number_format($stats['imported_articles']) }}</div>
                        </div>
                        <div class="fs-2 text-warning"><i class="bi bi-cloud-arrow-down"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-semibold">Latest Feed Sync</div>
                            <div class="fs-5 fw-bold text-white mt-1">
                                {{ $stats['latest_fetch'] ? \Carbon\Carbon::parse($stats['latest_fetch'])->diffForHumans() : 'Not yet run' }}
                            </div>
                        </div>
                        <div class="fs-2 text-info"><i class="bi bi-clock-history"></i></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Configured Sources Table Card --}}
        <div class="card bg-dark border-secondary mb-4 shadow-sm">
            <div class="card-header bg-black bg-opacity-40 border-secondary d-flex justify-content-between align-items-center py-3">
                <h5 class="m-0 fs-6 fw-bold text-white">
                    <i class="bi bi-hdd-network text-warning me-2"></i>Configured Public News Feeds
                </h5>
                <span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $sources->count() }} Registered</span>
            </div>
            <div class="table-responsive">
                <table class="table table-admin table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Source Name</th>
                            <th>Feed Endpoint</th>
                            <th>Default Section</th>
                            <th>Status</th>
                            <th>Last Sync</th>
                            <th>Imported</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sources as $src)
                            <tr>
                                <td>
                                    <div class="fw-bold text-white">{{ $src->name }}</div>
                                    @if($src->site_url)
                                        <a href="{{ $src->site_url }}" class="small text-muted text-decoration-none" target="_blank" rel="noopener noreferrer">
                                            <i class="bi bi-box-arrow-up-right me-1"></i>{{ parse_url($src->site_url, PHP_URL_HOST) }}
                                        </a>
                                    @endif
                                    @if($src->last_error)
                                        <div class="text-danger small mt-1" title="{{ $src->last_error }}">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ Str::limit($src->last_error, 50) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ $src->feed_url }}" target="_blank" rel="noopener noreferrer" class="small text-warning text-decoration-none font-monospace">
                                        <i class="bi bi-rss me-1"></i>{{ Str::limit($src->feed_url, 40) }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $src->defaultCategory->name ?? 'General' }}</span>
                                </td>
                                <td>
                                    @if($src->is_active)
                                        <span class="badge bg-success-subtle text-success-emphasis">Active</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $src->last_fetched_at ? $src->last_fetched_at->diffForHumans() : 'Never' }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-dark border border-secondary text-light">{{ $src->items_fetched_count }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-warning btn-refresh-single" data-id="{{ $src->id }}" data-name="{{ $src->name }}" title="Fetch Now">
                                            <i class="bi bi-arrow-clockwise"></i> Fetch
                                        </button>
                                        <form action="{{ route('admin.ingestion.sources.toggle', $src->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-secondary" title="{{ $src->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="bi {{ $src->is_active ? 'bi-pause-fill' : 'bi-play-fill' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.ingestion.sources.destroy', $src->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove source {{ $src->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No news sources configured.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Ingestion Stream & Deduplication Audit Table --}}
        <div class="card bg-dark border-secondary shadow-sm">
            <div class="card-header bg-black bg-opacity-40 border-secondary d-flex justify-content-between align-items-center py-3">
                <h5 class="m-0 fs-6 fw-bold text-white">
                    <i class="bi bi-clock-history text-warning me-2"></i>Recent Ingested Articles &amp; Canonical Deduplication Audit
                </h5>
                <span class="badge bg-secondary-subtle text-secondary-emphasis">Last {{ $recentArticles->count() }} Entries</span>
            </div>
            <div class="table-responsive">
                <table class="table table-admin table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Article Title</th>
                            <th>Attributed Source</th>
                            <th>Canonical URL</th>
                            <th>Content Hash (SHA-256)</th>
                            <th>Published Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentArticles as $art)
                            <tr>
                                <td style="max-width: 320px;">
                                    <a href="{{ route('articles.show', $art->slug) }}" class="fw-semibold text-white text-decoration-none" target="_blank">
                                        {{ Str::limit($art->title, 65) }}
                                    </a>
                                </td>
                                <td>
                                    @if($art->newsSource)
                                        <span class="badge bg-warning text-dark">{{ $art->newsSource->name }}</span>
                                    @elseif($art->source_name)
                                        <span class="badge bg-secondary">{{ $art->source_name }}</span>
                                    @else
                                        <span class="badge bg-dark-subtle text-muted">ChessLiv Editorial</span>
                                    @endif
                                </td>
                                <td style="max-width: 250px;">
                                    @if($art->canonical_url)
                                        <a href="{{ $art->original_url ?: $art->canonical_url }}" class="small text-truncate d-block text-secondary text-decoration-none" target="_blank" rel="noopener noreferrer" title="{{ $art->canonical_url }}">
                                            <i class="bi bi-link-45deg me-1"></i>{{ Str::limit($art->canonical_url, 35) }}
                                        </a>
                                    @else
                                        <span class="text-muted small">Local / Editorial</span>
                                    @endif
                                </td>
                                <td>
                                    @if($art->content_hash)
                                        <span class="hash-code" title="{{ $art->content_hash }}">
                                            {{ substr($art->content_hash, 0, 12) }}...
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $art->published_at ? $art->published_at->format('Y-m-d H:i') : 'N/A' }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success-emphasis text-uppercase small">
                                        {{ $art->processing_status ?: 'processed' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No articles found in archive.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add Source Modal --}}
    <div class="modal fade" id="addSourceModal" tabindex="-1" aria-labelledby="addSourceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark border-secondary text-white">
                <form action="{{ route('admin.ingestion.sources.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title font-heading fs-5" id="addSourceModalLabel">
                            <i class="bi bi-plus-circle text-warning me-2"></i>Add Public News Source
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Source Name *</label>
                            <input type="text" name="name" class="form-control bg-black text-white border-secondary" placeholder="e.g. FIDE Press Releases" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Feed URL (RSS/Atom) *</label>
                            <input type="url" name="feed_url" class="form-control bg-black text-white border-secondary" placeholder="https://example.com/rss.xml" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Official Site URL</label>
                            <input type="url" name="site_url" class="form-control bg-black text-white border-secondary" placeholder="https://example.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Default Category</label>
                            <select name="default_category_id" class="form-select bg-black text-white border-secondary">
                                <option value="">-- Auto-detect from keywords --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Language</label>
                                <input type="text" name="language" class="form-control bg-black text-white border-secondary" value="en">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Fetch Interval (Mins)</label>
                                <input type="number" name="fetch_frequency_minutes" class="form-control bg-black text-white border-secondary" value="60" min="15">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Description / Notes</label>
                            <textarea name="description" rows="2" class="form-control bg-black text-white border-secondary" placeholder="Official terms, copyright or attribution notes"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning fw-bold">Save News Source</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Vendor Scripts -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>

    <!-- Admin Ingestion AJAX Script -->
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // 1. Refresh All Active Feeds
            $('#btnRefreshAll').on('click', function () {
                const $btn = $(this);
                const originalHtml = $btn.html();

                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Ingesting Feeds...');
                $('#ajaxAlert').addClass('d-none').removeClass('alert-success alert-danger');

                $.ajax({
                    url: '{{ route("admin.ingestion.refresh") }}',
                    method: 'POST',
                    data: {},
                    dataType: 'json',
                    success: function (res) {
                        $btn.prop('disabled', false).html(originalHtml);
                        $('#ajaxAlert').removeClass('d-none alert-danger').addClass('alert-success')
                            .html('<i class="bi bi-check-circle-fill me-2"></i>' + res.message);
                        
                        setTimeout(function () {
                            window.location.reload();
                        }, 1500);
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html(originalHtml);
                        let err = 'An error occurred during ingestion.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            err = xhr.responseJSON.message;
                        }
                        $('#ajaxAlert').removeClass('d-none alert-success').addClass('alert-danger')
                            .html('<i class="bi bi-exclamation-triangle-fill me-2"></i>' + err);
                    }
                });
            });

            // 2. Refresh Single Source
            $('.btn-refresh-single').on('click', function () {
                const $btn = $(this);
                const sourceId = $btn.data('id');
                const sourceName = $btn.data('name');
                const originalHtml = $btn.html();

                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
                $('#ajaxAlert').addClass('d-none');

                $.ajax({
                    url: '{{ route("admin.ingestion.refresh") }}',
                    method: 'POST',
                    data: { source_id: sourceId },
                    dataType: 'json',
                    success: function (res) {
                        $btn.prop('disabled', false).html(originalHtml);
                        $('#ajaxAlert').removeClass('d-none alert-danger').addClass('alert-success')
                            .html('<i class="bi bi-check-circle-fill me-2"></i>' + res.message);
                        
                        setTimeout(function () {
                            window.location.reload();
                        }, 1500);
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html(originalHtml);
                        let err = 'Failed to refresh ' + sourceName;
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            err = xhr.responseJSON.message;
                        }
                        $('#ajaxAlert').removeClass('d-none alert-success').addClass('alert-danger')
                            .html('<i class="bi bi-exclamation-triangle-fill me-2"></i>' + err);
                    }
                });
            });
        });
    </script>
</body>
</html>
