<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Exception;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        try {
            $categories = Category::where('is_active', true)->withCount('articles')->get();
            $query = Article::with(['category', 'newsSource'])->latest('published_at');

            // Category filter
            if ($request->filled('category')) {
                $catSlug = $request->query('category');
                $query->whereHas('category', function ($q) use ($catSlug) {
                    $q->where('slug', $catSlug);
                });
                $currentCategory = Category::where('slug', $catSlug)->first();
            } else {
                $currentCategory = null;
            }

            // Player filter
            if ($request->filled('player')) {
                $query->forPlayer($request->query('player'));
            }

            // Search query
            if ($request->filled('q')) {
                $s = trim($request->query('q'));
                $query->where(function ($sub) use ($s) {
                    $sub->where('title', 'like', "%{$s}%")
                        ->orWhere('summary', 'like', "%{$s}%")
                        ->orWhere('player_names', 'like', "%{$s}%")
                        ->orWhere('author_name', 'like', "%{$s}%");
                });
            }

            $articles = $query->paginate(9)->withQueryString();

            return view('articles.index', compact('articles', 'categories', 'currentCategory'));

        } catch (Exception $e) {
            Log::error('Error loading articles index: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Unable to load article archive at this time.');
        }
    }

    public function show($slug)
    {
        try {
            $article = Article::with(['category', 'categories', 'newsSource', 'chessGame'])
                ->where('slug', $slug)
                ->firstOrFail();

            // Increment view count safely
            try {
                $article->increment('views_count');
            } catch (Exception $e) {
                // Non-critical, ignore if views count lock fails
            }

            // Ensure ChessGame is loaded or created from article PGN if needed
            $chessGame = $article->chessGame;
            if (!$chessGame && !empty($article->pgn)) {
                $parser = new \App\Services\PgnParserService();
                $parsed = $parser->parse($article->pgn);
                if ($parsed) {
                    $chessGame = \App\Models\ChessGame::create([
                        'article_id' => $article->id,
                        'white' => $parsed['white'] ?? $article->game_white,
                        'black' => $parsed['black'] ?? $article->game_black,
                        'white_elo' => $parsed['white_elo'] ?? null,
                        'black_elo' => $parsed['black_elo'] ?? null,
                        'event' => $parsed['event'] ?? $article->game_event,
                        'site' => $parsed['site'] ?? null,
                        'date' => $parsed['date'] ?? $article->game_date,
                        'round' => $parsed['round'] ?? null,
                        'result' => $parsed['result'] ?? $article->game_result ?? '*',
                        'eco' => $parsed['eco'] ?? null,
                        'starting_fen' => $parsed['starting_fen'] ?? \App\Models\ChessGame::STANDARD_STARTING_FEN,
                        'pgn' => $article->pgn,
                        'moves_san' => $parsed['moves_san'] ?? [],
                        'source_url' => $article->original_url,
                    ]);
                }
            }

            // Structured turns for display and navigation
            $gameTurns = [];
            if ($chessGame && !empty($chessGame->moves_san)) {
                $sanList = $chessGame->moves_san;
                for ($i = 0; $i < count($sanList); $i += 2) {
                    $turnNum = (int)($i / 2) + 1;
                    $gameTurns[] = [
                        'number' => $turnNum,
                        'white' => $sanList[$i] ?? '',
                        'white_ply' => $i + 1,
                        'black' => $sanList[$i + 1] ?? '',
                        'black_ply' => isset($sanList[$i + 1]) ? $i + 2 : null,
                    ];
                }
            }

            $relatedArticles = Article::with(['category', 'newsSource'])
                ->where('id', '!=', $article->id)
                ->where(function ($q) use ($article) {
                    $q->where('category_id', $article->category_id);
                    if (!empty($article->player_names)) {
                        $firstPlayer = explode(',', $article->player_names)[0] ?? null;
                        if ($firstPlayer) {
                            $q->orWhere('player_names', 'like', '%' . trim($firstPlayer) . '%');
                        }
                    }
                })
                ->latest('published_at')
                ->take(3)
                ->get();

            $categories = Category::where('is_active', true)->withCount('articles')->get();

            return view('articles.show', compact('article', 'chessGame', 'gameTurns', 'relatedArticles', 'categories'));

        } catch (Exception $e) {
            Log::error("Error loading article '{$slug}': " . $e->getMessage());
            abort(404, 'Article not found or temporarily unavailable.');
        }
    }

    public function category($slug)
    {
        try {
            $category = Category::where('slug', $slug)->firstOrFail();
            $articles = Article::with(['category', 'newsSource'])
                ->where('category_id', $category->id)
                ->latest('published_at')
                ->paginate(9);

            $categories = Category::where('is_active', true)->withCount('articles')->get();

            return view('category', compact('category', 'articles', 'categories'));

        } catch (Exception $e) {
            Log::error("Error loading category '{$slug}': " . $e->getMessage());
            abort(404, 'Category not found.');
        }
    }

    /**
     * Parse raw PGN string into ordered move turns.
     */
    protected function parsePgnMoves(?string $pgn): array
    {
        if (empty($pgn)) {
            return [];
        }

        // Remove comments { ... }, headers [ ... ], and NAG annotations $1, $2
        $clean = preg_replace('/\{[^}]*\}/', '', $pgn);
        $clean = preg_replace('/\[[^\]]*\]/', '', $clean);
        $clean = preg_replace('/\$\d+/', '', $clean);
        $clean = trim(preg_replace('/\s+/', ' ', $clean));

        // Match moves in format: 1. e4 e5 or 1. e4
        preg_match_all('/(\d+)\.\s*([^\s]+)(?:\s+([^\s]+))?/', $clean, $matches, PREG_SET_ORDER);

        $parsed = [];
        foreach ($matches as $m) {
            $moveNumber = $m[1];
            $whiteMove = $m[2] ?? '';
            $blackMove = $m[3] ?? '';

            // Ignore result indicators like 1-0 or 0-1
            if (in_array($whiteMove, ['1-0', '0-1', '1/2-1/2', '*'])) {
                continue;
            }
            if (in_array($blackMove, ['1-0', '0-1', '1/2-1/2', '*'])) {
                $blackMove = '';
            }

            $parsed[] = [
                'number' => (int)$moveNumber,
                'white' => $whiteMove,
                'black' => $blackMove,
            ];
        }

        return $parsed;
    }
}
