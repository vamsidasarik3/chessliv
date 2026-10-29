<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ChessGame;
use App\Models\ImportHistory;
use App\Models\NewsSource;
use App\Models\Category;
use App\Services\PgnParserService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use SimpleXMLElement;
use Exception;

class NewsIngestionService
{
    protected array $config;

    public function __construct(?array $config = null)
    {
        $this->config = $config ?? (function_exists('config') ? (config('chess_sources') ?? []) : []);
    }

    /**
     * Ingest news from all active sources.
     *
     * @param int|null $limitPerFeed
     * @param string $triggerType
     * @return array
     */
    public function ingestAll(?int $limitPerFeed = null, string $triggerType = 'manual'): array
    {
        $sources = NewsSource::where('is_active', true)->get();
        $summary = [
            'total_sources' => $sources->count(),
            'total_imported' => 0,
            'total_skipped' => 0,
            'total_failed' => 0,
            'source_results' => [],
        ];

        $delay = $this->config['request']['rate_limit_delay_seconds'] ?? 1;

        foreach ($sources as $index => $source) {
            if ($index > 0 && $delay > 0) {
                sleep($delay);
            }

            $res = $this->ingestSource($source, $limitPerFeed, $triggerType);
            $summary['total_imported'] += $res['imported'];
            $summary['total_skipped'] += $res['skipped'];
            if (!$res['success']) {
                $summary['total_failed']++;
            }
            $summary['source_results'][$source->slug] = $res;
        }

        return $summary;
    }

    /**
     * Ingest news from a specific source.
     *
     * @param NewsSource $source
     * @param int|null $limit
     * @param string $triggerType
     * @return array
     */
    public function ingestSource(NewsSource $source, ?int $limit = null, string $triggerType = 'manual'): array
    {
        $startTime = microtime(true);
        $startedAt = now();
        $maxItems = $limit ?? ($this->config['request']['max_items_per_feed'] ?? 15);
        $result = [
            'source_id' => $source->id,
            'source_name' => $source->name,
            'success' => false,
            'imported' => 0,
            'skipped' => 0,
            'errors' => [],
            'items' => [],
        ];

        // Create initial running history record
        $history = null;
        try {
            $history = ImportHistory::create([
                'news_source_id' => $source->id,
                'source_name' => $source->name,
                'started_at' => $startedAt,
                'status' => 'running',
                'trigger_type' => $triggerType,
            ]);
        } catch (Exception $e) {
            Log::warning("Could not create initial ImportHistory record: " . $e->getMessage());
        }

        try {
            $feedXml = $this->fetchFeed($source->feed_url);

            if (empty($feedXml)) {
                throw new Exception("Feed response was empty from {$source->feed_url}");
            }

            $items = $this->parseFeedContent($feedXml);

            if (empty($items)) {
                Log::info("No items parsed from feed: {$source->name} ({$source->feed_url})");
            }

            // Process items up to limit
            $items = array_slice($items, 0, $maxItems);

            foreach ($items as $item) {
                try {
                    $itemResult = $this->processFeedItem($item, $source);
                    if ($itemResult['status'] === 'imported') {
                        $result['imported']++;
                    } elseif ($itemResult['status'] === 'skipped') {
                        $result['skipped']++;
                    }
                    $result['items'][] = $itemResult;
                } catch (Exception $e) {
                    $errMsg = "Error processing item '{$item['title']}': " . $e->getMessage();
                    Log::error($errMsg);
                    $result['errors'][] = $errMsg;
                }
            }

            // Update source stats
            $source->update([
                'last_fetched_at' => now(),
                'last_error' => count($result['errors']) > 0 ? implode('; ', array_slice($result['errors'], 0, 3)) : null,
                'items_fetched_count' => $source->items_fetched_count + $result['imported'],
            ]);

            $result['success'] = true;

        } catch (Exception $e) {
            $errMsg = "Feed fetch/parse error for {$source->name}: " . $e->getMessage();
            Log::error($errMsg, ['feed_url' => $source->feed_url, 'trace' => $e->getTraceAsString()]);
            
            $source->update([
                'last_fetched_at' => now(),
                'last_error' => Str::limit($e->getMessage(), 500),
            ]);

            $result['errors'][] = $errMsg;
            $result['success'] = false;
        }

        // Finalize ImportHistory record
        if ($history) {
            $duration = round(microtime(true) - $startTime, 2);
            $finalStatus = $result['success'] 
                ? (count($result['errors']) > 0 ? 'partial' : 'completed') 
                : 'failed';

            try {
                $history->update([
                    'completed_at' => now(),
                    'duration_seconds' => $duration,
                    'status' => $finalStatus,
                    'articles_fetched' => count($result['items']),
                    'articles_imported' => $result['imported'],
                    'duplicates_skipped' => $result['skipped'],
                    'errors_count' => count($result['errors']),
                    'error_messages' => !empty($result['errors']) ? $result['errors'] : null,
                ]);
            } catch (Exception $e) {
                Log::warning("Could not finalize ImportHistory record: " . $e->getMessage());
            }
        }

        return $result;
    }

