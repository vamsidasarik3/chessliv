<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Article;
use App\Models\NewsSource;
use App\Models\ChessGame;
use App\Models\ImportHistory;
use App\Services\NewsIngestionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

class AdminReliabilityTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;
    protected Category $category;
    protected NewsSource $source;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@chessliv.com',
            'role' => 'admin',
        ]);

        $this->regularUser = User::factory()->create([
            'email' => 'reader@chessliv.com',
            'role' => 'user',
        ]);

        $this->category = Category::create([
            'name' => 'Tournaments',
            'slug' => 'tournaments',
            'icon' => 'bi-trophy',
            'order' => 1,
            'is_active' => true,
        ]);

        $this->source = NewsSource::create([
            'name' => 'FIDE News',
            'slug' => 'fide-news',
            'feed_url' => 'https://fide.com/news/rss',
            'is_active' => true,
            'fetch_frequency_minutes' => 60,
        ]);
    }

    /**
     * Test access control: unauthenticated users are redirected to login.
     */
    public function test_unauthenticated_users_redirected_to_admin_login(): void
    {
        $protectedRoutes = [
            route('admin.ingestion.index'),
            route('admin.history.index'),
            route('admin.categories.index'),
            route('admin.games.index'),
            route('admin.health.index'),
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect(route('admin.login'));
        }
    }

    /**
     * Test access control: regular users are denied access.
     */
    public function test_regular_users_are_denied_access_to_admin_console(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.ingestion.index'));
        $response->assertRedirect(route('admin.login'));
    }

    /**
     * Test admin user has full access to all admin management modules.
     */
    public function test_admin_user_can_access_all_admin_modules(): void
    {
        $this->actingAs($this->admin);

        // Ingestion & feeds
        $res1 = $this->get(route('admin.ingestion.index'));
        $res1->assertStatus(200);
        $res1->assertSee('Chess News Ingestion');

        // Import History & Failures
        $res2 = $this->get(route('admin.history.index'));
        $res2->assertStatus(200);
        $res2->assertSee('Import History &amp; Failure Review', false);

        // Category Management
        $res3 = $this->get(route('admin.categories.index'));
        $res3->assertStatus(200);
        $res3->assertSee('Editorial Category Management');
        $res3->assertSee('Tournaments');

        // Games Metadata
        $res4 = $this->get(route('admin.games.index'));
        $res4->assertStatus(200);
        $res4->assertSee('Chess Game Metadata Correction');

        // Health Status
        $res5 = $this->get(route('admin.health.index'));
        $res5->assertStatus(200);
        $res5->assertSee('System Health &amp; Reliability Status', false);
        $res5->assertSee('Database Connectivity');
    }

    /**
     * Test admin can create, update, toggle, and delete categories safely.
     */
    public function test_admin_can_manage_categories(): void
    {
        $this->actingAs($this->admin);

        // 1. Create Category
        $response = $this->post(route('admin.categories.store'), [
            'name' => 'Tactics and Studies',
            'slug' => 'tactics-studies',
            'icon' => 'bi-puzzle',
            'description' => 'Daily puzzle analysis.',
            'order' => 5,
            'is_active' => 1,
        ]);
        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['slug' => 'tactics-studies']);

        $category = Category::where('slug', 'tactics-studies')->first();

        // 2. Update Category
        $updateRes = $this->put(route('admin.categories.update', $category->id), [
            'name' => 'Tactics and Studies Updated',
            'slug' => 'tactics-studies-updated',
            'icon' => 'bi-puzzle-fill',
            'description' => 'Updated description.',
            'order' => 8,
            'is_active' => 1,
        ]);
        $updateRes->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['name' => 'Tactics and Studies Updated']);

        // 3. Toggle Status
        $toggleRes = $this->post(route('admin.categories.toggle', $category->id));
        $toggleRes->assertRedirect(route('admin.categories.index'));
        $this->assertFalse($category->fresh()->is_active);

        // 4. Safe Delete with Article Reassignment
        $article = Article::create([
            'category_id' => $category->id,
            'news_source_id' => $this->source->id,
            'title' => 'Article to be reassigned',
            'slug' => 'article-to-be-reassigned',
            'summary' => 'Summary',
            'content' => '<p>Content</p>',
            'author_name' => 'Admin',
            'processing_status' => 'processed',
        ]);

        $deleteRes = $this->delete(route('admin.categories.destroy', $category->id));
        $deleteRes->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        
        // Confirm article was reassigned to the remaining default category
        $this->assertEquals($this->category->id, $article->fresh()->category_id);
    }

    /**
     * Test admin can correct chess game metadata and sync to article.
     */
    public function test_admin_can_correct_game_metadata_and_sync_to_article(): void
    {
        $this->actingAs($this->admin);

        $article = Article::create([
            'category_id' => $this->category->id,
            'news_source_id' => $this->source->id,
            'title' => 'Candidates Round 11 Analysis',
            'slug' => 'candidates-round-11-analysis',
            'summary' => 'Summary',
            'content' => '<p>Content</p>',
            'author_name' => 'Admin',
            'processing_status' => 'processed',
            'game_white' => 'Initial White',
            'game_black' => 'Initial Black',
            'game_result' => '*',
            'pgn' => '1. e4 e5 *',
        ]);

        $game = ChessGame::create([
            'article_id' => $article->id,
            'white' => 'Initial White',
            'black' => 'Initial Black',
            'event' => 'Initial Event',
            'result' => '*',
            'starting_fen' => ChessGame::STANDARD_STARTING_FEN,
            'pgn' => '1. e4 e5 *',
            'moves_san' => ['e4', 'e5'],
        ]);

        // Edit page loads
        $editPage = $this->get(route('admin.games.edit', $game->id));
        $editPage->assertStatus(200);
        $editPage->assertSee('Initial White');

        // Submit corrected metadata
        $correctedPgn = <<<PGN
[Event "FIDE Candidates 2024"]
[Round "11"]
[White "Gukesh D"]
[Black "Nakamura, Hikaru"]
[Result "1-0"]

1. d4 Nf6 2. c4 e6 3. Nf3 d5 1-0
PGN;

        $updateRes = $this->put(route('admin.games.update', $game->id), [
            'white' => 'Gukesh D',
            'black' => 'Nakamura, Hikaru',
            'white_elo' => '2743',
            'black_elo' => '2789',
            'event' => 'FIDE Candidates 2024',
            'round' => '11',
            'result' => '1-0',
            'eco' => 'D38',
            'pgn' => $correctedPgn,
        ]);

        $updateRes->assertRedirect(route('admin.games.index'));

        // Verify chess_games table updated
        $gameFresh = $game->fresh();
        $this->assertEquals('Gukesh D', $gameFresh->white);
        $this->assertEquals('Nakamura, Hikaru', $gameFresh->black);
        $this->assertEquals('1-0', $gameFresh->result);
        $this->assertEquals(['d4', 'Nf6', 'c4', 'e6', 'Nf3', 'd5'], $gameFresh->moves_san);

        // Verify synced with parent article
        $articleFresh = $article->fresh();
        $this->assertEquals('Gukesh D', $articleFresh->game_white);
        $this->assertEquals('Nakamura, Hikaru', $articleFresh->game_black);
        $this->assertEquals('1-0', $articleFresh->game_result);
        $this->assertEquals('FIDE Candidates 2024', $articleFresh->game_event);
        $this->assertStringContainsString('Gukesh D', $articleFresh->player_names);
    }

    /**
     * Test import history logging on ingestion execution.
     */
    public function test_import_history_is_recorded_when_feed_is_ingested(): void
    {
        $mockXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
    <channel>
        <title>FIDE News</title>
        <link>https://fide.com</link>
        <item>
            <title>Gukesh Clinches Candidates Lead</title>
            <link>https://fide.com/news/gukesh-clinches-lead</link>
            <description>Historic victory in round 10 of candidates.</description>
            <pubDate>Mon, 14 Oct 2026 10:00:00 GMT</pubDate>
        </item>
    </channel>
</rss>
XML;

        Http::fake([
            'https://fide.com/news/rss' => Http::response($mockXml, 200),
        ]);

        $service = new NewsIngestionService();
        $res = $service->ingestSource($this->source, 5, 'admin');

        $this->assertTrue($res['success']);
        $this->assertEquals(1, $res['imported']);

        // Check ImportHistory table
        $this->assertDatabaseHas('import_histories', [
            'news_source_id' => $this->source->id,
            'source_name' => 'FIDE News',
            'status' => 'completed',
            'articles_imported' => 1,
            'trigger_type' => 'admin',
        ]);

        $history = ImportHistory::where('news_source_id', $this->source->id)->first();
        $this->assertNotNull($history->completed_at);
        $this->assertGreaterThanOrEqual(0, $history->duration_seconds);
    }

    /**
     * Test failed import records error trace into import_histories.
     */
    public function test_failed_import_records_error_trace_in_import_histories(): void
    {
        Http::fake([
            'https://fide.com/news/rss' => Http::response('Server Internal Error', 500),
        ]);

        $service = new NewsIngestionService();
        $res = $service->ingestSource($this->source, 5, 'admin');

        $this->assertFalse($res['success']);
        $this->assertNotEmpty($res['errors']);

        // Check ImportHistory recorded failure
        $this->assertDatabaseHas('import_histories', [
            'news_source_id' => $this->source->id,
            'status' => 'failed',
            'articles_imported' => 0,
        ]);

        $history = ImportHistory::where('news_source_id', $this->source->id)->latest('started_at')->first();
        $this->assertNotNull($history->error_messages);
        $this->assertGreaterThan(0, $history->errors_count);
    }

    /**
     * Test system health status check.
     */
    public function test_system_health_status_page_returns_positive_metrics(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.health.index'));

        $response->assertStatus(200);
        $response->assertSee('Database Connectivity');
        $response->assertSee('News Sources Health');
        $response->assertSee('Background Queue &amp; Failed Jobs', false);
        $response->assertSee('Local Assets &amp; Runtime Environment', false);
        $response->assertSee('chess.js (v0.10.3)');
    }
}
