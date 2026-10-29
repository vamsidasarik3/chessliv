<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Article;
use App\Models\Category;
use App\Models\ChessGame;
use App\Models\NewsSource;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChessGameViewerTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;
    protected NewsSource $source;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::firstOrCreate(
            ['slug' => 'game-analysis'],
            ['name' => 'Game Analysis', 'icon' => 'bi-graph-up', 'is_active' => true]
        );

        $this->source = NewsSource::firstOrCreate(
            ['slug' => 'fide-news'],
            ['name' => 'FIDE Official', 'feed_url' => 'https://fide.com/rss', 'is_active' => true]
        );
    }

    /**
     * Test article with valid game displays board, controls, players, and notation list.
     */
    public function test_article_page_displays_interactive_chessboard_and_controls(): void
    {
        $article = Article::create([
            'category_id' => $this->category->id,
            'news_source_id' => $this->source->id,
            'title' => 'Feature Test Candidates Round 10 Game',
            'slug' => 'feature-test-candidates-round-10-game',
            'summary' => 'Comprehensive master game review.',
            'content' => '<p>Master game analysis content.</p>',
            'author_name' => 'ChessLiv GM Analyst',
            'processing_status' => 'processed',
        ]);

        $pgn = <<<PGN
[Event "FIDE Candidates 2024"]
[Site "Toronto"]
[Date "2024.04.14"]
[Round "10"]
[White "Gukesh D"]
[Black "Firouzja, Alireza"]
[Result "1-0"]
[WhiteElo "2743"]
[BlackElo "2760"]
[ECO "D38"]

1. d4 Nf6 2. c4 e6 3. Nf3 d5 4. Nc3 Bb4 5. cxd5 exd5 1-0
PGN;

        $game = ChessGame::create([
            'article_id' => $article->id,
            'white' => 'Gukesh D',
            'black' => 'Firouzja, Alireza',
            'white_elo' => '2743',
            'black_elo' => '2760',
            'event' => 'FIDE Candidates 2024',
            'site' => 'Toronto',
            'date' => '2024.04.14',
            'round' => '10',
            'result' => '1-0',
            'eco' => 'D38',
            'starting_fen' => ChessGame::STANDARD_STARTING_FEN,
            'pgn' => $pgn,
            'moves_san' => ['d4', 'Nf6', 'c4', 'e6', 'Nf3', 'd5', 'Nc3', 'Bb4', 'cxd5', 'exd5'],
            'source_url' => 'https://chessliv.example.com/games/10',
        ]);

        $response = $this->get(route('articles.show', $article->slug));

        $response->assertStatus(200);
        $response->assertSee('Gukesh D');
        $response->assertSee('Firouzja, Alireza');
        $response->assertSee('FIDE Candidates 2024');
        $response->assertSee('Round 10');
        $response->assertSee('Result: 1-0');

        // Verify board and navigation controls
        $response->assertSee('id="chesslivBoard"', false);
        $response->assertSee('id="btnMoveStart"', false);
        $response->assertSee('id="btnMovePrev"', false);
        $response->assertSee('id="btnMoveNext"', false);
        $response->assertSee('id="btnMoveEnd"', false);
        $response->assertSee('id="btnFlipBoard"', false);
        $response->assertSee('id="boardMoveIndicator"', false);
        $response->assertSee('id="boardTurnBadge"', false);
        $response->assertSee('id="pgnMoveList"', false);

        // Verify moves notation
        $response->assertSee('data-ply="1"', false);
        $response->assertSee('d4');
        $response->assertSee('Nf6');

        // Verify chess.js and chessboard.js library assets are loaded
        $response->assertSee('chess.min.js');
        $response->assertSee('chessboard.min.js');
        $response->assertSee('chessboard.min.css');

        // Verify NO React or Vue
        $response->assertDontSee('react.production.min.js');
        $response->assertDontSee('vue.global.prod.js');
        $response->assertDontSee('react-dom');
    }

    /**
     * Test article without game displays clear "No game moves available" message.
     */
    public function test_article_without_game_displays_no_game_moves_available_message(): void
    {
        $article = Article::create([
            'category_id' => $this->category->id,
            'news_source_id' => $this->source->id,
            'title' => 'General Chess Federation Announcement',
            'slug' => 'general-chess-federation-announcement',
            'summary' => 'FIDE announced dates for next year congress.',
            'content' => '<p>Congress details without any moves.</p>',
            'author_name' => 'ChessLiv Editor',
            'processing_status' => 'processed',
        ]);

        $response = $this->get(route('articles.show', $article->slug));

        $response->assertStatus(200);
        $response->assertSee('No game moves available');
        $response->assertDontSee('id="chesslivBoard"', false);
        $response->assertDontSee('id="btnMoveStart"', false);
    }

    /**
     * Test article with invalid or unparseable PGN handles gracefully and displays no game moves message.
     */
    public function test_article_with_invalid_pgn_handles_gracefully(): void
    {
        $article = Article::create([
            'category_id' => $this->category->id,
            'news_source_id' => $this->source->id,
            'title' => 'Article with Corrupt PGN Test',
            'slug' => 'article-with-corrupt-pgn-test',
            'summary' => 'Article having garbage instead of real chess PGN.',
            'content' => '<p>Some content</p>',
            'pgn' => 'Not a chess game at all, just invalid gibberish! &%$#@',
            'author_name' => 'ChessLiv Tester',
            'processing_status' => 'processed',
        ]);

        $response = $this->get(route('articles.show', $article->slug));

        $response->assertStatus(200);
        $response->assertSee('No game moves available');
        $response->assertDontSee('id="chesslivBoard"', false);
    }

    /**
     * Test ChessGame model relationships and accessors.
     */
    public function test_chess_game_model_relationships_and_accessors(): void
    {
        $article = Article::create([
            'category_id' => $this->category->id,
            'news_source_id' => $this->source->id,
            'title' => 'Model Relationship Test Article',
            'slug' => 'model-relationship-test-article',
            'summary' => 'Testing ChessGame relationships.',
            'content' => '<p>Test</p>',
            'author_name' => 'Tester',
            'processing_status' => 'processed',
        ]);

        $game = ChessGame::create([
            'article_id' => $article->id,
            'white' => 'Magnus Carlsen',
            'black' => 'Hikaru Nakamura',
            'white_elo' => '2830',
            'black_elo' => '2875',
            'event' => 'Speed Chess Championship',
            'result' => '1-0',
            'starting_fen' => ChessGame::STANDARD_STARTING_FEN,
            'pgn' => '1. e4 c5 2. Nf3 1-0',
            'moves_san' => ['e4', 'c5', 'Nf3'],
        ]);

        $this->assertEquals($article->id, $game->article->id);
        $this->assertEquals($game->id, $article->fresh()->chessGame->id);
        $this->assertEquals('Magnus Carlsen vs Hikaru Nakamura', $game->matchup);
        $this->assertEquals(3, $game->move_count);
        $this->assertTrue($game->hasMoves());
        $this->assertEquals(ChessGame::STANDARD_STARTING_FEN, $game->effective_starting_fen);
    }
}
