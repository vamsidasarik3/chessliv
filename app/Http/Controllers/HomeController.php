<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tournament;
use App\Models\Puzzle;
use Illuminate\Support\Facades\Log;
use Exception;

class HomeController extends Controller
{
    /**
     * Featured players available for instant filtering.
     */
    protected array $popularPlayers = [
        'Gukesh D',
        'Magnus Carlsen',
        'Hikaru Nakamura',
        'Praggnanandhaa',
        'Fabiano Caruana',
        'Alireza Firouzja',
        'Ding Liren',
        'Bodhana Sivanandan',
    ];

    public function index(Request $request)
    {
        try {
            $categories = Category::where('is_active', true)
                ->withCount('articles')
                ->orderBy('order')
                ->get();

            // Active filters
            $selectedCategory = $request->query('category');
            $selectedPlayer = $request->query('player');
            $searchQuery = trim($request->query('q', ''));
            $activeSection = $request->query('section', 'all');

            // Base query for main news feed - newest first
            $articlesQuery = Article::with(['category', 'newsSource'])
                ->latest('published_at');

            // Apply category filter
            if (!empty($selectedCategory) && $selectedCategory !== 'all') {
                $articlesQuery->whereHas('category', function ($q) use ($selectedCategory) {
                    $q->where('slug', $selectedCategory);
                });
            }

            // Apply player filter
            if (!empty($selectedPlayer)) {
                $articlesQuery->forPlayer($selectedPlayer);
            }

            // Apply search query
            if (!empty($searchQuery)) {
                $articlesQuery->where(function ($q) use ($searchQuery) {
                    $q->where('title', 'like', "%{$searchQuery}%")
                      ->orWhere('summary', 'like', "%{$searchQuery}%")
                      ->orWhere('player_names', 'like', "%{$searchQuery}%")
                      ->orWhere('author_name', 'like', "%{$searchQuery}%")
                      ->orWhere('source_name', 'like', "%{$searchQuery}%");
                });
            }

            // Paginate main feed (10 per page)
            $articles = $articlesQuery->paginate(10)->withQueryString();

            // Featured Hero & Sub-hero articles
            $featuredArticles = Article::with(['category', 'newsSource'])
                ->where('is_featured', true)
                ->latest('published_at')
                ->take(4)
                ->get();

            if ($featuredArticles->isEmpty()) {
                $featuredArticles = Article::with(['category', 'newsSource'])
                    ->latest('published_at')
                    ->take(4)
                    ->get();
            }

            $heroArticle = $featuredArticles->first();
            $subFeatured = $featuredArticles->slice(1, 3);

            // Breaking News headline
            $breakingArticle = Article::where('is_breaking', true)
                ->latest('published_at')
                ->first();

            // Section 1: Latest News (newest 6)
            $sectionLatest = Article::with(['category', 'newsSource'])
                ->latest('published_at')
                ->take(6)
                ->get();

            // Section 2: Tournaments
            $sectionTournaments = Article::with(['category', 'newsSource'])
                ->whereHas('category', function ($q) {
                    $q->where('slug', 'tournaments');
                })
                ->latest('published_at')
                ->take(4)
                ->get();

            // Section 3: Players (Player News & Profiles)
            $sectionPlayers = Article::with(['category', 'newsSource'])
                ->where(function ($q) {
                    $q->whereHas('category', function ($cat) {
                        $cat->where('slug', 'player-profiles');
                    })->orWhereNotNull('player_names');
                })
                ->latest('published_at')
                ->take(4)
                ->get();

            // Section 4: World Championships
            $sectionWorldChamps = Article::with(['category', 'newsSource'])
                ->whereHas('category', function ($q) {
                    $q->where('slug', 'world-championship');
                })
                ->latest('published_at')
                ->take(4)
                ->get();

            // Section 5: Chess Openings (Theory)
            $sectionOpenings = Article::with(['category', 'newsSource'])
                ->whereHas('category', function ($q) {
                    $q->where('slug', 'opening-theory');
                })
                ->latest('published_at')
                ->take(4)
                ->get();

            // Section 6: Game Analysis (Grandmaster Analysis)
            $sectionAnalysis = Article::with(['category', 'newsSource'])
                ->where(function ($q) {
                    $q->whereHas('category', function ($cat) {
                        $cat->where('slug', 'gm-insights');
                    })->orWhereNotNull('pgn');
                })
                ->latest('published_at')
                ->take(4)
                ->get();

            // Sidebar widgets
            $ongoingTournaments = Tournament::where('status', 'ongoing')->take(5)->get();
            $dailyPuzzle = Puzzle::latest()->first();

            $dbError = null;

        } catch (Exception $e) {
            Log::error('Database error in HomeController: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $dbError = 'Our live database is experiencing brief maintenance. Some live news items may be temporarily cached.';

            // Fallback empty collections to prevent 500 crash
            $categories = collect();
            $articles = Article::paginate(10);
            $heroArticle = null;
            $subFeatured = collect();
            $breakingArticle = null;
            $sectionLatest = collect();
            $sectionTournaments = collect();
            $sectionPlayers = collect();
            $sectionWorldChamps = collect();
            $sectionOpenings = collect();
            $sectionAnalysis = collect();
            $ongoingTournaments = collect();
            $dailyPuzzle = null;
            $selectedCategory = null;
            $selectedPlayer = null;
            $searchQuery = '';
        }

        return view('home', [
            'categories' => $categories,
            'articles' => $articles,
            'heroArticle' => $heroArticle,
            'subFeatured' => $subFeatured,
            'breakingArticle' => $breakingArticle,
            'sectionLatest' => $sectionLatest,
            'sectionTournaments' => $sectionTournaments,
            'sectionPlayers' => $sectionPlayers,
            'sectionWorldChamps' => $sectionWorldChamps,
            'sectionOpenings' => $sectionOpenings,
            'sectionAnalysis' => $sectionAnalysis,
            'popularPlayers' => $this->popularPlayers,
            'selectedCategory' => $selectedCategory,
            'selectedPlayer' => $selectedPlayer,
            'searchQuery' => $searchQuery,
            'ongoingTournaments' => $ongoingTournaments,
            'dailyPuzzle' => $dailyPuzzle,
            'dbError' => $dbError,
        ]);
    }

    public function search(Request $request)
    {
        $query = trim($request->input('q', ''));

        if ($request->ajax()) {
            if (empty($query)) {
                return response()->json([]);
            }

            try {
                $results = Article::with('category')
                    ->where(function ($q) use ($query) {
                        $q->where('title', 'like', "%{$query}%")
                          ->orWhere('summary', 'like', "%{$query}%")
                          ->orWhere('player_names', 'like', "%{$query}%")
                          ->orWhereHas('category', function ($cat) use ($query) {
                              $cat->where('name', 'like', "%{$query}%");
                          });
                    })
                    ->latest('published_at')
                    ->take(6)
                    ->get()
                    ->map(function ($article) {
                        return [
                            'title' => $article->title,
                            'url' => route('articles.show', $article->slug),
                            'category' => $article->category->name ?? 'General',
                            'source' => $article->source_name ?: 'ChessLiv',
                            'published' => $article->published_at ? $article->published_at->diffForHumans() : '',
                        ];
                    });

                return response()->json($results);
            } catch (Exception $e) {
                Log::error('Search AJAX error: ' . $e->getMessage());
                return response()->json([], 500);
            }
        }

        return redirect()->route('home', ['q' => $query]);
    }
}
