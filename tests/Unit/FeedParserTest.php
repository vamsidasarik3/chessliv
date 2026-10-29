<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\NewsIngestionService;
use Exception;

class FeedParserTest extends TestCase
{
    protected NewsIngestionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NewsIngestionService();
    }

    /**
     * Test RSS 2.0 Feed Parsing.
     */
    public function test_parses_valid_rss_feed(): void
    {
        $rssXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:dc="http://purl.org/dc/elements/1.1/">
    <channel>
        <title>FIDE Chess News</title>
        <link>https://fide.com</link>
        <description>Official Chess News</description>
        <item>
            <title>Gukesh Defends Crown in Candidates Round 10</title>
            <link>https://fide.com/news/1234?utm_source=feed&amp;utm_medium=rss</link>
            <description>A tactical victory in 42 moves.</description>
            <content:encoded><![CDATA[<p>Full grandmaster review of the game.</p>]]></content:encoded>
            <dc:creator>GM Vladimir Kramnik</dc:creator>
            <pubDate>Wed, 14 Oct 2026 12:00:00 +0000</pubDate>
            <enclosure url="https://fide.com/images/gukesh.jpg" type="image/jpeg" />
        </item>
    </channel>
</rss>
XML;

        $items = $this->service->parseFeedContent($rssXml);

        $this->assertCount(1, $items);
        $this->assertEquals('Gukesh Defends Crown in Candidates Round 10', $items[0]['title']);
        $this->assertEquals('https://fide.com/news/1234?utm_source=feed&utm_medium=rss', $items[0]['link']);
        $this->assertEquals('A tactical victory in 42 moves.', $items[0]['summary']);
        $this->assertEquals('GM Vladimir Kramnik', $items[0]['author']);
        $this->assertEquals('https://fide.com/images/gukesh.jpg', $items[0]['image_url']);
    }

    /**
     * Test Atom Feed Parsing.
     */
    public function test_parses_valid_atom_feed(): void
    {
        $atomXml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
    <title>ChessBase News</title>
    <link href="https://en.chessbase.com"/>
    <entry>
        <title>Magnus Carlsen Wins Freestyle Grand Slam</title>
        <link rel="alternate" href="https://en.chessbase.com/post/magnus-freestyle-paris"/>
        <summary>Carlsen was unbeatable in the rapid playoffs.</summary>
        <content type="html"><![CDATA[<p>Comprehensive tournament report with PGNs.</p>]]></content>
        <author>
            <name>Andre Schulz</name>
        </author>
        <published>2026-10-14T10:30:00Z</published>
    </entry>
</feed>
XML;

        $items = $this->service->parseFeedContent($atomXml);

        $this->assertCount(1, $items);
        $this->assertEquals('Magnus Carlsen Wins Freestyle Grand Slam', $items[0]['title']);
        $this->assertEquals('https://en.chessbase.com/post/magnus-freestyle-paris', $items[0]['link']);
        $this->assertEquals('Andre Schulz', $items[0]['author']);
    }

    /**
     * Test URL Normalization removes tracking params and standardizes URLs.
     */
    public function test_normalizes_url_and_strips_tracking(): void
    {
        $rawUrl = 'https://theweekinchess.com/chessnews/events/candidates-2026/?utm_source=twitter&utm_medium=social&utm_campaign=round9&ref=rss#header';
        $normalized = $this->service->normalizeUrl($rawUrl);

        $this->assertEquals('https://theweekinchess.com/chessnews/events/candidates-2026', $normalized);
    }

    /**
     * Test Content Hash Generation produces deterministic SHA-256.
     */
    public function test_generates_consistent_content_hash(): void
    {
        $hash1 = $this->service->generateContentHash('Gukesh Wins', 'Great game in Madrid', '<p>Some content</p>');
        $hash2 = $this->service->generateContentHash('  gukesh wins  ', 'Great game in Madrid', 'Some content');

        $this->assertEquals($hash1, $hash2);
        $this->assertEquals(64, strlen($hash1));
    }

    /**
     * Test Invalid / Malformed XML Feed throws an Exception cleanly.
     */
    public function test_handles_malformed_xml_feed(): void
    {
        $this->expectException(Exception::class);
        $this->service->parseFeedContent('<rss><unclosed_tag>');
    }
}
