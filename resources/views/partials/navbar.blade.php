<nav class="navbar navbar-expand-lg main-navbar">
    <div class="container-xl">
        <a class="navbar-brand d-lg-none fw-bold" href="{{ route('home') }}">
            <i class="bi bi-shield-fill-check text-warning me-1"></i>ChessLiv
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#chesslivNavbar" aria-controls="chesslivNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="chesslivNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="bi bi-house-door me-1"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('articles.index') && !request()->has('category') ? 'active' : '' }}" href="{{ route('articles.index') }}">
                        <i class="bi bi-newspaper me-1"></i>All News
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('category.show') ? 'active' : '' }}" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-grid-3x3-gap me-1"></i>Categories
                    </a>
                    <ul class="dropdown-menu shadow border-0" aria-labelledby="categoriesDropdown">
                        <li><a class="dropdown-item py-2" href="{{ route('category.show', 'tournaments') }}"><i class="bi bi-trophy text-warning me-2"></i>Tournaments & Events</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('category.show', 'world-championship') }}"><i class="bi bi-award text-primary me-2"></i>World Championship</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('category.show', 'gm-insights') }}"><i class="bi bi-lightbulb text-info me-2"></i>Grandmaster Insights</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('category.show', 'opening-theory') }}"><i class="bi bi-book text-success me-2"></i>Opening Theory</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('category.show', 'player-profiles') }}"><i class="bi bi-person-badge text-danger me-2"></i>Player Profiles</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2" href="{{ route('puzzles.index') }}"><i class="bi bi-puzzle text-warning me-2"></i>Tactics & Puzzles</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('tournaments.index') ? 'active' : '' }}" href="{{ route('tournaments.index') }}">
                        <i class="bi bi-trophy me-1"></i>Tournaments
                        <span class="badge-live-pulse ms-1">Live</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('puzzles.index') ? 'active' : '' }}" href="{{ route('puzzles.index') }}">
                        <i class="bi bi-puzzle me-1"></i>Daily Tactics
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">
                        <i class="bi bi-info-circle me-1"></i>About
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2 py-2 py-lg-0">
                <span class="text-muted small d-none d-xl-inline">
                    <i class="bi bi-server text-success me-1"></i>Local Edition :8084
                </span>
                <a href="{{ route('admin.ingestion.index') }}" class="btn btn-sm btn-outline-warning d-none d-sm-inline-flex align-items-center gap-1">
                    <i class="bi bi-rss"></i> Feed Console
                </a>
                <button type="button" class="btn btn-sm btn-outline-secondary d-lg-none" data-bs-toggle="modal" data-bs-target="#searchModal">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
    </div>
</nav>
