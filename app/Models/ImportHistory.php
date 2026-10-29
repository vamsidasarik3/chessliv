<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportHistory extends Model
{
    use HasFactory;

    protected $table = 'import_histories';

    protected $fillable = [
        'news_source_id',
        'source_name',
        'started_at',
        'completed_at',
        'duration_seconds',
        'status',
        'articles_fetched',
        'articles_imported',
        'duplicates_skipped',
        'errors_count',
        'error_messages',
        'trigger_type',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'duration_seconds' => 'float',
        'error_messages' => 'array',
        'articles_fetched' => 'integer',
        'articles_imported' => 'integer',
        'duplicates_skipped' => 'integer',
        'errors_count' => 'integer',
    ];

    /**
     * Associated news source
     */
    public function newsSource()
    {
        return $this->belongsTo(NewsSource::class, 'news_source_id');
    }

    /**
     * Scope for failed or error-prone imports
     */
    public function scopeFailed($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'failed')
              ->orWhere('errors_count', '>', 0);
        });
    }

    /**
     * Scope for recent imports first
     */
    public function scopeRecent($query)
    {
        return $query->latest('started_at');
    }

    /**
     * Formatted duration string
     */
    public function getFormattedDurationAttribute(): string
    {
        if ($this->duration_seconds === null) {
            return 'Running...';
        }
        return number_format($this->duration_seconds, 2) . 's';
    }
}
