<header class="site-header">
    {{-- Top Utility Bar --}}
    <div class="top-utility-bar">
        <div class="container-xl">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <span class="d-none d-md-inline text-secondary">
                        <i class="bi bi-clock me-1 text-warning"></i>
                        <span id="liveClock">Loading local time...</span>
                    </span>
                    <span class="badge bg-dark-subtle text-light-emphasis border border-secondary-subtle">
                        <i class="bi bi-broadcast text-danger me-1"></i>Live: Candidates 2026
                    </span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('tournaments.index') }}" class="d-none d-sm-inline">
                        <i class="bi bi-trophy me-1"></i>Tournaments
                    </a>
                    <a href="{{ route('puzzles.index') }}" class="d-none d-sm-inline">
                        <i class="bi bi-puzzle me-1"></i>Daily Puzzle
                    </a>
                    <a href="{{ route('about') }}">
                        <i class="bi bi-info-circle me-1"></i>About
                    </a>
                    <button id="themeToggleBtn" class="btn btn-sm btn-outline-secondary py-0 px-2 text-light" type="button" title="Switch Theme">
                        <i class="bi bi-moon-stars-fill"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Brand Masthead --}}
    <div class="brand-masthead">
        <div class="container-xl">
            <div class="row align-items-center">
                <div class="col-md-7 col-12 text-md-start text-center mb-3 mb-md-0">
                    <a href="{{ route('home') }}" class="text-decoration-none d-inline-flex align-items-center gap-3">
                        <div class="brand-logo-symbol">
                            <i class="bi bi-shield-fill-check"></i>
                        </div>
                        <div>
                            <h1 class="brand-title">CHESS<span>LIV</span></h1>
                            <p class="brand-tagline">The Grandmaster Pulse of Global Chess</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-5 col-12 text-md-end text-center">
                    <div class="d-inline-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-light d-flex align-items-center gap-2 px-3 py-2 rounded-pill" data-bs-toggle="modal" data-bs-target="#searchModal">
                            <i class="bi bi-search text-warning"></i>
                            <span class="d-none d-sm-inline">Search Chess News & Theory...</span>
                            <span class="d-sm-none">Search</span>
                            <kbd class="bg-dark text-muted border border-secondary px-1 py-0 small d-none d-lg-inline">⌘K</kbd>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
