@extends('layouts.app')

@section('title', $article->title . ' - ChessLiv')
@section('meta_description', Str::limit($article->summary, 150))

@push('styles')
@if($chessGame && $chessGame->hasMoves())
<link rel="stylesheet" href="{{ asset('vendor/chess/chessboard.min.css') }}">
<style>
    /* Chessboard styling */
    #chesslivBoard {
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 4px 14px rgba(0,0,0,0.4);
    }
    .highlight-from {
        background-color: rgba(255, 235, 59, 0.55) !important;
        box-shadow: inset 0 0 4px rgba(0,0,0,0.2);
    }
    .highlight-to {
        background-color: rgba(255, 193, 7, 0.7) !important;
        box-shadow: inset 0 0 4px rgba(0,0,0,0.3);
    }
    .pgn-move-btn {
        cursor: pointer;
        transition: all 0.12s ease-in-out;
        border-radius: 3px;
    }
    .pgn-move-btn:hover {
        background-color: rgba(255, 193, 7, 0.25) !important;
    }
    .pgn-move-btn.active-move {
        background-color: #ffc107 !important;
        color: #000 !important;
        font-weight: 700 !important;
    }
    .pgn-move-btn.active-move .move-span {
        color: #000 !important;
    }
</style>
@endif
@endpush

