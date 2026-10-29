<?php

namespace App\Services;

class PgnParserService
{
    public const STANDARD_STARTING_FEN = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';

    // Regex for standard SAN move format
    public const SAN_REGEX = '/^(?:O-O-O|O-O|[KQRBN]?[a-h]?[1-8]?x?[a-h][1-8](?:=[QRBN])?[\+#]?)$/';

    /**
     * Parse structured PGN string into game metadata, starting FEN, and SAN moves.
     *
     * @param string|null $pgn
     * @return array|null Returns parsed structure or null if invalid / empty
     */
    public function parse(?string $pgn): ?array
    {
        if (empty($pgn) || !is_string($pgn)) {
            return null;
        }

        $pgn = trim($pgn);
        if (empty($pgn)) {
            return null;
        }

        $headers = $this->extractHeaders($pgn);
        $rawMoveText = $this->extractMoveSection($pgn);

        $parsedMoves = $this->parseMoveText($rawMoveText);
        $sanList = array_column($parsedMoves['moves'], 'san');

        // If no headers and no valid moves, it's not a valid chess game
        if (empty($headers) && empty($sanList)) {
            return null;
        }

        $startingFen = $headers['FEN'] ?? self::STANDARD_STARTING_FEN;
        $result = $headers['Result'] ?? $parsedMoves['result'] ?? '*';

        return [
            'headers' => $headers,
            'white' => $headers['White'] ?? null,
            'black' => $headers['Black'] ?? null,
            'white_elo' => $headers['WhiteElo'] ?? null,
            'black_elo' => $headers['BlackElo'] ?? null,
            'event' => $headers['Event'] ?? null,
            'site' => $headers['Site'] ?? null,
            'date' => $headers['Date'] ?? null,
            'round' => $headers['Round'] ?? null,
            'result' => $result,
            'eco' => $headers['ECO'] ?? null,
            'starting_fen' => $startingFen,
            'moves' => $parsedMoves['moves'],
            'moves_san' => $sanList,
            'move_count' => count($sanList),
            'has_moves' => count($sanList) > 0,
            'pgn' => $pgn,
        ];
    }

    /**
     * Extract PGN header tags [Key "Value"]
     */
    public function extractHeaders(string $pgn): array
    {
        $headers = [];
        if (preg_match_all('/^\s*\[([A-Za-z0-9_]+)\s+"([^"]*)"\]\s*$/m', $pgn, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $headers[$m[1]] = trim($m[2]);
            }
        }
        return $headers;
    }

