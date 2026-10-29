<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewsSource;
use App\Models\Article;
use App\Models\Category;
use App\Services\NewsIngestionService;
use Illuminate\Support\Str;

class NewsIngestionController extends Controller
{
    /**
     * Display the ingestion and refresh dashboard.
     */
    public function index()
    {
        $sources = NewsSource::with('defaultCategory')
            ->withCount('articles')
            ->orderBy('id')
            ->get();

        $categories = Category::where('is_active', true)->get();

        $stats = [
            'total_sources' => $sources->count(),
            'active_sources' => $sources->where('is_active', true)->count(),
            'total_articles' => Article::count(),
            'imported_articles' => Article::whereNotNull('news_source_id')->count(),
            'latest_fetch' => $sources->max('last_fetched_at'),
        ];

        $recentArticles = Article::with(['newsSource', 'category'])
            ->latest('published_at')
            ->take(15)
            ->get();

        return view('admin.ingestion', compact('sources', 'categories', 'stats', 'recentArticles'));
    }

    /**
     * Trigger manual feed refresh (single or all active sources).
     */
    public function refresh(Request $request, NewsIngestionService $service)
    {
        $sourceId = $request->input('source_id');

        if ($sourceId) {
            $source = NewsSource::findOrFail($sourceId);
            $result = $service->ingestSource($source);

            $msg = $result['success']
                ? "Refreshed {$source->name}: {$result['imported']} imported, {$result['skipped']} duplicates skipped."
                : "Failed to refresh {$source->name}: " . implode('; ', $result['errors']);

            if ($request->ajax()) {
                return response()->json([
                    'success' => $result['success'],
                    'message' => $msg,
                    'imported' => $result['imported'],
                    'skipped' => $result['skipped'],
                    'errors' => $result['errors'],
                ]);
            }

            return redirect()->route('admin.ingestion.index')
                ->with($result['success'] ? 'success' : 'error', $msg);
        }

        // Refresh all active sources
        $summary = $service->ingestAll();

        $msg = "Batch refresh complete across {$summary['total_sources']} sources: {$summary['total_imported']} imported, {$summary['total_skipped']} skipped.";

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'imported' => $summary['total_imported'],
                'skipped' => $summary['total_skipped'],
                'failed' => $summary['total_failed'],
                'summary' => $summary,
            ]);
        }

        return redirect()->route('admin.ingestion.index')->with('success', $msg);
    }

    /**
     * Toggle active/inactive status of a source.
     */
    public function toggleSource($id)
    {
        $source = NewsSource::findOrFail($id);
        $source->is_active = !$source->is_active;
        $source->save();

        $status = $source->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.ingestion.index')
            ->with('success', "News source '{$source->name}' has been {$status}.");
    }

    /**
     * Store a new news source.
     */
    public function storeSource(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'feed_url' => 'required|url',
            'site_url' => 'nullable|url',
            'description' => 'nullable|string',
            'default_category_id' => 'nullable|exists:categories,id',
            'language' => 'nullable|string|max:10',
            'fetch_frequency_minutes' => 'nullable|integer|min:15',
        ]);

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;
        while (NewsSource::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        NewsSource::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'feed_url' => $validated['feed_url'],
            'site_url' => $validated['site_url'] ?? null,
            'description' => $validated['description'] ?? null,
            'default_category_id' => $validated['default_category_id'] ?? null,
            'language' => $validated['language'] ?? 'en',
            'fetch_frequency_minutes' => $validated['fetch_frequency_minutes'] ?? 60,
            'is_active' => true,
        ]);

        return redirect()->route('admin.ingestion.index')
            ->with('success', "News source '{$validated['name']}' added successfully.");
    }

    /**
     * Update an existing news source.
     */
    public function updateSource(Request $request, $id)
    {
        $source = NewsSource::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'feed_url' => 'required|url',
            'site_url' => 'nullable|url',
            'description' => 'nullable|string',
            'default_category_id' => 'nullable|exists:categories,id',
            'language' => 'nullable|string|max:10',
            'fetch_frequency_minutes' => 'nullable|integer|min:15',
            'is_active' => 'nullable|boolean',
        ]);

        $source->update([
            'name' => $validated['name'],
            'feed_url' => $validated['feed_url'],
            'site_url' => $validated['site_url'] ?? null,
            'description' => $validated['description'] ?? null,
            'default_category_id' => $validated['default_category_id'] ?? null,
            'language' => $validated['language'] ?? 'en',
            'fetch_frequency_minutes' => $validated['fetch_frequency_minutes'] ?? 60,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.ingestion.index')
            ->with('success', "News source '{$source->name}' updated successfully.");
    }

    /**
     * Delete a news source.
     */
    public function destroySource($id)
    {
        $source = NewsSource::findOrFail($id);
        $name = $source->name;
        $source->delete();

        return redirect()->route('admin.ingestion.index')
            ->with('success', "News source '{$name}' removed.");
    }
}