    /**
     * Fetch feed content with configurable timeouts, retries, and custom user-agent.
     *
     * @param string $url
     * @return string
     * @throws Exception
     */
    public function fetchFeed(string $url): string
    {
        $userAgent = $this->config['request']['user_agent'] ?? 'ChessLivNewsBot/1.0 (+http://chessliv.vkrishna.in:8084/)';
        $timeout = $this->config['request']['timeout'] ?? 15;
        $connectTimeout = $this->config['request']['connect_timeout'] ?? 8;
        $retryLimit = (int)($this->config['request']['retry_limit'] ?? 2);
        $retryDelay = (int)($this->config['request']['retry_delay_ms'] ?? 500);

        $client = Http::withUserAgent($userAgent)
            ->timeout($timeout)
            ->connectTimeout($connectTimeout)
            ->withHeaders([
                'Accept' => 'application/rss+xml, application/atom+xml, application/xml, text/xml, */*',
            ]);

        if ($retryLimit > 0) {
            $client = $client->retry($retryLimit, $retryDelay);
        }

        $response = $client->get($url);

        if (!$response->successful()) {
            throw new Exception("HTTP request failed with status: " . $response->status());
        }

        return $response->body();
    }

    /**
     * Parse RSS 2.0, RSS 1.0, or Atom feed XML.
     *
     * @param string $xmlContent
     * @return array
     * @throws Exception
     */
    public function parseFeedContent(string $xmlContent): array
    {
        // Suppress libxml errors and handle them internally
        libxml_use_internal_errors(true);
        libxml_clear_errors();

        // Strip non-printable control characters that break XML parser
        $cleanXml = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $xmlContent);

