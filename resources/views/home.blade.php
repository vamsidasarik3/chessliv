@extends('layouts.app')

@section('title', 'ChessLiv - Breaking Chess News, Grandmaster Analysis & Tournaments')
@section('meta_description', 'Latest chess news, live FIDE Candidates coverage, Magnus Carlsen updates, tactical puzzles, opening insights, and grandmaster game analyses.')

@section('content')
<div class="container-xl">

    {{-- Database Error Handling Notification --}}
    @if(!empty($dbError))
        <div class="alert alert-warning alert-dismissible fade show mb-4 d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
            <div>
                <strong>Notice:</strong> {{ $dbError }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- TOP SEARCH & QUICK PLAYER FILTER BAR --}}
    <div class="card bg-body-tertiary border-secondary-subtle p-3 mb-4 shadow-sm">
        <form action="{{ route('home') }}" method="GET" class="row g-2 align-items-center">
            {{-- Search Input --}}
            <div class="col-md-5 col-12">
                <div class="input-group">
                    <span class="input-group-text bg-dark-subtle border-secondary-subtle text-warning">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="q" class="form-control" placeholder="Search headline, player, grandmaster, or event..." value="{{ request('q') }}">
                    @if(request('q'))
                        <a href="{{ route('home', array_filter(request()->except('q'))) }}" class="btn btn-outline-secondary" title="Clear search">
                            <i class="bi bi-x"></i>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Category Filter Select --}}
            <div class="col-md-3 col-6">
                <select name="category" class="form-select" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ $cat->articles_count }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Player Filter Select --}}
            <div class="col-md-3 col-6">
                <select name="player" class="form-select" onchange="this.form.submit()">
                    <option value="">Filter by Player</option>
                    @foreach($popularPlayers as $p)
                        <option value="{{ $p }}" {{ request('player') === $p ? 'selected' : '' }}>
                            {{ $p }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-1 col-12 d-grid">
                <button type="submit" class="btn btn-warning fw-bold">Filter</button>
            </div>
        </form>

        {{-- Popular Player Quick-Filter Pills --}}
        <div class="d-flex flex-wrap align-items-center gap-1 mt-3 pt-2 border-top border-secondary-subtle small">
            <span class="text-muted fw-bold me-1"><i class="bi bi-person-badge text-warning me-1"></i>Top Players:</span>
            @foreach($popularPlayers as $player)
                <a href="{{ route('home', array_merge(request()->except('player', 'page'), request('player') === $player ? [] : ['player' => $player])) }}"
                   class="badge text-decoration-none px-2 py-1 {{ request('player') === $player ? 'bg-warning text-dark fw-bold' : 'bg-secondary-subtle text-secondary-emphasis' }}">
                    {{ $player }}
                    @if(request('player') === $player)
                        <i class="bi bi-x ms-1"></i>
                    @endif
                </a>
            @endforeach
            @if(request()->filled('player') || request()->filled('category') || request()->filled('q'))
                <a href="{{ route('home') }}" class="badge bg-danger-subtle text-danger-emphasis text-decoration-none px-2 py-1 ms-auto">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset All Filters
                </a>
            @endif
        </div>
    </div>

    {{-- HERO SECTION: Only shown when no active search/player filter is set --}}
    @if(!request()->filled('q') && !request()->filled('player') && !request()->filled('category') && $heroArticle)
    <div class="row g-4 mb-5">
        {{-- Main Hero Feature --}}
        <div class="col-lg-8">
            <div class="hero-card">
                <img src="{{ $heroArticle->image_url ?: asset('images/chess-placeholder.svg') }}" 
                     alt="{{ $heroArticle->title }}" 
                     class="hero-img"
                     onerror="this.onerror=null; this.src='{{ asset('images/chess-placeholder.svg') }}';">
                <div class="hero-overlay">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="badge bg-warning text-dark fw-bold px-2 py-1">
                            <i class="bi bi-star-fill me-1"></i>Featured Lead
                        </span>
                        @if($heroArticle->is_breaking)
                            <span class="badge bg-danger text-white px-2 py-1">
                                <i class="bi bi-lightning-fill me-1"></i>Breaking
                            </span>
                        @endif
                        <span class="badge bg-dark-subtle text-light border border-secondary-subtle">
                            {{ $heroArticle->category->name ?? 'General' }}
                        </span>
                        <span class="badge bg-secondary-subtle text-secondary-emphasis">
                            <i class="bi bi-broadcast me-1"></i>{{ $heroArticle->source_name ?: 'ChessLiv' }}
                        </span>
                    </div>
                    <h2 class="hero-title">
                        <a href="{{ route('articles.show', $heroArticle->slug) }}" class="text-white text-decoration-none">
                            {{ $heroArticle->title }}
                        </a>
                    </h2>
                    <p class="text-light-50 d-none d-md-block mb-3 fs-6">
                        {{ Str::limit($heroArticle->summary, 160) }}
                    </p>
                    <div class="d-flex flex-wrap align-items-center gap-3 text-secondary small">
                        <span><i class="bi bi-person-fill text-warning me-1"></i>{{ $heroArticle->author_name }}</span>
                        <span><i class="bi bi-calendar3 me-1"></i>{{ $heroArticle->published_at ? $heroArticle->published_at->format('M d, Y') : 'Today' }} ({{ $heroArticle->published_at ? $heroArticle->published_at->diffForHumans() : '' }})</span>
                        <span><i class="bi bi-clock me-1"></i>{{ $heroArticle->read_time }}</span>
                        @if($heroArticle->hasGame())
                            <span class="badge bg-success-subtle text-success-emphasis"><i class="bi bi-controller me-1"></i>Interactive Game PGN</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Side Sub-Features --}}
        <div class="col-lg-4">
            @foreach($subFeatured as $sub)
                <div class="sub-hero-card">
                    <img src="{{ $sub->image_url ?: asset('images/chess-placeholder.svg') }}" 
                         alt="{{ $sub->title }}"
                         onerror="this.onerror=null; this.src='{{ asset('images/chess-placeholder.svg') }}';">
                    <div class="sub-hero-overlay">
                        <div class="d-flex align-items-center gap-1 mb-1">
                            <span class="badge bg-warning text-dark small">{{ $sub->category->name ?? 'Update' }}</span>
                            <span class="badge bg-secondary-subtle text-secondary-emphasis small">{{ $sub->source_name ?: 'ChessLiv' }}</span>
                        </div>
                        <h3 class="sub-hero-title">
                            <a href="{{ route('articles.show', $sub->slug) }}" class="text-white text-decoration-none">
                                {{ Str::limit($sub->title, 75) }}
                            </a>
                        </h3>
                        <div class="small text-secondary mt-1 d-flex justify-content-between">
                            <span><i class="bi bi-clock me-1"></i>{{ $sub->published_at ? $sub->published_at->diffForHumans() : 'Recently' }}</span>
                            @if($sub->hasGame())
                                <span class="badge bg-success text-white small"><i class="bi bi-controller"></i> PGN</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- MAIN CONTENT & SIDEBAR ROW --}}
    <div class="row g-4">
        {{-- Left: Main News Feed (Newest First with Pagination) --}}
        <div class="col-lg-8">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="bi bi-fire text-warning"></i>
                    @if(request('player'))
                        News for: <span class="text-warning">{{ request('player') }}</span>
                    @elseif(request('category'))
                        Section: <span class="text-warning">{{ $categories->where('slug', request('category'))->first()->name ?? request('category') }}</span>
                    @elseif(request('q'))
                        Search Results: <span class="text-warning">"{{ request('q') }}"</span>
                    @else
                        Latest Dispatches (Newest First)
                    @endif
                </h2>
                <div class="text-muted small">
                    Showing {{ $articles->total() }} stories
                </div>
            </div>

            {{-- Articles Stream --}}
            <div class="row g-4" id="articlesContainer">
                @forelse($articles as $article)
                    <div class="col-md-6 article-grid-item" data-category="{{ $article->category->slug ?? 'all' }}">
                        <div class="article-card h-100">
                            {{-- Thumbnail with broken-image fallback --}}
                            <div class="article-img-wrap">
                                <span class="category-badge">{{ $article->category->name ?? 'News' }}</span>
                                <img src="{{ $article->image_url ?: asset('images/chess-placeholder.svg') }}" 
                                     alt="{{ $article->title }}" 
                                     loading="lazy"
                                     onerror="this.onerror=null; this.src='{{ asset('images/chess-placeholder.svg') }}';">
                                @if($article->hasGame())
                                    <span class="badge bg-success position-absolute bottom-0 end-0 m-2 shadow-sm">
                                        <i class="bi bi-controller me-1"></i>Game PGN
                                    </span>
                                @endif
                            </div>

                            <div class="article-body">
                                {{-- Source & Date Metadata --}}
                                <div class="d-flex justify-content-between align-items-center mb-2 small text-muted">
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                        <i class="bi bi-broadcast me-1"></i>{{ $article->source_name ?: 'ChessLiv Editorial' }}
                                    </span>
                                    <span>
                                        <i class="bi bi-calendar3 me-1"></i>{{ $article->published_at ? $article->published_at->diffForHumans() : 'Recently' }}
                                    </span>
                                </div>

                                {{-- Headline --}}
                                <h3 class="article-title">
                                    <a href="{{ route('articles.show', $article->slug) }}">
                                        {{ $article->title }}
                                    </a>
                                </h3>

                                {{-- Summary Excerpt --}}
                                <p class="article-summary">
                                    {{ $article->summary }}
                                </p>

                                {{-- Card Footer --}}
                                <div class="article-meta mt-auto">
                                    <span class="text-truncate" style="max-width: 140px;">
                                        <i class="bi bi-person me-1"></i>{{ $article->author_name }}
                                    </span>
                                    <span>
                                        <i class="bi bi-clock me-1"></i>{{ $article->read_time }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- Useful Empty State --}}
                    <div class="col-12 py-5 text-center">
                        <div class="card bg-body-tertiary border-0 p-5 shadow-sm text-center">
                            <div class="fs-1 text-warning mb-3"><i class="bi bi-search"></i></div>
                            <h3 class="font-heading fs-4 fw-bold">No articles match your criteria</h3>
                            <p class="text-muted mx-auto" style="max-width: 480px;">
                                We could not find any news matching your current search, player, or category filter. Try clearing filters or searching for popular grandmasters like Gukesh, Magnus, or Nakamura.
                            </p>
                            <div class="mt-3">
                                <a href="{{ route('home') }}" class="btn btn-warning fw-bold px-4">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Clear All Filters
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Pagination Links --}}
            <div class="d-flex justify-content-center mt-5">
                {{ $articles->links('pagination::bootstrap-5') }}
            </div>
        </div>

        {{-- Right: Sidebar Widgets --}}
        <div class="col-lg-4">
            {{-- 1. Live Tournaments Widget --}}
            <div class="sidebar-widget">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="m-0 font-heading fs-6 fw-bold">
                        <i class="bi bi-trophy-fill text-warning me-1"></i>Tournament Arena
                    </h5>
                    <a href="{{ route('tournaments.index') }}" class="small text-muted text-decoration-none">More</a>
                </div>
                <div class="tournament-list">
                    @forelse($ongoingTournaments as $t)
                        <div class="tournament-list-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge-status {{ $t->status }}">{{ $t->status }}</span>
                                <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $t->location }}</small>
                            </div>
                            <h6 class="fw-bold mb-1 mt-1 text-primary-emphasis">{{ $t->name }}</h6>
                            <div class="small text-muted">
                                <strong>Leader:</strong> <span class="text-warning-emphasis fw-semibold">{{ $t->current_leader }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small">No active tournaments right now.</p>
                    @endforelse
                </div>
            </div>

            {{-- 2. Daily Tactics Puzzle Widget --}}
            @if($dailyPuzzle)
            <div class="sidebar-widget puzzle-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="m-0 font-heading fs-6 fw-bold">
                        <i class="bi bi-puzzle-fill text-warning me-1"></i>Daily Tactics
                    </h5>
                    <span class="badge bg-warning text-dark">{{ $dailyPuzzle->rating }} ELO</span>
                </div>
                <p class="small text-muted mb-3">{{ $dailyPuzzle->title }}</p>

                <div class="text-center mb-3">
                    <div class="p-3 bg-dark rounded border border-secondary text-center">
                        <div class="d-flex justify-content-center align-items-center gap-2 mb-2 text-warning">
                            <i class="bi bi-bullseye"></i>
                            <span class="fw-bold text-uppercase small">{{ ucfirst($dailyPuzzle->turn) }} to Move and Win</span>
                        </div>
                        <div class="chessboard-mini mb-2">
                            @for($row = 8; $row >= 1; $row--)
                                @for($col = 1; $col <= 8; $col++)
                                    @php $isLight = ($row + $col) % 2 != 0; @endphp
                                    <div class="chess-square {{ $isLight ? 'light' : 'dark' }}">
                                        @if($row == 8 && $col == 5)<span class="text-dark">♚</span>@endif
                                        @if($row == 1 && $col == 3)<span class="text-light" style="text-shadow: 0 0 2px #000;">♔</span>@endif
                                        @if($row == 3 && $col == 2)<span class="text-light" style="text-shadow: 0 0 2px #000;">♕</span>@endif
                                        @if($row == 5 && $col == 2)<span class="text-light" style="text-shadow: 0 0 2px #000;">♗</span>@endif
                                        @if($row == 5 && $col == 7)<span class="text-light" style="text-shadow: 0 0 2px #000;">♗</span>@endif
                                        @if($row == 6 && $col == 6)<span class="text-dark">♞</span>@endif
                                    </div>
                                @endfor
                            @endfor
                        </div>
                        <code class="small text-secondary d-block font-monospace text-truncate" title="{{ $dailyPuzzle->fen }}">
                            FEN: {{ $dailyPuzzle->fen }}
                        </code>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-warning w-50 btn-puzzle-hint">
                        <i class="bi bi-lightbulb me-1"></i>Hint
                    </button>
                    <button type="button" class="btn btn-sm btn-warning w-50 btn-puzzle-solution">
                        <i class="bi bi-check-circle me-1"></i>Solution
                    </button>
                </div>

                <div class="puzzle-hint-box alert alert-warning p-2 mt-3 small" style="display:none;">
                    <strong>Hint:</strong> {{ $dailyPuzzle->hint ?? 'Look for forcing checks and piece sacrifices.' }}
                </div>

                <div class="puzzle-solution-box alert alert-success p-2 mt-3 small" style="display:none;">
                    <strong>Solution:</strong> <span class="font-monospace fw-bold">{{ $dailyPuzzle->solution }}</span>
                </div>
            </div>
            @endif

            {{-- 3. Live FIDE World Top 5 Ratings --}}
            <div class="sidebar-widget">
                <h5 class="m-0 font-heading fs-6 fw-bold mb-3">
                    <i class="bi bi-bar-chart-line-fill text-warning me-1"></i>FIDE World Top 5
                </h5>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0 small">
                        <thead>
                            <tr class="text-muted">
                                <th>#</th>
                                <th>Grandmaster</th>
                                <th class="text-end">Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold text-warning">1</td>
                                <td><strong>Magnus Carlsen</strong> <span class="text-muted">(NOR)</span></td>
                                <td class="text-end font-monospace fw-bold">2832</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">2</td>
                                <td><strong>Hikaru Nakamura</strong> <span class="text-muted">(USA)</span></td>
                                <td class="text-end font-monospace fw-bold">2805</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">3</td>
                                <td><strong>Arjun Erigaisi</strong> <span class="text-muted">(IND)</span></td>
                                <td class="text-end font-monospace fw-bold">2797</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">4</td>
                                <td><strong>Gukesh D</strong> <span class="text-muted">(IND)</span></td>
                                <td class="text-end font-monospace fw-bold">2794</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">5</td>
                                <td><strong>Fabiano Caruana</strong> <span class="text-muted">(USA)</span></td>
                                <td class="text-end font-monospace fw-bold">2791</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- DEDICATED SECTIONS: When viewing general front page, showcase the 6 key editorial sections --}}
    @if(!request()->filled('q') && !request()->filled('player') && !request()->filled('category'))
    <div class="mt-5 pt-4 border-top">
        <h2 class="font-heading fs-3 fw-bold mb-4 text-center">
            <span class="text-warning">♛</span> Curated Chess Desks &amp; Analysis
        </h2>

        {{-- 1. Tournaments Section --}}
        <div class="mb-5">
            <div class="section-header">
                <h3 class="section-title fs-5">
                    <i class="bi bi-trophy text-warning me-2"></i>Tournaments
                </h3>
                <a href="{{ route('category.show', 'tournaments') }}" class="btn btn-sm btn-outline-secondary">View All Tournaments</a>
            </div>
            <div class="row g-4">
                @foreach($sectionTournaments as $item)
                    <div class="col-lg-3 col-md-6">
                        <div class="article-card h-100">
                            <div class="article-img-wrap" style="height: 150px;">
                                <img src="{{ $item->image_url ?: asset('images/chess-placeholder.svg') }}" 
                                     alt="{{ $item->title }}" 
                                     loading="lazy"
                                     onerror="this.onerror=null; this.src='{{ asset('images/chess-placeholder.svg') }}';">
                                <span class="category-badge">{{ $item->source_name ?: 'Tournaments' }}</span>
                            </div>
                            <div class="article-body p-3">
                                <h4 class="fs-6 fw-bold mb-2">
                                    <a href="{{ route('articles.show', $item->slug) }}" class="text-decoration-none text-reset">
                                        {{ Str::limit($item->title, 60) }}
                                    </a>
                                </h4>
                                <div class="small text-muted mt-auto">{{ $item->published_at ? $item->published_at->diffForHumans() : '' }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 2. Players Section --}}
        <div class="mb-5">
            <div class="section-header">
                <h3 class="section-title fs-5">
                    <i class="bi bi-person-badge text-danger me-2"></i>Players
                </h3>
                <a href="{{ route('category.show', 'player-profiles') }}" class="btn btn-sm btn-outline-secondary">View Player Profiles</a>
            </div>
            <div class="row g-4">
                @foreach($sectionPlayers as $item)
                    <div class="col-lg-3 col-md-6">
                        <div class="article-card h-100">
                            <div class="article-img-wrap" style="height: 150px;">
                                <img src="{{ $item->image_url ?: asset('images/chess-placeholder.svg') }}" 
                                     alt="{{ $item->title }}" 
                                     loading="lazy"
                                     onerror="this.onerror=null; this.src='{{ asset('images/chess-placeholder.svg') }}';">
                                <span class="category-badge">{{ $item->source_name ?: 'Players' }}</span>
                            </div>
                            <div class="article-body p-3">
                                <h4 class="fs-6 fw-bold mb-2">
                                    <a href="{{ route('articles.show', $item->slug) }}" class="text-decoration-none text-reset">
                                        {{ Str::limit($item->title, 60) }}
                                    </a>
                                </h4>
                                <div class="small text-muted mt-auto">{{ $item->published_at ? $item->published_at->diffForHumans() : '' }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 3. World Championships Section --}}
        <div class="mb-5">
            <div class="section-header">
                <h3 class="section-title fs-5">
                    <i class="bi bi-award text-primary me-2"></i>World Championships
                </h3>
                <a href="{{ route('category.show', 'world-championship') }}" class="btn btn-sm btn-outline-secondary">View World Championship News</a>
            </div>
            <div class="row g-4">
                @foreach($sectionWorldChamps as $item)
                    <div class="col-lg-3 col-md-6">
                        <div class="article-card h-100">
                            <div class="article-img-wrap" style="height: 150px;">
                                <img src="{{ $item->image_url ?: asset('images/chess-placeholder.svg') }}" 
                                     alt="{{ $item->title }}" 
                                     loading="lazy"
                                     onerror="this.onerror=null; this.src='{{ asset('images/chess-placeholder.svg') }}';">
                                <span class="category-badge">{{ $item->source_name ?: 'World Match' }}</span>
                            </div>
                            <div class="article-body p-3">
                                <h4 class="fs-6 fw-bold mb-2">
                                    <a href="{{ route('articles.show', $item->slug) }}" class="text-decoration-none text-reset">
                                        {{ Str::limit($item->title, 60) }}
                                    </a>
                                </h4>
                                <div class="small text-muted mt-auto">{{ $item->published_at ? $item->published_at->diffForHumans() : '' }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 4. Chess Openings Section --}}
        <div class="mb-5">
            <div class="section-header">
                <h3 class="section-title fs-5">
                    <i class="bi bi-book text-success me-2"></i>Chess Openings
                </h3>
                <a href="{{ route('category.show', 'opening-theory') }}" class="btn btn-sm btn-outline-secondary">View Opening Theory</a>
            </div>
            <div class="row g-4">
                @foreach($sectionOpenings as $item)
                    <div class="col-lg-3 col-md-6">
                        <div class="article-card h-100">
                            <div class="article-img-wrap" style="height: 150px;">
                                <img src="{{ $item->image_url ?: asset('images/chess-placeholder.svg') }}" 
                                     alt="{{ $item->title }}" 
                                     loading="lazy"
                                     onerror="this.onerror=null; this.src='{{ asset('images/chess-placeholder.svg') }}';">
                                <span class="category-badge">{{ $item->source_name ?: 'Openings' }}</span>
                            </div>
                            <div class="article-body p-3">
                                <h4 class="fs-6 fw-bold mb-2">
                                    <a href="{{ route('articles.show', $item->slug) }}" class="text-decoration-none text-reset">
                                        {{ Str::limit($item->title, 60) }}
                                    </a>
                                </h4>
                                <div class="small text-muted mt-auto">{{ $item->published_at ? $item->published_at->diffForHumans() : '' }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 5. Game Analysis Section --}}
        <div class="mb-5">
            <div class="section-header">
                <h3 class="section-title fs-5">
                    <i class="bi bi-lightbulb text-info me-2"></i>Game Analysis
                </h3>
                <a href="{{ route('category.show', 'gm-insights') }}" class="btn btn-sm btn-outline-secondary">View Grandmaster Analysis</a>
            </div>
            <div class="row g-4">
                @foreach($sectionAnalysis as $item)
                    <div class="col-lg-3 col-md-6">
                        <div class="article-card h-100">
                            <div class="article-img-wrap" style="height: 150px;">
                                <img src="{{ $item->image_url ?: asset('images/chess-placeholder.svg') }}" 
                                     alt="{{ $item->title }}" 
                                     loading="lazy"
                                     onerror="this.onerror=null; this.src='{{ asset('images/chess-placeholder.svg') }}';">
                                <span class="category-badge">{{ $item->source_name ?: 'Game Analysis' }}</span>
                            </div>
                            <div class="article-body p-3">
                                <h4 class="fs-6 fw-bold mb-2">
                                    <a href="{{ route('articles.show', $item->slug) }}" class="text-decoration-none text-reset">
                                        {{ Str::limit($item->title, 60) }}
                                    </a>
                                </h4>
                                <div class="small text-muted mt-auto d-flex justify-content-between">
                                    <span>{{ $item->published_at ? $item->published_at->diffForHumans() : '' }}</span>
                                    @if($item->hasGame())
                                        <span class="badge bg-success small"><i class="bi bi-controller"></i> PGN</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
