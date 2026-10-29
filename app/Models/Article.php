<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'news_source_id',
        'title',
        'slug',
        'summary',
        'content',
        'pgn',
        'game_white',
        'game_black',
        'game_result',
        'game_event',
        'game_date',
        'original_url',
        'canonical_url',
        'content_hash',
        'language',
        'processing_status',
        'author_name',
        'author_title',
        'player_names',
        'source_name',
        'image_url',
        'read_time',
        'views_count',
        'is_featured',
        'is_breaking',
        'published_at',
        'fetched_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_breaking' => 'boolean',
        'published_at' => 'datetime',
        'fetched_at' => 'datetime',
        'views_count' => 'integer',
    ];

    /**
     * Primary category
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Many-to-many categories relationship
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'article_category')->withTimestamps();
    }

    /**
     * News source
     */
    public function newsSource()
    {
        return $this->belongsTo(NewsSource::class, 'news_source_id');
    }

    /**
     * Associated chess game (primary)
     */
    public function chessGame()
    {
        return $this->hasOne(ChessGame::class, 'article_id');
    }

    /**
     * All associated chess games
     */
    public function chessGames()
    {
        return $this->hasMany(ChessGame::class, 'article_id');
    }

    /**
     * Check if article has an associated chess game
     */
    public function hasGame(): bool
    {
        if ($this->relationLoaded('chessGame')) {
            return $this->chessGame !== null;
        }

        return !empty($this->pgn) || $this->chessGame()->exists();
    }

    /**
     * Author accessor / alias for 'author'
     */
    public function getAuthorAttribute()
    {
        return $this->author_name;
    }

    public function setAuthorAttribute($value)
    {
        $this->attributes['author_name'] = $value;
    }

    /**
     * Full content accessor / alias
     */
    public function getFullContentAttribute()
    {
        return $this->content;
    }

    public function setFullContentAttribute($value)
    {
        $this->attributes['content'] = $value;
    }

    /**
     * Scopes
     */
    public function scopeProcessed($query)
    {
        return $query->where('processing_status', 'processed');
    }

    public function scopeBySource($query, $sourceId)
    {
        return $query->where('news_source_id', $sourceId);
    }

    public function scopeForPlayer($query, string $player)
    {
        $term = trim($player);
        return $query->where(function ($q) use ($term) {
            $q->where('player_names', 'like', "%{$term}%")
              ->orWhere('title', 'like', "%{$term}%")
              ->orWhere('summary', 'like', "%{$term}%")
              ->orWhere('game_white', 'like', "%{$term}%")
              ->orWhere('game_black', 'like', "%{$term}%");
        });
    }
}