@section('content')
<div class="container-xl">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('articles.index') }}" class="text-decoration-none">News</a></li>
            @if($article->category)
                <li class="breadcrumb-item"><a href="{{ route('category.show', $article->category->slug) }}" class="text-decoration-none">{{ $article->category->name }}</a></li>
            @endif
            <li class="breadcrumb-item active text-truncate" style="max-width: 300px;" aria-current="page">{{ $article->title }}</li>
        </ol>
    </nav>

    <div class="row g-5">
        {{-- Main Article Content Column --}}
        <div class="col-lg-8">
            <article class="article-detail">
                {{-- Category & Date Header --}}
                <div class="mb-3 d-flex flex-wrap align-items-center gap-2">
                    <span class="badge bg-warning text-dark fw-bold px-3 py-1">
                        {{ $article->category->name ?? 'Special Report' }}
                    </span>
                    @if($article->is_breaking)
                        <span class="badge bg-danger text-white px-2 py-1">
                            <i class="bi bi-lightning-fill me-1"></i>Breaking
                        </span>
                    @endif
                    @if($article->source_name)
                        <span class="badge bg-secondary-subtle text-secondary-emphasis">
                            <i class="bi bi-broadcast me-1"></i>{{ $article->source_name }}
                        </span>
                    @endif
                    <span class="text-muted small">
                        <i class="bi bi-calendar3 me-1"></i>{{ $article->published_at ? $article->published_at->format('M d, Y') : 'Published Today' }} ({{ $article->published_at ? $article->published_at->diffForHumans() : '' }})
                    </span>
                </div>

                {{-- Headline --}}
                <h1 class="font-heading fs-1 fw-bold mb-3" style="line-height: 1.25;">
                    {{ $article->title }}
                </h1>

                {{-- Author & Utility Bar --}}
                <div class="d-flex flex-wrap justify-content-between align-items-center py-3 border-top border-bottom mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 44px; height: 44px; font-size: 1.2rem;">
                            {{ strtoupper(substr($article->author_name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold">{{ $article->author_name }}</div>
                            <div class="text-muted small">{{ $article->author_title ?: ($article->source_name ?: 'ChessLiv Analyst') }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 mt-2 mt-sm-0">
                        <span class="text-muted small"><i class="bi bi-clock me-1"></i>{{ $article->read_time }}</span>
                        <span class="text-muted small"><i class="bi bi-eye me-1"></i>{{ number_format($article->views_count) }} views</span>
                        
                        {{-- Font Size Controls (jQuery) --}}
                        <div class="btn-group btn-group-sm" role="group" aria-label="Font Resizer">
                            <button type="button" id="fontDecrease" class="btn btn-outline-secondary" title="Decrease font size">A-</button>
                            <button type="button" id="fontReset" class="btn btn-outline-secondary" title="Reset font size">A</button>
                            <button type="button" id="fontIncrease" class="btn btn-outline-secondary" title="Increase font size">A+</button>
                        </div>

                        {{-- Copy Link Button --}}
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-copy-link" title="Copy article URL">
                            <i class="bi bi-share me-1"></i>Share
                        </button>
                    </div>
                </div>

                {{-- Featured Image with Broken-Image Fallback --}}
                <div class="mb-4">
                    <img src="{{ $article->image_url ?: asset('images/chess-placeholder.svg') }}" 
                         alt="{{ $article->title }}" 
                         class="article-hero-img"
                         onerror="this.onerror=null; this.src='{{ asset('images/chess-placeholder.svg') }}';">
                    <div class="text-muted small text-end fst-italic mt-1">
                        Source: {{ $article->source_name ?: 'Global Chess Circuit / Official Archive' }}
                    </div>
                </div>

                {{-- Summary Lead --}}
                <div class="p-3 bg-body-tertiary rounded border-start border-4 border-warning mb-4">
                    <p class="fs-5 fw-medium text-secondary-emphasis mb-0 fst-italic">
                        {{ $article->summary }}
                    </p>
                </div>

                {{-- ASSOCIATED CHESS GAME VIEWER --}}
                @if($chessGame && $chessGame->hasMoves())
                <div class="card bg-dark text-white border-warning border-2 shadow-lg mb-5" id="gameViewer">
                    <div class="card-header bg-black bg-opacity-75 border-warning py-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div>
                                <span class="badge bg-warning text-dark text-uppercase fw-bold mb-1">
                                    <i class="bi bi-controller me-1"></i>Associated Master Game
                                </span>
                                <h4 class="font-heading fs-5 text-white m-0">
                                    {{ $chessGame->matchup }}
                                </h4>
                                <small class="text-secondary">
                                    {{ $chessGame->event ?: 'Tournament Match' }}
                                    @if($chessGame->round) • Round {{ $chessGame->round }} @endif
                                    @if($chessGame->date) • {{ $chessGame->date }} @endif
                                    @if($chessGame->eco) • ECO: {{ $chessGame->eco }} @endif
                                </small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge fs-6 bg-success px-3 py-2 fw-bold font-monospace">
                                    Result: {{ $chessGame->result ?: '*' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-4 align-items-center">
                            {{-- Interactive Chessboard Container --}}
                            <div class="col-md-6 text-center">
                                <div class="p-3 bg-black rounded-3 border border-secondary shadow mx-auto" style="max-width: 400px;">
                                    {{-- Black Player Header --}}
                                    <div class="d-flex justify-content-between align-items-center mb-2 px-1 text-secondary small">
                                        <div class="d-flex align-items-center text-truncate">
                                            <span class="rounded-circle d-inline-block bg-dark border border-secondary me-2" style="width: 14px; height: 14px;"></span>
                                            <strong class="text-light text-truncate" id="boardPlayerBlack">{{ $chessGame->black ?: 'Black' }}</strong>
                                            @if($chessGame->black_elo)
                                                <span class="badge bg-secondary ms-1">{{ $chessGame->black_elo }}</span>
                                            @endif
                                        </div>
                                        <span class="badge bg-dark border border-secondary text-warning" id="blackTurnDot" style="display: none;">To Move</span>
                                    </div>

                                    {{-- Real chessboard.js Rendering Container --}}
                                    <div id="chesslivBoard" class="mx-auto mb-2" style="width: 100%; max-width: 360px;"></div>

                                    {{-- White Player Header --}}
                                    <div class="d-flex justify-content-between align-items-center mb-3 px-1 text-secondary small">
                                        <div class="d-flex align-items-center text-truncate">
                                            <span class="rounded-circle d-inline-block bg-light border border-secondary me-2" style="width: 14px; height: 14px;"></span>
                                            <strong class="text-light text-truncate" id="boardPlayerWhite">{{ $chessGame->white ?: 'White' }}</strong>
                                            @if($chessGame->white_elo)
                                                <span class="badge bg-secondary ms-1">{{ $chessGame->white_elo }}</span>
                                            @endif
                                        </div>
                                        <span class="badge bg-dark border border-secondary text-warning" id="whiteTurnDot">To Move</span>
                                    </div>

                                    {{-- Move Controls Bar --}}
                                    <div class="d-flex justify-content-between align-items-center gap-1 mb-2">
                                        <div class="btn-group flex-grow-1" role="group" aria-label="Game Navigation">
                                            <button type="button" id="btnMoveStart" class="btn btn-sm btn-outline-warning" title="Start / Initial Position">
                                                <i class="bi bi-chevron-bar-left"></i> Start
                                            </button>
                                            <button type="button" id="btnMovePrev" class="btn btn-sm btn-outline-warning" title="Previous Move">
                                                <i class="bi bi-chevron-left"></i> Prev
                                            </button>
                                            <button type="button" id="btnMoveNext" class="btn btn-sm btn-outline-warning" title="Next Move">
                                                Next <i class="bi bi-chevron-right"></i>
                                            </button>
                                            <button type="button" id="btnMoveEnd" class="btn btn-sm btn-outline-warning" title="End / Final Position">
                                                End <i class="bi bi-chevron-bar-right"></i>
                                            </button>
                                        </div>
                                        <button type="button" id="btnFlipBoard" class="btn btn-sm btn-outline-secondary" title="Flip Board Orientation">
                                            <i class="bi bi-arrow-repeat"></i> Flip
                                        </button>
                                    </div>

                                    {{-- Move Indicator Status --}}
                                    <div class="d-flex justify-content-between align-items-center px-1 text-secondary small">
                                        <span id="boardMoveIndicator" class="text-light font-monospace fw-semibold">
                                            Start Position
                                        </span>
                                        <span id="boardTurnBadge" class="badge bg-secondary font-monospace">
                                            White to move
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Move Notation Scroll Box --}}
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small fw-bold text-uppercase text-secondary">
                                        <i class="bi bi-list-ol text-warning me-1"></i>Moves Notation ({{ count($gameTurns) }} Turns • {{ $chessGame->move_count }} Plies)
                                    </span>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" id="btnCopyPgn" class="btn btn-outline-secondary py-0" title="Copy Raw PGN">
                                            <i class="bi bi-clipboard me-1"></i>Copy PGN
                                        </button>
                                    </div>
                                </div>

                                <div class="p-2 bg-black rounded border border-secondary" style="max-height: 330px; overflow-y: auto;" id="pgnMoveList">
                                    <table class="table table-sm table-dark table-hover mb-0 font-monospace small">
                                        <tbody>
                                            @foreach($gameTurns as $turn)
                                                <tr>
                                                    <td class="text-muted text-end" style="width: 44px;">{{ $turn['number'] }}.</td>
                                                    <td class="pgn-move-btn px-2 py-1" data-ply="{{ $turn['white_ply'] }}" style="width: 45%; cursor: pointer;">
                                                        <span class="move-span">{{ $turn['white'] }}</span>
                                                    </td>
                                                    <td class="pgn-move-btn px-2 py-1" @if($turn['black_ply']) data-ply="{{ $turn['black_ply'] }}" style="width: 45%; cursor: pointer;" @endif>
                                                        <span class="move-span">{{ $turn['black'] }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="badge bg-secondary font-monospace" id="activeMoveDisplay">
                                        Start Position
                                    </span>
                                    <small class="text-muted">Tip: Click any move or use Left/Right keys</small>
                                </div>

                                <textarea id="rawPgnText" class="d-none">{{ $chessGame->pgn }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                {{-- Clear No Game Moves Available Message --}}
                <div class="card bg-body-tertiary border-secondary-subtle my-4 shadow-sm">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-slash-circle fs-3 text-secondary d-block mb-2"></i>
                        <h5 class="fs-6 fw-bold text-secondary-emphasis mb-1">No game moves available</h5>
                        <p class="small text-muted mb-0">This article does not contain playable chess game moves or structured notation.</p>
                    </div>
                </div>
                @endif

                {{-- Body Content --}}
                <div class="article-content">
                    {!! $article->content !!}
                </div>

                {{-- Source Attribution & Original Publication Details --}}
                @if($article->newsSource || $article->original_url || $article->source_name)
                    <div class="card bg-body-tertiary border-secondary-subtle mt-4 mb-4 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div>
                                    <span class="badge bg-warning text-dark text-uppercase fw-bold me-2">Original Publisher</span>
                                    <strong class="text-white">{{ $article->newsSource->name ?? $article->source_name }}</strong>
                                    @if($article->published_at)
                                        <span class="text-muted small ms-2">
                                            • Published {{ $article->published_at->format('M d, Y \a\t H:i T') }} ({{ $article->published_at->diffForHumans() }})
                                        </span>
                                    @endif
                                </div>
                                @if($article->original_url)
                                    <div>
                                        <a href="{{ $article->original_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-warning">
                                            Read Original Article <i class="bi bi-box-arrow-up-right ms-1"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="text-secondary small mt-2 pt-2 border-top border-secondary-subtle">
                                <i class="bi bi-shield-check me-1 text-success"></i>
                                Copyright and editorial rights belong to the original publisher. ChessLiv syndicates headlines and official summaries for journalistic review.
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Article Footer Tags & Actions --}}
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="fw-semibold text-muted small"><i class="bi bi-tags me-1"></i>Tags:</span>
                            @if($article->player_names)
                                @foreach(explode(',', $article->player_names) as $pname)
                                    <a href="{{ route('home', ['player' => trim($pname)]) }}" class="badge bg-warning text-dark text-decoration-none">
                                        {{ trim($pname) }}
                                    </a>
                                @endforeach
                            @endif
                            <span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $article->category->name ?? 'News' }}</span>
                            @if($chessGame && $chessGame->hasMoves())
                                <span class="badge bg-success-subtle text-success-emphasis">PGN Annotated</span>
                            @endif
                        </div>
                        <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back to Home
                        </a>
                    </div>
                </div>
            </article>

            {{-- Related Stories in This Category --}}
            @if($relatedArticles->isNotEmpty())
            <div class="mt-5 pt-4">
                <div class="section-header">
                    <h3 class="section-title fs-5">
                        <i class="bi bi-collection-play-fill text-warning me-1"></i>Related in {{ $article->category->name ?? 'This Category' }}
                    </h3>
                </div>
                <div class="row g-4">
                    @foreach($relatedArticles as $rel)
                        <div class="col-md-4">
                            <div class="article-card h-100">
                                <div class="article-img-wrap" style="height: 140px;">
                                    <img src="{{ $rel->image_url ?: asset('images/chess-placeholder.svg') }}" 
                                         alt="{{ $rel->title }}" 
                                         loading="lazy"
                                         onerror="this.onerror=null; this.src='{{ asset('images/chess-placeholder.svg') }}';">
                                    <span class="category-badge">{{ $rel->source_name ?: 'News' }}</span>
                                </div>
                                <div class="article-body p-3">
                                    <h4 class="fs-6 fw-bold mb-2">
                                        <a href="{{ route('articles.show', $rel->slug) }}" class="text-decoration-none text-reset">
                                            {{ Str::limit($rel->title, 60) }}
                                        </a>
                                    </h4>
                                    <div class="small text-muted mt-auto d-flex justify-content-between">
                                        <span>{{ $rel->read_time }}</span>
                                        @if($rel->hasGame())
                                            <span class="badge bg-success small"><i class="bi bi-controller"></i></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Right Column: Sidebar --}}
        <div class="col-lg-4">
            {{-- Quick Categories List --}}
            <div class="sidebar-widget">
                <h5 class="font-heading fs-6 fw-bold mb-3">
                    <i class="bi bi-folder2-open text-warning me-1"></i>Explore Sections
                </h5>
                <ul class="list-group list-group-flush small">
                    @foreach($categories as $cat)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                            <a href="{{ route('category.show', $cat->slug) }}" class="text-decoration-none {{ $cat->id === $article->category_id ? 'fw-bold text-warning' : 'text-reset' }}">
                                <i class="bi {{ $cat->icon }} me-2 text-warning"></i>{{ $cat->name }}
                            </a>
                            <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">{{ $cat->articles_count }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Newsletter Box --}}
            <div class="sidebar-widget bg-dark text-white">
                <h5 class="font-heading fs-6 fw-bold text-white mb-2">
                    <i class="bi bi-envelope-check-fill text-warning me-1"></i>Get Next Round Analysis
                </h5>
                <p class="text-secondary small mb-3">
                    Never miss a crucial novelty or candidates tiebreak result. Delivered to your inbox.
                </p>
                <form class="newsletter-form">
                    @csrf
                    <div class="mb-2">
                        <input type="email" class="form-control form-control-sm bg-secondary text-white border-0" placeholder="Your email address" required>
                    </div>
                    <button class="btn btn-warning btn-sm w-100 fw-bold" type="submit">Subscribe Free</button>
                    <div class="newsletter-response small mt-2" style="display:none;"></div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($chessGame && $chessGame->hasMoves())
    <script src="{{ asset('vendor/chess/chess.min.js') }}"></script>
    <script src="{{ asset('vendor/chess/chessboard.min.js') }}"></script>
    <script>
    $(function() {
        var startingFen = {!! json_encode($chessGame->effective_starting_fen) !!};
        var sanMoves = {!! json_encode($chessGame->moves_san ?? []) !!};
        var gameResult = {!! json_encode($chessGame->result ?? '*') !!};

        // Initialize chess engine
        var chessEngine = null;
        try {
            chessEngine = startingFen ? new Chess(startingFen) : new Chess();
        } catch(e) {
            console.error('Invalid starting FEN, falling back to standard start', e);
            chessEngine = new Chess();
        }

        // Validate each move step-by-step and build positions history array
        var positions = [{
            fen: chessEngine.fen(),
            turn: chessEngine.turn(),
            inCheck: chessEngine.in_check(),
            inCheckmate: chessEngine.in_checkmate(),
            move: null,
            san: null,
            ply: 0
        }];

        for (var i = 0; i < sanMoves.length; i++) {
            var moveSan = sanMoves[i];
            var m = chessEngine.move(moveSan);
            if (!m) {
                console.warn('Invalid or illegal move encountered:', moveSan, 'at move index', i);
                break; // stop gracefully at last legal position
            }
            positions.push({
                fen: chessEngine.fen(),
                turn: chessEngine.turn(),
                inCheck: chessEngine.in_check(),
                inCheckmate: chessEngine.in_checkmate(),
                move: m,
                san: m.san,
                ply: i + 1
            });
        }

        var currentPly = 0;

        // Initialize visual board
        var board = Chessboard('chesslivBoard', {
            position: positions[0].fen,
            pieceTheme: '{{ asset("vendor/chess/img/chesspieces/wikipedia/{piece}.png") }}',
            orientation: 'white',
            showNotation: true
        });

        // Highlight last move squares
        function highlightSquares(move) {
            $('#chesslivBoard .square-55d63').removeClass('highlight-from highlight-to');
            if (move && move.from && move.to) {
                $('#chesslivBoard .square-' + move.from).addClass('highlight-from');
                $('#chesslivBoard .square-' + move.to).addClass('highlight-to');
            }
        }

        // Navigate to specified ply
        function goToPly(targetPly) {
            if (targetPly < 0) targetPly = 0;
            if (targetPly >= positions.length) targetPly = positions.length - 1;

            currentPly = targetPly;
            var pos = positions[currentPly];

            // Update board position
            board.position(pos.fen, true);

            // Update square highlights
            highlightSquares(pos.move);

            // Update navigation buttons
            $('#btnMoveStart, #btnMovePrev').prop('disabled', currentPly === 0);
            $('#btnMoveNext, #btnMoveEnd').prop('disabled', currentPly === positions.length - 1);

            // Update status text
            if (currentPly === 0) {
                $('#boardMoveIndicator').text('Start Position');
                $('#boardTurnBadge').text('White to move').removeClass('bg-warning bg-danger').addClass('bg-secondary');
                $('#activeMoveDisplay').text('Start Position');
                $('#whiteTurnDot').show();
                $('#blackTurnDot').hide();
            } else {
                var moveNum = Math.ceil(currentPly / 2);
                var isWhiteMove = (currentPly % 2 === 1);
                var notationText = moveNum + (isWhiteMove ? '. ' : '... ') + pos.san;

                var statusMsg = 'Move ' + moveNum + ' (' + notationText + ')';
                if (pos.inCheckmate) {
                    statusMsg += ' • Checkmate!';
                } else if (pos.inCheck) {
                    statusMsg += ' • Check!';
                }
                $('#boardMoveIndicator').text(statusMsg);

                var sideText = (pos.turn === 'w') ? 'White to move' : 'Black to move';
                if (pos.inCheckmate) {
                    $('#boardTurnBadge').text('Game Over (' + gameResult + ')').removeClass('bg-secondary').addClass('bg-danger');
                } else {
                    $('#boardTurnBadge').text(sideText).removeClass('bg-danger').addClass('bg-secondary');
                }

                if (pos.turn === 'w') {
                    $('#whiteTurnDot').show();
                    $('#blackTurnDot').hide();
                } else {
                    $('#whiteTurnDot').hide();
                    $('#blackTurnDot').show();
                }

                $('#activeMoveDisplay').text(notationText);
            }

            // Highlight in notation table
            $('.pgn-move-btn').removeClass('active-move');
            var $activeCell = $('.pgn-move-btn[data-ply="' + currentPly + '"]');
            if ($activeCell.length) {
                $activeCell.addClass('active-move');
                // Auto scroll list container
                var container = $('#pgnMoveList');
                if (container.length) {
                    var cellPos = $activeCell.position();
                    if (cellPos) {
                        var cellTop = cellPos.top;
                        container.scrollTop(container.scrollTop() + cellTop - 80);
                    }
                }
            }
        }

        // Control clicks
        $('#btnMoveStart').on('click', function() { goToPly(0); });
        $('#btnMovePrev').on('click', function() { goToPly(currentPly - 1); });
        $('#btnMoveNext').on('click', function() { goToPly(currentPly + 1); });
        $('#btnMoveEnd').on('click', function() { goToPly(positions.length - 1); });
        $('#btnFlipBoard').on('click', function() {
            board.flip();
            // Re-apply highlights after flip re-renders squares
            highlightSquares(positions[currentPly].move);
        });

        // Click on notation move
        $(document).on('click', '.pgn-move-btn', function() {
            var ply = parseInt($(this).data('ply'), 10);
            if (!isNaN(ply)) {
                goToPly(ply);
            }
        });

        // Keyboard arrow navigation
        $(document).on('keydown', function(e) {
            if ($(e.target).is('input, textarea')) return;
            if (e.keyCode === 37) { // Left arrow
                goToPly(currentPly - 1);
            } else if (e.keyCode === 39) { // Right arrow
                goToPly(currentPly + 1);
            }
        });

        // Window resize responsive board redraw
        $(window).on('resize', function() {
            board.resize();
            highlightSquares(positions[currentPly].move);
        });

        // Copy PGN button
        $('#btnCopyPgn').on('click', function() {
            var pgn = $('#rawPgnText').val();
            navigator.clipboard.writeText(pgn).then(function() {
                var $btn = $('#btnCopyPgn');
                var originalHtml = $btn.html();
                $btn.html('<i class="bi bi-check2 text-success me-1"></i>Copied!');
                setTimeout(function() {
                    $btn.html(originalHtml);
                }, 2000);
            });
        });

        // Initialize starting button states
        goToPly(0);
    });
    </script>
@endif
@endpush
