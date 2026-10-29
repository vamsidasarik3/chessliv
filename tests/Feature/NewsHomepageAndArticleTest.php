<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Category;
use App\Models\Article;
use App\Models\NewsSource;

class NewsHomepageAndArticleTest extends TestCase
{
    use RefreshDatabase;

    protected Category $categoryTournaments;
    protected Category $categoryWorldMatch;
    protected NewsSource $source;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryTournaments = Category::create([
            'name' => 'Tournaments',
            'slug' => 'tournaments',
            'icon' => 'bi-trophy',
            'order' => 1,
        ]);

        $this->categoryWorldMatch = Category::create([
            'name' => 'World Championship',
            'slug' => 'world-championship',
            'icon' => 'bi-award',
            'order' => 2,
        ]);

        $this->source = NewsSource::create([
            'name' => 'FIDE News',
            'slug' => 'fide-news',
            'feed_url' => 'https://fide.com/rss',
            'site_url' => 'https://fide.com',
        ]);
    }

    /**
     * Test homepage renders newest articles first with headline, summary, category, source, and date.
     */
    public function test_homepage_shows_newest_articles_first_with_metadata(): void
    {
        $oldArticle = Article::create([
            'category_id' => $this->categoryTournaments->id,
            'news_source_id' => $this->source->id,
            'title' => 'Older Tournament Report',
            'slug' => 'older-tournament-report',
            'summary' => 'Summary of older tournament report.',
            'content' => '<p>Content older.</p>',
            'source_name' => 'FIDE News',
            'published_at' => now()->subDays(5),
        ]);

        $newArticle = Article::create([
            'category_id' => $this->categoryWorldMatch->id,
            'news_source_id' => $this->source->id,
            'title' => 'Brand New Candidates Victory',
            'slug' => 'brand-new-candidates-victory',
            'summary' => 'Summary of brand new candidates victory.',
            'content' => '<p>Content newer.</p>',
            'source_name' => 'FIDE News',
            'published_at' => now()->subHour(),
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Brand New Candidates Victory');
        $response->assertSee('Older Tournament Report');
        $response->assertSee('FIDE News');
        $response->assertSee('World Championship');
    }

    /**
     * Test player filter filters articles mentioning that player.
     */
    public function test_homepage_filters_by_player_name(): void
    {
        Article::create([
            'category_id' => $this->categoryWorldMatch->id,
            'title' => 'Gukesh Wins Round 10 Masterclass',
            'slug' => 'gukesh-wins-round-10',
            'player_names' => 'Gukesh D, Alireza Firouzja',
            'summary' => 'Gukesh played brilliant chess.',
            'content' => '<p>Game report.</p>',
            'published_at' => now(),
        ]);

        Article::create([
            'category_id' => $this->categoryTournaments->id,
            'title' => 'Carlsen Sweeps Blitz Arena',
            'slug' => 'carlsen-sweeps-blitz',
            'player_names' => 'Magnus Carlsen',
            'summary' => 'Carlsen dominates the arena.',
            'content' => '<p>Carlsen report.</p>',
            'published_at' => now(),
        ]);

        $response = $this->get(route('home', ['player' => 'Gukesh D']));

        $response->assertStatus(200);
        $response->assertSee('Gukesh Wins Round 10 Masterclass');
        $response->assertViewHas('articles', function ($articles) {
            return $articles->contains('title', 'Gukesh Wins Round 10 Masterclass')
                && !$articles->contains('title', 'Carlsen Sweeps Blitz Arena');
        });
    }

    /**
     * Test empty state when filter yields no results.
     */
    public function test_homepage_shows_empty_state_when_no_results(): void
    {
        $response = $this->get(route('home', ['q' => 'NonExistentGrandmasterXYZ']));

        $response->assertStatus(200);
        $response->assertSee('No articles match your criteria');
        $response->assertSee('Clear All Filters');
    }

    /**
     * Test article detail page displays content, attribution, and associated chess game.
     */
    public function test_article_detail_displays_game_and_attribution(): void
    {
        $pgn = '1. e4 e5 2. Nf3 Nc6 3. Bb5 a6 4. Ba4 Nf6 5. O-O Be7 1-0';

        $article = Article::create([
            'category_id' => $this->categoryWorldMatch->id,
            'news_source_id' => $this->source->id,
            'title' => 'Detailed Game 14 Analysis with PGN',
            'slug' => 'detailed-game-14-analysis',
            'summary' => 'In-depth breakdown of game 14.',
            'content' => '<p>Grandmaster commentary on move 12 novelty.</p>',
            'original_url' => 'https://fide.com/news/game-14-official',
            'source_name' => 'FIDE News',
            'game_white' => 'Gukesh D',
            'game_black' => 'Ding Liren',
            'game_result' => '1-0',
            'game_event' => 'FIDE World Championship 2024',
            'game_date' => '2024-12-12',
            'pgn' => $pgn,
            'published_at' => now(),
        ]);

        $response = $this->get(route('articles.show', $article->slug));

        $response->assertStatus(200);
        $response->assertSee('Detailed Game 14 Analysis with PGN');
        $response->assertSee('Associated Master Game');
        $response->assertSee('Gukesh D vs Ding Liren');
        $response->assertSee('Result: 1-0');
        $response->assertSee('Read Original Article');
        $response->assertSee('https://fide.com/news/game-14-official');
    }
}
