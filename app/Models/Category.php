<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function articlesManyToMany()
    {
        return $this->belongsToMany(Article::class, 'article_category')->withTimestamps();
    }
}