    /**
     * Separate the move section from the header tags
     */
    public function extractMoveSection(string $pgn): string
    {
        // Strip out lines starting with [Header "Value"]
        $lines = explode("\n", $pgn);
        $moveLines = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) {
                continue;
            }
            if (str_starts_with($trimmed, '[') && str_ends_with($trimmed, ']')) {
                continue;
            }
            $moveLines[] = $trimmed;
        }

        return implode(' ', $moveLines);
    }

    /**
     * Parse move text supporting:
     * - Move numbers (e.g. 1., 1..., 24.)
     * - Comments in curly braces { ... }
     * - Variations in parentheses ( ... )
     * - NAGs ($1, $2, !, ?, !?, ?!)
     * - Standard game results (1-0, 0-1, 1/2-1/2, *)
     */
    public function parseMoveText(string $moveText): array
    {
        if (empty(trim($moveText))) {
            return ['moves' => [], 'result' => null];
        }

        $moves = [];
        $gameResult = null;

        // Extract and balance comments and variations while tokenizing
        $tokens = $this->tokenizeMoveText($moveText);
        $currentMoveNumber = 1;
        $currentComment = '';

        foreach ($tokens as $token) {
            // Check for game result
            if (in_array($token, ['1-0', '0-1', '1/2-1/2', '*'], true)) {
                $gameResult = $token;
                continue;
            }

            // Check if token is a comment
            if (str_starts_with($token, '{') && str_ends_with($token, '}')) {
                $commentContent = trim(substr($token, 1, -1));
                if (!empty($moves)) {
                    $moves[count($moves) - 1]['comment'] = $commentContent;
                } else {
                    $currentComment = $commentContent;
                }
                continue;
            }

            // Check if token is a variation (skip moves inside variation for primary navigation)
            if (str_starts_with($token, '(') && str_ends_with($token, ')')) {
                if (!empty($moves)) {
                    $moves[count($moves) - 1]['variation'] = trim(substr($token, 1, -1));
                }
                continue;
            }

            // Move number like "1." or "1..." or "14."
            if (preg_match('/^(\d+)\.+$/', $token, $m)) {
                $currentMoveNumber = (int)$m[1];
                continue;
            }

            // Strip suffix evaluation symbols like !, ?, !?, ?!, etc. from the move token for validation
            $cleanSan = preg_replace('/[\!\?]+$/', '', $token);

            // Strip numeric annotation glyphs like $1, $2, etc.
            if (preg_match('/^\$\d+$/', $cleanSan)) {
                continue;
            }

            // Validate against SAN format
            if (preg_match(self::SAN_REGEX, $cleanSan)) {
                $ply = count($moves) + 1;
                $isWhite = ($ply % 2 === 1);
                $moveNum = (int)ceil($ply / 2);

                $moveData = [
                    'ply' => $ply,
                    'move_number' => $moveNum,
                    'color' => $isWhite ? 'w' : 'b',
                    'san' => $cleanSan,
                    'raw' => $token,
                    'comment' => $currentComment ?: null,
                ];

                $moves[] = $moveData;
                $currentComment = '';
            }
        }

        return [
            'moves' => $moves,
            'result' => $gameResult,
        ];
    }

    /**
     * Tokenize raw move string into tokens, preserving comments { ... } and variations ( ... )
     */
    protected function tokenizeMoveText(string $text): array
    {
        $tokens = [];
        $len = strlen($text);
        $i = 0;

        while ($i < $len) {
            $char = $text[$i];

            // Skip whitespace
            if (ctype_space($char)) {
                $i++;
                continue;
            }

            // Comment enclosed in { ... }
            if ($char === '{') {
                $start = $i;
                $i++;
                while ($i < $len && $text[$i] !== '}') {
                    $i++;
                }
                if ($i < $len && $text[$i] === '}') {
                    $i++;
                }
                $tokens[] = substr($text, $start, $i - $start);
                continue;
            }

            // Variation enclosed in ( ... )
            if ($char === '(') {
                $start = $i;
                $depth = 1;
                $i++;
                while ($i < $len && $depth > 0) {
                    if ($text[$i] === '(') {
                        $depth++;
                    } elseif ($text[$i] === ')') {
                        $depth--;
                    }
                    $i++;
                }
                $tokens[] = substr($text, $start, $i - $start);
                continue;
            }

            // Regular token (move number, SAN move, result, etc.)
            $start = $i;
            while ($i < $len && !ctype_space($text[$i]) && $text[$i] !== '{' && $text[$i] !== '(') {
                $i++;
            }
            $tokens[] = substr($text, $start, $i - $start);
        }

        return $tokens;
    }

    /**
     * Extract structured PGN from arbitrary text (HTML, Markdown, or raw content).
     * Strictly verifies that the block is genuine chess notation, never inventing moves from prose.
     *
     * @param string $content
     * @return string|null
     */
    public function extractPgnFromContent(string $content): ?string
    {
        if (empty($content)) {
            return null;
        }

        // 1. Look for explicit PGN tags block e.g. [Event "..."] ... 1. e4
        if (preg_match('/(\[Event\s+"[^"]+"\].*?(?:1-0|0-1|1\/2-1\/2|\*|\d+\.\s+[A-Za-z0-9\+#\-\=]+))/s', $content, $match)) {
            $candidate = trim($match[1]);
            if ($this->isValidStructuredPgn($candidate)) {
                return $candidate;
            }
        }

        // 2. Look for code / pre blocks containing structured chess notation
        if (preg_match_all('/<(?:pre|code)[^>]*>(.*?)<\/(?:pre|code)>/si', $content, $codeMatches)) {
            foreach ($codeMatches[1] as $codeBlock) {
                $candidate = trim(strip_tags($codeBlock));
                if ($this->isValidStructuredPgn($candidate)) {
                    return $candidate;
                }
            }
        }

        // 3. Look for isolated standard notation blocks (e.g. 1. e4 e5 2. Nf3 ...) with at least 3 valid move pairs
        if (preg_match('/(?:^|\n\n)(\s*1\.\s+[KQRBN]?[a-h]?[1-8]?x?[a-h][1-8].{10,})(?:\n\n|$)/s', $content, $notationMatch)) {
            $candidate = trim($notationMatch[1]);
            if ($this->isValidStructuredPgn($candidate, minMoves: 6)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Validate that candidate PGN string is truly structured chess data and not ordinary English prose.
     */
    public function isValidStructuredPgn(string $pgn, int $minMoves = 2): bool
    {
        $hasTags = (bool)preg_match('/^\s*\[(?:Event|Site|Date|Round|White|Black|Result|FEN)\s+"[^"]+"\]/m', $pgn);
        $moveText = $this->extractMoveSection($pgn);

        if (empty(trim($moveText))) {
            return $hasTags;
        }

        // Must start with or contain move notation (e.g. "1. e4" or "1... c5")
        if (!preg_match('/\b\d+\s*\.\s*[KQRBN]?[a-h]?[1-8]?x?[a-h][1-8]/i', $moveText)) {
            return false;
        }

        // Parse moves and check count of valid SAN moves
        $parsed = $this->parseMoveText($moveText);
        $validMoves = count($parsed['moves']);

        // Prevent false positives from ordinary sentences that happen to have a number and period
        return ($validMoves >= $minMoves) || ($hasTags && $validMoves >= 1);
    }
}
