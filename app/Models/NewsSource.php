<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'feed_url',
        'site_url',
        'description',
        'default_category_id',
        'language',
        'is_active',
        'fetch_frequency_minutes',
        'last_fetched_at',
        'last_error',
        'items_fetched_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'fetch_frequency_minutes' => 'integer',
        'items_fetched_count' => 'integer',
        'last_fetched_at' => 'datetime',
    ];

    public function defaultCategory()
    {
        return $this->belongsTo(Category::class, 'default_category_id');
    }

    public function articles()
    {
        return $this->hasMany(Article::class, 'news_source_id');
    }

    public function importHistories()
    {
        return $this->hasMany(ImportHistory::class, 'news_source_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
