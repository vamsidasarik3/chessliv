<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\PgnParserService;
use App\Models\ChessGame;

class PgnParserTest extends TestCase
{
    protected PgnParserService $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new PgnParserService();
    }

    /**
     * Test structured PGN parsing with standard headers and moves.
     */
    public function test_parses_structured_pgn_headers_and_moves(): void
    {
        $pgn = <<<PGN
[Event "FIDE Candidates 2024"]
[Site "Toronto, Canada"]
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

        $parsed = $this->parser->parse($pgn);

        $this->assertNotNull($parsed);
        $this->assertEquals('Gukesh D', $parsed['white']);
        $this->assertEquals('Firouzja, Alireza', $parsed['black']);
        $this->assertEquals('FIDE Candidates 2024', $parsed['event']);
        $this->assertEquals('10', $parsed['round']);
        $this->assertEquals('1-0', $parsed['result']);
        $this->assertEquals('2743', $parsed['white_elo']);
        $this->assertEquals('2760', $parsed['black_elo']);
        $this->assertEquals('D38', $parsed['eco']);
        $this->assertEquals(ChessGame::STANDARD_STARTING_FEN, $parsed['starting_fen']);

        $expectedMoves = ['d4', 'Nf6', 'c4', 'e6', 'Nf3', 'd5', 'Nc3', 'Bb4', 'cxd5', 'exd5'];
        $this->assertEquals($expectedMoves, $parsed['moves_san']);
        $this->assertEquals(10, $parsed['move_count']);
        $this->assertTrue($parsed['has_moves']);
    }

    /**
     * Test starting FEN loading from FEN header tag.
     */
    public function test_extracts_custom_starting_fen_when_present(): void
    {
        $customFen = 'r1bqk2r/pp2bppp/2n1pn2/3p4/2PP4/2N2N2/PP2BPPP/R1BQK2R w KQkq - 4 8';
        $pgn = <<<PGN
[Event "Tactical Puzzle"]
[White "Test White"]
[Black "Test Black"]
[SetUp "1"]
[FEN "{$customFen}"]
[Result "*"]

8. O-O O-O 9. Bg5 h6 *
PGN;

        $parsed = $this->parser->parse($pgn);

        $this->assertNotNull($parsed);
        $this->assertEquals($customFen, $parsed['starting_fen']);
        $this->assertEquals(['O-O', 'O-O', 'Bg5', 'h6'], $parsed['moves_san']);
    }

    /**
     * Test comments, move annotations, and variations are parsed without breaking main move line.
     */
    public function test_handles_comments_variations_and_nags(): void
    {
        $pgn = '1. e4 {Best by test} e5 2. Nf3 (2. f4 exf4) 2... Nc6 $1 3. Bb5 a6 4. Ba4 Nf6 1/2-1/2';

        $parsed = $this->parser->parse($pgn);

        $this->assertNotNull($parsed);
        $this->assertContains('e4', $parsed['moves_san']);
        $this->assertContains('e5', $parsed['moves_san']);
        $this->assertContains('Nf3', $parsed['moves_san']);
        $this->assertContains('Nc6', $parsed['moves_san']);
        $this->assertContains('Bb5', $parsed['moves_san']);
        $this->assertEquals('1/2-1/2', $parsed['result']);
    }

    /**
     * Test castling notation (O-O and O-O-O) and pawn promotion (e8=Q).
     */
    public function test_supports_castling_and_promotions(): void
    {
        $pgn = '1. e4 e5 2. Nf3 Nc6 3. Bc4 Bc5 4. O-O Nf6 5. d4 exd4 6. e5 d5 7. exf6 dxc4 8. Re1+ Be6 9. fxg7 Rg8 10. Bg5 Qd5 11. Nc3 Qf5 12. Ne4 Be7 13. Bxe7 Kxe7 14. Nxd4 Nxd4 15. Qxd4 Rad8 16. Qc3 b6 17. Qa3+ c5 18. Qxa7+ Rd7 19. Qxb6 Rxg7 20. Nxc5 Rd6 21. Qc7+ *';

        $parsed = $this->parser->parse($pgn);

        $this->assertNotNull($parsed);
        $this->assertContains('O-O', $parsed['moves_san']);
        $this->assertContains('Re1+', $parsed['moves_san']);
        $this->assertTrue($parsed['has_moves']);
    }

    /**
     * Test invalid or empty PGN handles gracefully and returns null.
     */
    public function test_gracefully_handles_empty_or_invalid_pgn(): void
    {
        $this->assertNull($this->parser->parse(''));
        $this->assertNull($this->parser->parse(null));
        $this->assertNull($this->parser->parse('    '));
        $this->assertNull($this->parser->parse('Lorem ipsum dolor sit amet, consectetur adipiscing elit.'));
    }

    /**
     * Test that ordinary prose is NEVER mistaken for chess games (never invent moves).
     */
    public function test_never_invents_moves_from_ordinary_prose(): void
    {
        $prose1 = 'Gukesh played brilliantly in round 10 and won with style.';
        $prose2 = 'The tournament took place on 2024.04.14 in Toronto. 1. Ding Liren finished with 5.5 points.';
        $prose3 = 'Carlsen won 3 games and drew 2. The score was 1-0 in favor of the white pieces.';

        $this->assertNull($this->parser->extractPgnFromContent($prose1));
        $this->assertNull($this->parser->extractPgnFromContent($prose2));
        $this->assertNull($this->parser->extractPgnFromContent($prose3));
    }

    /**
     * Test extracting structured PGN from article HTML content.
     */
    public function test_extracts_structured_pgn_from_html_code_block(): void
    {
        $html = <<<HTML
<div class="article-body">
    <p>Below is the complete game notation from the crucial game:</p>
    <pre>
[Event "Candidates 2024"]
[White "Gukesh D"]
[Black "Pragg R"]
[Result "1-0"]

1. d4 Nf6 2. c4 e6 3. Nf3 d5 4. Nc3 c5 1-0
    </pre>
    <p>Analysis will follow tomorrow.</p>
</div>
HTML;

        $extracted = $this->parser->extractPgnFromContent($html);

        $this->assertNotNull($extracted);
        $parsed = $this->parser->parse($extracted);
        $this->assertEquals('Gukesh D', $parsed['white']);
        $this->assertEquals('Pragg R', $parsed['black']);
        $this->assertEquals(['d4', 'Nf6', 'c4', 'e6', 'Nf3', 'd5', 'Nc3', 'c5'], $parsed['moves_san']);
    }
}