        $xml = simplexml_load_string($cleanXml, 'SimpleXMLElement', LIBXML_NOCDATA);

        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            $errStr = !empty($errors) ? trim($errors[0]->message) : 'Malformed XML';
            throw new Exception("XML parsing error: " . $errStr);
        }

        $items = [];

        // Check if Atom Feed (<entry>)
        if (isset($xml->entry)) {
            foreach ($xml->entry as $entry) {
                $items[] = $this->parseAtomEntry($entry);
            }
        }
        // Check if RSS 2.0 (<channel><item>)
        elseif (isset($xml->channel->item)) {
            foreach ($xml->channel->item as $item) {
                $items[] = $this->parseRssItem($item);
            }
        }
        // Check if RSS 1.0 RDF (<item>)
        elseif (isset($xml->item)) {
            foreach ($xml->item as $item) {
                $items[] = $this->parseRssItem($item);
            }
        }

        return $items;
    }

    /**
     * Parse individual RSS item.
     */
    protected function parseRssItem(SimpleXMLElement $item): array
    {
        $namespaces = $item->getNamespaces(true);

        $title = (string)$item->title;
        $link = (string)$item->link;
        $description = (string)$item->description;

        // Content encoded (full content when legally provided in feed)
        $content = '';
        if (isset($namespaces['content'])) {
            $contentNs = $item->children($namespaces['content']);
            if (isset($contentNs->encoded)) {
                $content = (string)$contentNs->encoded;
            }
        }

        // Author
        $author = (string)($item->author ?? '');
        if (empty($author) && isset($namespaces['dc'])) {
            $dc = $item->children($namespaces['dc']);
            if (isset($dc->creator)) {
                $author = (string)$dc->creator;
            }
        }

        // Date
        $publishedAt = null;
        $pubDate = (string)($item->pubDate ?? '');
        if (empty($pubDate) && isset($namespaces['dc'])) {
            $dc = $item->children($namespaces['dc']);
            if (isset($dc->date)) {
                $pubDate = (string)$dc->date;
            }
        }
        if (!empty($pubDate)) {
            try {
                $publishedAt = Carbon::parse($pubDate);
            } catch (Exception $e) {
                $publishedAt = now();
            }
        } else {
            $publishedAt = now();
        }

        // Image URL extraction: enclosure, media:content, media:thumbnail, or img tag
        $imageUrl = null;
        if (isset($item->enclosure) && isset($item->enclosure['url'])) {
            $type = (string)($item->enclosure['type'] ?? '');
            if (str_starts_with($type, 'image/') || empty($type)) {
                $imageUrl = (string)$item->enclosure['url'];
            }
        }

        if (empty($imageUrl) && isset($namespaces['media'])) {
            $media = $item->children($namespaces['media']);
            if (isset($media->content) && isset($media->content->attributes()->url)) {
                $imageUrl = (string)$media->content->attributes()->url;
            } elseif (isset($media->thumbnail) && isset($media->thumbnail->attributes()->url)) {
                $imageUrl = (string)$media->thumbnail->attributes()->url;
            }
        }

        if (empty($imageUrl)) {
            $imageUrl = $this->extractFirstImageFromHtml($content ?: $description);
        }

        return [
            'title' => trim($title),
            'link' => trim($link),
            'summary' => $this->sanitizeSummary($description),
            'content' => $this->sanitizeContent($content ?: $description),
            'author' => trim($author) ?: 'ChessLiv Correspondent',
            'published_at' => $publishedAt,
            'image_url' => $imageUrl,
        ];
    }

    /**
     * Parse individual Atom entry.
     */
    protected function parseAtomEntry(SimpleXMLElement $entry): array
    {
        $title = (string)$entry->title;

        // Atom links can be multiple
        $link = '';
        if (isset($entry->link)) {
            foreach ($entry->link as $l) {
                $rel = (string)($l['rel'] ?? 'alternate');
                if ($rel === 'alternate' || empty($link)) {
                    $link = (string)$l['href'];
                }
            }
        }

        $summary = (string)($entry->summary ?? '');
        $content = (string)($entry->content ?? '');

        // Author
        $author = '';
        if (isset($entry->author) && isset($entry->author->name)) {
            $author = (string)$entry->author->name;
        }

        // Date
        $publishedAt = null;
        $dateStr = (string)($entry->published ?? $entry->updated ?? '');
        if (!empty($dateStr)) {
            try {
                $publishedAt = Carbon::parse($dateStr);
            } catch (Exception $e) {
                $publishedAt = now();
            }
        } else {
            $publishedAt = now();
        }

        $imageUrl = $this->extractFirstImageFromHtml($content ?: $summary);

        return [
            'title' => trim($title),
            'link' => trim($link),
            'summary' => $this->sanitizeSummary($summary ?: $content),
            'content' => $this->sanitizeContent($content ?: $summary),
            'author' => trim($author) ?: 'ChessLiv Correspondent',
            'published_at' => $publishedAt,
            'image_url' => $imageUrl,
        ];
    }

    /**
     * Process a parsed feed item into the database with deduplication.
     *
     * @param array $item
     * @param NewsSource $source
     * @return array
     */
    public function processFeedItem(array $item, NewsSource $source): array
    {
        if (empty($item['title'])) {
            return ['status' => 'skipped', 'reason' => 'Empty title'];
        }

        // 1. Normalize Canonical URL
        $canonicalUrl = $this->normalizeUrl($item['link']);

        // 2. Generate Content Hash
        $contentHash = $this->generateContentHash($item['title'], $item['summary'], $item['content']);

        // 3. Deduplication Check by Canonical URL or Content Hash
        $existingArticle = Article::where(function ($query) use ($canonicalUrl, $contentHash) {
            if (!empty($canonicalUrl)) {
                $query->where('canonical_url', $canonicalUrl);
            }
            if (!empty($contentHash)) {
                $query->orWhere('content_hash', $contentHash);
            }
        })->first();

        if ($existingArticle) {
            // Update fetched_at without duplicating
            $existingArticle->update(['fetched_at' => now()]);

            return [
                'status' => 'skipped',
                'title' => $item['title'],
                'reason' => 'Duplicate detected (existing article ID: ' . $existingArticle->id . ')',
                'article_id' => $existingArticle->id,
            ];
        }

        // 4. Generate unique slug
        $baseSlug = Str::slug($item['title']);
        if (empty($baseSlug)) {
            $baseSlug = 'chess-news-' . time();
        }
        $slug = $baseSlug;
        $counter = 1;
        while (Article::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        // 5. Determine Category
        $categoryId = $source->default_category_id;
        if (!$categoryId) {
            $categoryId = $this->detectCategoryFromContent($item['title'] . ' ' . $item['summary']);
        }

        // Fallback category if still null
        if (!$categoryId) {
            $defaultCat = Category::first();
            $categoryId = $defaultCat ? $defaultCat->id : 1;
        }

        // Calculate read time
        $wordCount = str_word_count(strip_tags($item['content'] ?: $item['summary']));
        $readMinutes = max(1, (int)ceil($wordCount / 200));
        $readTime = $readMinutes . ' min read';

        // 6. Create Article, attach category, and extract game in a single atomic transaction
        return DB::transaction(function () use ($item, $source, $categoryId, $slug, $canonicalUrl, $contentHash, $readTime) {
            $article = Article::create([
                'category_id' => $categoryId,
                'news_source_id' => $source->id,
                'title' => $item['title'],
                'slug' => $slug,
                'summary' => Str::limit(strip_tags($item['summary']), 320),
                'content' => $item['content'] ?: '<p>' . htmlspecialchars($item['summary']) . '</p>',
                'original_url' => $item['link'],
                'canonical_url' => $canonicalUrl,
                'content_hash' => $contentHash,
                'language' => $source->language ?: 'en',
                'processing_status' => 'processed',
                'author_name' => $item['author'] ?: $source->name,
                'author_title' => $source->name,
                'source_name' => $source->name,
                'image_url' => $item['image_url'],
                'read_time' => $readTime,
                'views_count' => 0,
                'is_featured' => false,
                'is_breaking' => false,
                'published_at' => $item['published_at'] ?? now(),
                'fetched_at' => now(),
            ]);

            // Attach category to pivot table
            $article->categories()->syncWithoutDetaching([$categoryId]);

            // Structured chess game extraction (strictly from structured PGN or recognized notation sections, never ordinary prose)
            $pgnParser = new PgnParserService();
            $rawPgn = $item['pgn'] ?? $pgnParser->extractPgnFromContent(($item['content'] ?? '') . "\n" . ($item['summary'] ?? ''));
            if (!empty($rawPgn)) {
                $parsedGame = $pgnParser->parse($rawPgn);
                if ($parsedGame && $parsedGame['has_moves']) {
                    $white = $parsedGame['white'] ?: null;
                    $black = $parsedGame['black'] ?: null;
                    $event = $parsedGame['event'] ?: null;
                    $round = $parsedGame['round'] ?: null;
                    $result = $parsedGame['result'] ?: '*';
                    $startingFen = $parsedGame['starting_fen'] ?: ChessGame::STANDARD_STARTING_FEN;

                    ChessGame::create([
                        'article_id' => $article->id,
                        'white' => $white,
                        'black' => $black,
                        'white_elo' => $parsedGame['white_elo'] ?? null,
                        'black_elo' => $parsedGame['black_elo'] ?? null,
                        'event' => $event,
                        'site' => $parsedGame['site'] ?? null,
                        'date' => $parsedGame['date'] ?? null,
                        'round' => $round,
                        'result' => $result,
                        'eco' => $parsedGame['eco'] ?? null,
                        'starting_fen' => $startingFen,
                        'pgn' => $rawPgn,
                        'moves_san' => $parsedGame['moves_san'] ?? [],
                        'source_url' => $item['link'] ?? null,
                    ]);

                    $article->update([
                        'pgn' => $rawPgn,
                        'game_white' => $white,
                        'game_black' => $black,
                        'game_result' => $result,
                        'game_event' => $event,
                        'game_date' => $parsedGame['date'] ?? null,
                    ]);
                }
            }

            return [
                'status' => 'imported',
                'title' => $item['title'],
                'article_id' => $article->id,
                'slug' => $article->slug,
            ];
        });
    }

    /**
     * Normalize URL for canonical deduplication.
     * Strips fragments, tracking parameters (utm_*, ref, etc.), normalizes scheme and host.
     *
     * @param string $url
     * @return string
     */
    public function normalizeUrl(string $url): string
    {
        $url = trim($url);
        if (empty($url)) {
            return '';
        }

        $parts = parse_url($url);
        if ($parts === false || !isset($parts['host'])) {
            return $url;
        }

        $scheme = strtolower($parts['scheme'] ?? 'https');
        $host = strtolower($parts['host']);
        
        // Remove standard port numbers
        if (isset($parts['port'])) {
            if (($scheme === 'http' && $parts['port'] == 80) || ($scheme === 'https' && $parts['port'] == 443)) {
                unset($parts['port']);
            }
        }
        $port = isset($parts['port']) ? ':' . $parts['port'] : '';

        $path = $parts['path'] ?? '/';
        // Normalize multiple slashes in path
        $path = preg_replace('#/+#', '/', $path);
        // Remove trailing slash if path is longer than '/'
        if (strlen($path) > 1 && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        // Clean query parameters
        $queryString = '';
        if (isset($parts['query'])) {
            parse_str($parts['query'], $queryParams);
            $trackingParams = $this->config['tracking_parameters_to_strip'] ?? [
                'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
                'gclid', 'fbclid', 'ref', 'source', 'rss'
            ];

            foreach ($trackingParams as $param) {
                unset($queryParams[$param]);
            }

            if (!empty($queryParams)) {
                ksort($queryParams);
                $queryString = '?' . http_build_query($queryParams);
            }
        }

        return "{$scheme}://{$host}{$port}{$path}{$queryString}";
    }

    /**
     * Generate content hash for duplicate detection.
     *
     * @param string $title
     * @param string $summary
     * @param string|null $content
     * @return string
     */
    public function generateContentHash(string $title, string $summary, ?string $content = null): string
    {
        $raw = $title . ' ' . $summary;
        if (!empty($content)) {
            $raw .= ' ' . substr(strip_tags($content), 0, 300);
        }

        // Normalize text: lowercase, remove tags, decode entities, strip spaces
        $normalized = mb_strtolower(trim(html_entity_decode(strip_tags($raw), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        return hash('sha256', $normalized);
    }

    /**
     * Sanitize feed summary/description.
     */
    protected function sanitizeSummary(string $html): string
    {
        $text = strip_tags($html);
        return trim(preg_replace('/\s+/', ' ', html_entity_decode($text, ENT_QUOTES, 'UTF-8')));
    }

    /**
     * Sanitize article content, preserving safe formatting while removing scripts.
     */
    protected function sanitizeContent(string $html): string
    {
        // Strip dangerous tags: script, style, iframe, object, embed
        $clean = preg_replace('#<(script|style|iframe|object|embed)[^>]*?>.*?</\\1>#si', '', $html);
        // Strip on* event attributes
        $clean = preg_replace('#\s+on[a-z]+=["\'][^"\']*["\']#i', '', $clean);
        return trim($clean);
    }

    /**
     * Extract first image from HTML if present.
     */
    protected function extractFirstImageFromHtml(string $html): ?string
    {
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $matches)) {
            $src = trim($matches[1]);
            if (filter_var($src, FILTER_VALIDATE_URL)) {
                return $src;
            }
        }
        return null;
    }

    /**
     * Detect matching category based on keywords.
     */
    protected function detectCategoryFromContent(string $text): int
    {
        $text = strtolower($text);

        if (str_contains($text, 'candidate') || str_contains($text, 'world championship') || str_contains($text, 'ding') || str_contains($text, 'crown')) {
            $cat = Category::where('slug', 'world-championship')->first();
            if ($cat) return $cat->id;
        }

        if (str_contains($text, 'opening') || str_contains($text, 'defense') || str_contains($text, 'gambit') || str_contains($text, 'sicilian')) {
            $cat = Category::where('slug', 'opening-theory')->first();
            if ($cat) return $cat->id;
        }

        if (str_contains($text, 'tournament') || str_contains($text, 'open') || str_contains($text, 'grand slam') || str_contains($text, 'round')) {
            $cat = Category::where('slug', 'tournaments')->first();
            if ($cat) return $cat->id;
        }

        if (str_contains($text, 'tactic') || str_contains($text, 'puzzle') || str_contains($text, 'mate') || str_contains($text, 'blunder')) {
            $cat = Category::where('slug', 'tactics-puzzles')->first();
            if ($cat) return $cat->id;
        }

        if (str_contains($text, 'interview') || str_contains($text, 'profile') || str_contains($text, 'prodigy') || str_contains($text, 'rating')) {
            $cat = Category::where('slug', 'player-profiles')->first();
            if ($cat) return $cat->id;
        }

        $default = Category::where('slug', 'gm-insights')->first() ?? Category::first();
        return $default ? $default->id : 1;
    }
}
