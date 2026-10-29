<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\NewsSource;
use App\Models\ImportHistory;
use App\Models\Article;
use App\Models\Category;
use App\Models\ChessGame;
use Exception;

class HealthController extends Controller
{
    /**
     * Display comprehensive system health and reliability status.
     */
    public function index()
    {
        // 1. Database Connectivity & Metrics
        $dbStatus = [
            'connected' => false,
            'latency_ms' => null,
            'version' => null,
            'database' => config('database.connections.' . config('database.default') . '.database'),
            'driver' => config('database.default'),
            'error' => null,
            'counts' => [
                'articles' => 0,
                'categories' => 0,
                'chess_games' => 0,
                'news_sources' => 0,
                'import_histories' => 0,
            ],
        ];

        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $dbStatus['latency_ms'] = round((microtime(true) - $start) * 1000, 2);
            $dbStatus['connected'] = true;

            $versionRes = DB::select('SELECT VERSION() as v');
            $dbStatus['version'] = $versionRes[0]->v ?? 'Unknown';

            $dbStatus['counts']['articles'] = Article::count();
            $dbStatus['counts']['categories'] = Category::count();
            $dbStatus['counts']['chess_games'] = ChessGame::count();
            $dbStatus['counts']['news_sources'] = NewsSource::count();
            $dbStatus['counts']['import_histories'] = ImportHistory::count();
        } catch (Exception $e) {
            $dbStatus['connected'] = false;
            $dbStatus['error'] = $e->getMessage();
        }

        // 2. Last Successful Import
        $lastSuccessfulImport = ImportHistory::where('status', 'completed')
            ->latest('completed_at')
            ->first();

        $lastAnyImport = ImportHistory::latest('started_at')->first();

        // 3. Enabled News Sources
        $sources = NewsSource::with('defaultCategory')
            ->withCount('articles')
            ->orderBy('id')
            ->get();

        $sourcesSummary = [
            'total' => $sources->count(),
            'enabled' => $sources->where('is_active', true)->count(),
            'disabled' => $sources->where('is_active', false)->count(),
            'healthy' => $sources->where('is_active', true)->whereNull('last_error')->count(),
            'with_errors' => $sources->whereNotNull('last_error')->count(),
        ];

        // 4. Failed Jobs & Queue Health
        $queueStatus = [
            'failed_jobs_count' => 0,
            'pending_jobs_count' => 0,
            'recent_failed' => [],
            'error' => null,
        ];

        try {
            $queueStatus['failed_jobs_count'] = DB::table('failed_jobs')->count();
            $queueStatus['pending_jobs_count'] = DB::table('jobs')->count();
            $queueStatus['recent_failed'] = DB::table('failed_jobs')
                ->latest('failed_at')
                ->take(5)
                ->get();
        } catch (Exception $e) {
            $queueStatus['error'] = $e->getMessage();
        }

        // 5. Local Assets Health (chess libraries and pieces)
        $assetChecks = [
            'chess_js' => file_exists(public_path('vendor/chess/chess.min.js')),
            'chessboard_js' => file_exists(public_path('vendor/chess/chessboard.min.js')),
            'chessboard_css' => file_exists(public_path('vendor/chess/chessboard.min.css')),
            'pieces_dir' => is_dir(public_path('vendor/chess/img/chesspieces/wikipedia')),
            'placeholder_img' => file_exists(public_path('images/chess-placeholder.svg')),
        ];

        // 6. Runtime Environment Status
        $runtime = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug'),
            'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'max_execution_time' => ini_get('max_execution_time') . 's',
            'http_timeout' => config('chess_sources.request.timeout', 15) . 's',
            'http_retries' => config('chess_sources.request.retry_limit', 2),
        ];

        // Overall system status score
        $overallHealthy = $dbStatus['connected'] 
            && ($queueStatus['failed_jobs_count'] === 0)
            && $assetChecks['chess_js']
            && $assetChecks['chessboard_js'];

        return view('admin.health.index', compact(
            'dbStatus',
            'lastSuccessfulImport',
            'lastAnyImport',
            'sources',
            'sourcesSummary',
            'queueStatus',
            'assetChecks',
            'runtime',
            'overallHealthy'
        ));
    }
}
