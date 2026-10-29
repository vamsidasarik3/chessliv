@extends('layouts.admin')

@section('title', 'Correct Game Metadata: ' . $game->matchup . ' - ChessLiv Admin')

@section('admin_content')
<div class="mb-4">
    <a href="{{ route('admin.games.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="bi bi-arrow-left me-1"></i>Back to Games List
    </a>
    <h2 class="font-heading fs-3 text-white m-0">
        <i class="bi bi-pencil-square text-warning me-2"></i>Correct Game Metadata: {{ $game->matchup }}
    </h2>
    <p class="text-secondary small m-0">
        Amend grandmaster player details, adjust tournament round, correct the final result, or replace malformed PGN notation.
    </p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card admin-card shadow">
            <div class="card-header bg-black bg-opacity-40 border-secondary py-3">
                <h5 class="fs-6 fw-bold text-white m-0">
                    <i class="bi bi-sliders me-2 text-warning"></i>Game Properties &amp; Notation Form
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.games.update', $game->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- White Player Row --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small text-secondary">
                                <i class="bi bi-circle-fill text-light me-1"></i>White Player Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="white" class="form-control form-control-sm bg-black text-white border-secondary" value="{{ old('white', $game->white) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-secondary">White ELO Rating</label>
                            <input type="text" name="white_elo" class="form-control form-control-sm bg-black text-white border-secondary" value="{{ old('white_elo', $game->white_elo) }}" placeholder="e.g. 2750">
                        </div>
                    </div>

                    {{-- Black Player Row --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small text-secondary">
                                <i class="bi bi-circle-fill text-dark border rounded-circle me-1"></i>Black Player Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="black" class="form-control form-control-sm bg-black text-white border-secondary" value="{{ old('black', $game->black) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-secondary">Black ELO Rating</label>
                            <input type="text" name="black_elo" class="form-control form-control-sm bg-black text-white border-secondary" value="{{ old('black_elo', $game->black_elo) }}" placeholder="e.g. 2780">
                        </div>
                    </div>

                    {{-- Event, Site, Round, Date --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-secondary">Tournament / Event</label>
                            <input type="text" name="event" class="form-control form-control-sm bg-black text-white border-secondary" value="{{ old('event', $game->event) }}" placeholder="e.g. FIDE Candidates 2026">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary">Site / Location</label>
                            <input type="text" name="site" class="form-control form-control-sm bg-black text-white border-secondary" value="{{ old('site', $game->site) }}" placeholder="e.g. Toronto, Canada">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small text-secondary">Round</label>
                            <input type="text" name="round" class="form-control form-control-sm bg-black text-white border-secondary" value="{{ old('round', $game->round) }}" placeholder="e.g. 10 or Final">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-secondary">Date Played</label>
                            <input type="text" name="date" class="form-control form-control-sm bg-black text-white border-secondary" value="{{ old('date', $game->date) }}" placeholder="e.g. 2024.04.14">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-secondary">Result <span class="text-danger">*</span></label>
                            <select name="result" class="form-select form-select-sm bg-black text-white border-secondary" required>
                                <option value="1-0" {{ old('result', $game->result) === '1-0' ? 'selected' : '' }}>1-0 (White Win)</option>
                                <option value="0-1" {{ old('result', $game->result) === '0-1' ? 'selected' : '' }}>0-1 (Black Win)</option>
                                <option value="1/2-1/2" {{ old('result', $game->result) === '1/2-1/2' ? 'selected' : '' }}>1/2-1/2 (Draw)</option>
                                <option value="*" {{ old('result', $game->result) === '*' ? 'selected' : '' }}>* (Unfinished / In Progress)</option>
                            </select>
                        </div>
                    </div>

                    {{-- ECO & Starting FEN --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small text-secondary">ECO Code</label>
                            <input type="text" name="eco" class="form-control form-control-sm bg-black text-white border-secondary" value="{{ old('eco', $game->eco) }}" placeholder="e.g. B90, D38">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small text-secondary">Starting FEN (Leave empty for standard starting board)</label>
                            <input type="text" name="starting_fen" class="form-control form-control-sm bg-black text-white border-secondary font-monospace" value="{{ old('starting_fen', $game->starting_fen) }}" placeholder="{{ \App\Models\ChessGame::STANDARD_STARTING_FEN }}">
                        </div>
                    </div>

                    {{-- Complete PGN --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small text-secondary m-0">Complete PGN Notation <span class="text-danger">*</span></label>
                            <span class="badge bg-secondary font-monospace small">Currently {{ $game->move_count }} Plies Parsed</span>
                        </div>
                        <textarea name="pgn" class="form-control form-control-sm bg-black text-white border-secondary font-monospace" rows="10" required>{{ old('pgn', $game->pgn) }}</textarea>
                        <div class="form-text text-secondary small">
                            Includes standard tags and SAN move line. PgnParserService will validate moves and automatically sync the interactive board.
                        </div>
                    </div>

                    {{-- Source URL --}}
                    <div class="mb-4">
                        <label class="form-label small text-secondary">Source URL</label>
                        <input type="url" name="source_url" class="form-control form-control-sm bg-black text-white border-secondary" value="{{ old('source_url', $game->source_url) }}" placeholder="https://...">
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top border-secondary">
                        <a href="{{ route('admin.games.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                        <button type="submit" class="btn btn-warning btn-sm fw-bold px-4">
                            <i class="bi bi-save me-1"></i>Save &amp; Sync Game Metadata
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Associated Article & Tips Sidebar --}}
    <div class="col-lg-4">
        <div class="card admin-card mb-4 shadow">
            <div class="card-header bg-black bg-opacity-40 border-secondary py-3">
                <h5 class="fs-6 fw-bold text-white m-0">
                    <i class="bi bi-newspaper me-2 text-warning"></i>Associated Article
                </h5>
            </div>
            <div class="card-body p-3">
                @if($game->article)
                    <h6 class="text-white fw-bold mb-2">{{ $game->article->title }}</h6>
                    <p class="text-secondary small mb-3">{{ Str::limit($game->article->summary, 120) }}</p>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-secondary">{{ $game->article->category->name ?? 'News' }}</span>
                        <span class="badge bg-dark border border-secondary">{{ $game->article->read_time }}</span>
                    </div>
                    <a href="{{ route('articles.show', $game->article->slug) }}" target="_blank" class="btn btn-outline-warning btn-sm w-100">
                        <i class="bi bi-eye me-1"></i>Preview on Public Article
                    </a>
                @else
                    <p class="text-secondary small m-0">This game is not currently attached to any article.</p>
                @endif
            </div>
        </div>

        <div class="card admin-card shadow">
            <div class="card-header bg-black bg-opacity-40 border-secondary py-3">
                <h5 class="fs-6 fw-bold text-white m-0">
                    <i class="bi bi-info-circle text-warning me-2"></i>Synchronization Guarantee
                </h5>
            </div>
            <div class="card-body p-3 small text-secondary">
                <p class="mb-2">
                    Saving this form executes an atomic database transaction:
                </p>
                <ul class="ps-3 mb-0">
                    <li>Updates <code>chess_games</code> record with validated PGN.</li>
                    <li>Recomputes the structured <code>moves_san</code> JSON array.</li>
                    <li>Updates the parent <code>articles</code> table fields for search indexing (<code>game_white</code>, <code>game_black</code>, <code>player_names</code>, <code>game_result</code>).</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
