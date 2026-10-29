@extends('layouts.app')

@section('title', 'Chess Tactics & Puzzles Trainer - ChessLiv')
@section('meta_description', 'Sharpen your calculation and tactical vision with master puzzles, smothered mates, and combinations.')

@section('content')
<div class="container-xl">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Daily Tactics</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="text-center mb-5">
        <span class="badge bg-warning text-dark text-uppercase px-3 py-1 fw-bold mb-2">Tactical Vision</span>
        <h1 class="font-heading fs-1 fw-bold mb-2">
            <i class="bi bi-puzzle-fill text-warning me-2"></i>Grandmaster Tactics Studio
        </h1>
        <p class="lead text-muted mx-auto" style="max-width: 650px;">
            Solve decisive combinations selected from classical and modern championship games. Calculate carefully before checking the solution.
        </p>
    </div>

    {{-- Puzzles Grid --}}
    <div class="row g-4 justify-content-center">
        @forelse($puzzles as $puz)
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-3 p-4 puzzle-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-warning text-dark fw-bold px-2 py-1">
                            <i class="bi bi-star-fill me-1"></i>{{ $puz->rating }} ELO
                        </span>
                        <span class="badge {{ $puz->turn === 'white' ? 'bg-light text-dark border' : 'bg-dark text-white' }} text-uppercase px-2 py-1">
                            {{ ucfirst($puz->turn) }} to Move
                        </span>
                    </div>

                    <h3 class="fs-5 fw-bold mb-3">{{ $puz->title }}</h3>

                    {{-- Mini Chessboard Graphic --}}
                    <div class="p-3 bg-dark rounded border border-secondary text-center mb-3">
                        <div class="chessboard-mini mb-2">
                            @for($row = 8; $row >= 1; $row--)
                                @for($col = 1; $col <= 8; $col++)
                                    @php $isLight = ($row + $col) % 2 != 0; @endphp
                                    <div class="chess-square {{ $isLight ? 'light' : 'dark' }}">
                                        @if($row == 8 && $col == 5)<span class="text-dark">♚</span>@endif
                                        @if($row == 1 && $col == 4)<span class="text-light" style="text-shadow: 0 0 2px #000;">♔</span>@endif
                                        @if($row == 4 && $col == 4)<span class="text-light" style="text-shadow: 0 0 2px #000;">♕</span>@endif
                                        @if($row == 5 && $col == 3)<span class="text-dark">♞</span>@endif
                                        @if($row == 6 && $col == 6)<span class="text-light" style="text-shadow: 0 0 2px #000;">♗</span>@endif
                                    </div>
                                @endfor
                            @endfor
                        </div>
                        <code class="small text-secondary d-block font-monospace text-truncate" title="{{ $puz->fen }}">
                            {{ $puz->fen }}
                        </code>
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex gap-2 mt-auto">
                        <button type="button" class="btn btn-sm btn-outline-warning w-50 btn-puzzle-hint">
                            <i class="bi bi-lightbulb me-1"></i>Hint
                        </button>
                        <button type="button" class="btn btn-sm btn-warning w-50 btn-puzzle-solution">
                            <i class="bi bi-check-circle me-1"></i>Solution
                        </button>
                    </div>

                    {{-- Hint Box --}}
                    <div class="puzzle-hint-box alert alert-warning p-2 mt-3 small" style="display:none;">
                        <strong>Hint:</strong> {{ $puz->hint ?? 'Look for weaknesses around the uncastled king.' }}
                    </div>

                    {{-- Solution Box --}}
                    <div class="puzzle-solution-box alert alert-success p-2 mt-3 small" style="display:none;">
                        <strong>Winning Move(s):</strong>
                        <div class="font-monospace fw-bold fs-6 mt-1">{{ $puz->solution }}</div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">No puzzles available at this time.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
