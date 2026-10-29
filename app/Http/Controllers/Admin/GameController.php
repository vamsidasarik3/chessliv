<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChessGame;
use App\Models\Article;
use App\Services\PgnParserService;
use Illuminate\Support\Facades\DB;
use Exception;

class GameController extends Controller
{
    /**
     * List all games with pagination, search, and article associations.
     */
    public function index(Request $request)
    {
        $query = ChessGame::with('article')->latest();

        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $query->where(function ($q) use ($search) {
                $q->where('white', 'like', "%{$search}%")
                  ->orWhere('black', 'like', "%{$search}%")
                  ->orWhere('event', 'like', "%{$search}%")
                  ->orWhere('site', 'like', "%{$search}%")
                  ->orWhereHas('article', function ($sub) use ($search) {
                      $sub->where('title', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('result')) {
            $query->where('result', $request->input('result'));
        }

        $games = $query->paginate(10)->withQueryString();

        return view('admin.games.index', compact('games'));
    }

    /**
     * Show form for editing and correcting game metadata.
     */
    public function edit($id)
    {
        $game = ChessGame::with('article')->findOrFail($id);
        return view('admin.games.edit', compact('game'));
    }

    /**
     * Update game metadata and sync to associated article.
     */
    public function update(Request $request, $id, PgnParserService $parser)
    {
        $game = ChessGame::with('article')->findOrFail($id);

        $validated = $request->validate([
            'white' => 'required|string|max:255',
            'black' => 'required|string|max:255',
            'white_elo' => 'nullable|string|max:10',
            'black_elo' => 'nullable|string|max:10',
            'event' => 'nullable|string|max:255',
            'site' => 'nullable|string|max:255',
            'date' => 'nullable|string|max:50',
            'round' => 'nullable|string|max:50',
            'result' => 'required|string|in:1-0,0-1,1/2-1/2,*',
            'eco' => 'nullable|string|max:10',
            'starting_fen' => 'nullable|string',
            'pgn' => 'required|string',
            'source_url' => 'nullable|url|max:1000',
        ]);

        // Parse updated PGN to refresh SAN moves and validate
        $parsed = $parser->parse($validated['pgn']);
        $movesSan = $parsed['moves_san'] ?? $game->moves_san ?? [];
        $startingFen = !empty($validated['starting_fen']) 
            ? trim($validated['starting_fen']) 
            : ($parsed['starting_fen'] ?? ChessGame::STANDARD_STARTING_FEN);

        DB::transaction(function () use ($game, $validated, $movesSan, $startingFen) {
            $game->update([
                'white' => $validated['white'],
                'black' => $validated['black'],
                'white_elo' => $validated['white_elo'] ?? null,
                'black_elo' => $validated['black_elo'] ?? null,
                'event' => $validated['event'] ?? null,
                'site' => $validated['site'] ?? null,
                'date' => $validated['date'] ?? null,
                'round' => $validated['round'] ?? null,
                'result' => $validated['result'],
                'eco' => $validated['eco'] ?? null,
                'starting_fen' => $startingFen,
                'pgn' => $validated['pgn'],
                'moves_san' => $movesSan,
                'source_url' => $validated['source_url'] ?? null,
            ]);

            // Sync with associated article
            if ($game->article) {
                $playerNames = trim($validated['white']) . ', ' . trim($validated['black']);
                $game->article->update([
                    'game_white' => $validated['white'],
                    'game_black' => $validated['black'],
                    'game_result' => $validated['result'],
                    'game_event' => $validated['event'] ?? null,
                    'game_date' => $validated['date'] ?? null,
                    'pgn' => $validated['pgn'],
                    'player_names' => $playerNames,
                ]);
            }
        });

        return redirect()->route('admin.games.index')
            ->with('success', "Chess game metadata for '{$game->matchup}' updated successfully.");
    }
}
