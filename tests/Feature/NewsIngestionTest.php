<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\NewsSource;
use App\Models\Category;
use App\Models\Article;
use App\Services\NewsIngestionService;

class NewsIngestionTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;
    protected NewsSource $source;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name' => 'World Championship',
            'slug' => 'world-championship',
            'icon' => 'bi-award',
            'order' => 1,
        ]);

        $this->source = NewsSource::create([
            'name' => 'FIDE Test Feed',
            'slug' => 'fide-test',
            'feed_url' => 'https://fide.com/test-rss.xml',
            'site_url' => 'https://fide.com',
            'default_category_id' => $this->category->id,
            'is_active' => true,
        ]);
    }

    /**
     * Test successful feed ingestion and article creation.
     */
    public function test_ingests_feed_and_creates_articles(): void
    {
        $mockRss = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
    <channel>
        <title>FIDE News</title>
        <item>
            <title>Candidates 2026: Gukesh Takes Lead</title>
            <link>https://fide.com/news/candidates-round-9?utm_source=rss</link>
            <description>Gukesh secured a brilliant tactical win.</description>
            <pubDate>Wed, 14 Oct 2026 08:00:00 +0000</pubDate>
        </item>
    </channel>
</rss>
XML;

        Http::fake([
            'https://fide.com/test-rss.xml*' => Http::response($mockRss, 200),
        ]);

        $service = app(NewsIngestionService::class);
        $result = $service->ingestSource($this->source);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['imported']);
        $this->assertEquals(0, $result['skipped']);

        $this->assertDatabaseHas('articles', [
            'title' => 'Candidates 2026: Gukesh Takes Lead',
            'canonical_url' => 'https://fide.com/news/candidates-round-9',
            'news_source_id' => $this->source->id,
        ]);

        $this->source->refresh();
        $this->assertEquals(1, $this->source->items_fetched_count);
        $this->assertNotNull($this->source->last_fetched_at);
        $this->assertNull($this->source->last_error);
    }

    /**
     * Test duplicate detection by canonical URL.
     */
    public function test_skips_duplicates_by_canonical_url(): void
    {
        $mockRss = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
    <channel>
        <item>
            <title>Article One</title>
            <link>https://theweekinchess.com/event/1?utm_source=twitter</link>
            <description>Event recap summary</description>
        </item>
    </channel>
</rss>
XML;

        Http::fake([
            'https://fide.com/test-rss.xml*' => Http::response($mockRss, 200),
        ]);

        $service = app(NewsIngestionService::class);

        // First ingestion: imports 1
        $res1 = $service->ingestSource($this->source);
        $this->assertEquals(1, $res1['imported']);

        // Second ingestion: skips duplicate
        $res2 = $service->ingestSource($this->source);
        $this->assertEquals(0, $res2['imported']);
        $this->assertEquals(1, $res2['skipped']);

        $this->assertEquals(1, Article::where('canonical_url', 'https://theweekinchess.com/event/1')->count());
    }

    /**
     * Test duplicate detection by content hash when URL differs.
     */
    public function test_skips_duplicates_by_content_hash(): void
    {
        $service = app(NewsIngestionService::class);

        $title = 'Pragg Wins Tata Steel Blitz';
        $summary = 'Incredible endgame technique by the Indian GM.';
        $contentHash = $service->generateContentHash($title, $summary, $summary);

        Article::create([
            'category_id' => $this->category->id,
            'news_source_id' => $this->source->id,
            'title' => $title,
            'slug' => 'pragg-wins-tata-steel-blitz',
            'summary' => $summary,
            'content' => '<p>' . $summary . '</p>',
            'canonical_url' => 'https://example.com/original-url',
            'content_hash' => $contentHash,
            'processing_status' => 'processed',
        ]);

        $feedItem = [
            'title' => $title,
            'link' => 'https://mirror.com/different-url',
            'summary' => $summary,
            'content' => $summary,
            'author' => 'Reporter',
            'image_url' => null,
            'published_at' => now(),
        ];

        $res = $service->processFeedItem($feedItem, $this->source);

        $this->assertEquals('skipped', $res['status']);
        $this->assertStringContainsString('Duplicate detected', $res['reason']);
    }

    /**
     * Test HTTP failure updates source last_error and returns false.
     */
    public function test_handles_http_failure_gracefully(): void
    {
        Http::fake([
            'https://fide.com/test-rss.xml*' => Http::response('Gateway Timeout', 504),
        ]);

        $service = app(NewsIngestionService::class);
        $res = $service->ingestSource($this->source);

        $this->assertFalse($res['success']);
        $this->assertEquals(0, $res['imported']);
        $this->assertNotEmpty($res['errors']);

        $this->source->refresh();
        $this->assertNotNull($this->source->last_error);
    }

    /**
     * Test unauthenticated users are redirected from admin ingestion dashboard.
     */
    public function test_admin_ingestion_screen_requires_auth(): void
    {
        $response = $this->get(route('admin.ingestion.index'));
        $response->assertRedirect(route('admin.login'));
    }

    /**
     * Test admin user can access the ingestion screen.
     */
    public function test_admin_user_can_access_ingestion_screen(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@chessliv.com',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.ingestion.index'));
        $response->assertStatus(200);
        $response->assertSee('News Ingestion &amp; Syndication', false);
        $response->assertSee($this->source->name);
    }

    /**
     * Test Artisan import command execution.
     */
    public function test_artisan_import_command_runs(): void
    {
        $mockRss = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
    <channel>
        <item>
            <title>Artisan Command Import Test</title>
            <link>https://fide.com/news/cli-test</link>
            <description>Testing CLI execution</description>
        </item>
    </channel>
</rss>
XML;

        Http::fake([
            'https://fide.com/test-rss.xml*' => Http::response($mockRss, 200),
        ]);

        $this->artisan('chessliv:import-feeds', ['--source' => 'fide-test'])
            ->expectsOutputToContain('Starting ChessLiv news feed ingestion')
            ->assertExitCode(0);

        $this->assertDatabaseHas('articles', [
            'title' => 'Artisan Command Import Test',
        ]);
    }
}
