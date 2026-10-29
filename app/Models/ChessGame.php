<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChessGame extends Model
{
    use HasFactory;

    protected $table = 'chess_games';

    public const STANDARD_STARTING_FEN = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';

    protected $fillable = [
        'article_id',
        'white',
        'black',
        'white_elo',
        'black_elo',
        'event',
        'site',
        'date',
        'round',
        'result',
        'eco',
        'starting_fen',
        'pgn',
        'moves_san',
        'source_url',
    ];

    protected $casts = [
        'moves_san' => 'array',
    ];

    /**
     * Associated article
     */
    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    /**
     * Check if game has playable SAN moves
     */
    public function hasMoves(): bool
    {
        return !empty($this->moves_san) && count($this->moves_san) > 0;
    }

    /**
     * Returns matchup text
     */
    public function getMatchupAttribute(): string
    {
        $white = $this->white ?: 'White';
        $black = $this->black ?: 'Black';
        return "{$white} vs {$black}";
    }

    /**
     * Returns clean starting FEN or standard default
     */
    public function getEffectiveStartingFenAttribute(): string
    {
        return !empty($this->starting_fen) ? $this->starting_fen : self::STANDARD_STARTING_FEN;
    }

    /**
     * Count total half-moves / plies
     */
    public function getMoveCountAttribute(): int
    {
        return is_array($this->moves_san) ? count($this->moves_san) : 0;
    }
}
