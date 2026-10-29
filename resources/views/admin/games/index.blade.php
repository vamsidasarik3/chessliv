@extends('layouts.admin')

@section('title', 'Game Metadata Correction - ChessLiv Admin')

@section('admin_content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="font-heading fs-3 text-white m-0">
            <i class="bi bi-controller text-warning me-2"></i>Chess Game Metadata Correction
        </h2>
        <p class="text-secondary small m-0">
            Review parsed PGN games, correct master player names, update ELO ratings, edit move notations, and adjust tournament results.
        </p>
    </div>
</div>

{{-- Search & Filters --}}
<div class="card admin-card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.games.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-dark text-secondary border-secondary"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control bg-dark text-white border-secondary" placeholder="Search by player (Gukesh, Carlsen), event, site, or article title..." value="{{ request('q') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="result" class="form-select form-select-sm bg-dark text-white border-secondary">
                    <option value="">All Game Results</option>
                    <option value="1-0" {{ request('result') === '1-0' ? 'selected' : '' }}>1-0 (White Win)</option>
                    <option value="0-1" {{ request('result') === '0-1' ? 'selected' : '' }}>0-1 (Black Win)</option>
                    <option value="1/2-1/2" {{ request('result') === '1/2-1/2' ? 'selected' : '' }}>1/2-1/2 (Draw)</option>
                    <option value="*" {{ request('result') === '*' ? 'selected' : '' }}>* (Ongoing / Unspecified)</option>
                </select>
            </div>
            <div class="col-md-2 text-md-end">
                <button type="submit" class="btn btn-sm btn-outline-warning w-100">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Games Table --}}
<div class="card admin-card mb-4 shadow">
    <div class="card-header bg-black bg-opacity-40 border-secondary d-flex justify-content-between align-items-center py-3">
        <h5 class="fs-6 fw-bold text-white m-0">
            <i class="bi bi-grid-3x3-gap-fill me-2 text-warning"></i>Master Games Database ({{ $games->total() }} Games Recorded)
        </h5>
        @if(request()->anyFilled(['q', 'result']))
            <a href="{{ route('admin.games.index') }}" class="btn btn-sm btn-outline-secondary py-0">Reset Filters</a>
        @endif
    </div>

    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0 small">
            <thead class="table-dark text-secondary text-uppercase" style="font-size: 0.75rem;">
                <tr>
                    <th style="width: 40px;">#</th>
                    <th>Matchup</th>
                    <th>Event &amp; Round</th>
                    <th class="text-center">Result</th>
                    <th class="text-center">ECO</th>
                    <th class="text-center">Moves (Plies)</th>
                    <th>Associated News Article</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($games as $game)
                    <tr>
                        <td class="text-muted">{{ $game->id }}</td>
                        <td>
                            <div>
                                <span class="d-inline-block rounded-circle bg-light me-1" style="width: 8px; height: 8px;"></span>
                                <strong class="text-white">{{ $game->white ?: 'White' }}</strong>
                                @if($game->white_elo)
                                    <span class="badge bg-secondary ms-1">{{ $game->white_elo }}</span>
                                @endif
                            </div>
                            <div class="mt-1">
                                <span class="d-inline-block rounded-circle bg-dark border border-secondary me-1" style="width: 8px; height: 8px;"></span>
                                <strong class="text-secondary">{{ $game->black ?: 'Black' }}</strong>
                                @if($game->black_elo)
                                    <span class="badge bg-secondary ms-1">{{ $game->black_elo }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="text-white fw-semibold">{{ $game->event ?: 'Championship' }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                @if($game->round) Round {{ $game->round }} • @endif
                                {{ $game->date ?: 'Date unrecorded' }}
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge font-monospace {{ $game->result === '1-0' ? 'bg-success' : ($game->result === '0-1' ? 'bg-danger' : 'bg-secondary') }}">
                                {{ $game->result ?: '*' }}
                            </span>
                        </td>
                        <td class="text-center font-monospace text-warning">
                            {{ $game->eco ?: '—' }}
                        </td>
                        <td class="text-center font-monospace">
                            <span class="badge bg-dark border border-secondary">{{ $game->move_count }}</span>
                        </td>
                        <td>
                            @if($game->article)
                                <a href="{{ route('articles.show', $game->article->slug) }}" target="_blank" class="text-decoration-none text-light" title="{{ $game->article->title }}">
                                    {{ Str::limit($game->article->title, 45) }}
                                    <i class="bi bi-box-arrow-up-right ms-1 small text-secondary"></i>
                                </a>
                            @else
                                <span class="text-muted fst-italic">Standalone Game</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.games.edit', $game->id) }}" class="btn btn-sm btn-outline-warning py-0 px-2 fw-semibold">
                                <i class="bi bi-pencil-square me-1"></i>Correct
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-secondary">
                            <i class="bi bi-controller fs-3 d-block mb-2"></i>
                            No chess games found matching your search.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($games->hasPages())
        <div class="card-footer bg-black bg-opacity-25 border-secondary py-3">
            {{ $games->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
