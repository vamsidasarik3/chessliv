<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ImportHistory;
use App\Models\NewsSource;

class ImportHistoryController extends Controller
{
    /**
     * Display import history table with filters and error reviewing.
     */
    public function index(Request $request)
    {
        $query = ImportHistory::with('newsSource')->latest('started_at');

        // Status filter
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        // Only failures filter
        if ($request->boolean('failed_only')) {
            $query->failed();
        }

        // Source filter
        if ($request->filled('source_id')) {
            $query->where('news_source_id', $request->input('source_id'));
        }

        $histories = $query->paginate(15)->withQueryString();
        $sources = NewsSource::orderBy('name')->get();

        $stats = [
            'total_runs' => ImportHistory::count(),
            'successful_runs' => ImportHistory::where('status', 'completed')->count(),
            'failed_runs' => ImportHistory::where('status', 'failed')->count(),
            'partial_runs' => ImportHistory::where('status', 'partial')->count(),
            'total_imported' => ImportHistory::sum('articles_imported'),
            'total_skipped' => ImportHistory::sum('duplicates_skipped'),
            'total_errors' => ImportHistory::sum('errors_count'),
        ];

        return view('admin.history.index', compact('histories', 'sources', 'stats'));
    }

    /**
     * View detailed JSON or modal view for a specific import run.
     */
    public function show($id)
    {
        $history = ImportHistory::with('newsSource')->findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($history);
        }

        return view('admin.history.show', compact('history'));
    }
}
