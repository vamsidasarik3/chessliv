<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\PuzzleController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\NewsIngestionController as AdminNewsIngestionController;
use App\Http\Controllers\Admin\ImportHistoryController as AdminImportHistoryController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\GameController as AdminGameController;
use App\Http\Controllers\Admin\HealthController as AdminHealthController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');

Route::get('/news', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/news/{slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/category/{slug}', [ArticleController::class, 'category'])->name('category.show');

Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments.index');
Route::get('/puzzles', [PuzzleController::class, 'index'])->name('puzzles.index');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

Route::view('/about', 'about')->name('about');

// Admin Authentication & Ingestion Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Protected Admin Routes (EnsureUserIsAdmin)
    Route::middleware('admin')->group(function () {
        // Feeds & Ingestion Console
        Route::get('/ingestion', [AdminNewsIngestionController::class, 'index'])->name('ingestion.index');
        Route::post('/ingestion/refresh', [AdminNewsIngestionController::class, 'refresh'])->name('ingestion.refresh');
        Route::post('/ingestion/sources', [AdminNewsIngestionController::class, 'storeSource'])->name('ingestion.sources.store');
        Route::put('/ingestion/sources/{id}', [AdminNewsIngestionController::class, 'updateSource'])->name('ingestion.sources.update');
        Route::post('/ingestion/sources/{id}/toggle', [AdminNewsIngestionController::class, 'toggleSource'])->name('ingestion.sources.toggle');
        Route::delete('/ingestion/sources/{id}', [AdminNewsIngestionController::class, 'destroySource'])->name('ingestion.sources.destroy');

        // Import History & Review Failed Imports
        Route::get('/history', [AdminImportHistoryController::class, 'index'])->name('history.index');
        Route::get('/history/{id}', [AdminImportHistoryController::class, 'show'])->name('history.show');

        // Category Management
        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{id}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::post('/categories/{id}/toggle', [AdminCategoryController::class, 'toggle'])->name('categories.toggle');
        Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

        // Game Metadata Correction
        Route::get('/games', [AdminGameController::class, 'index'])->name('games.index');
        Route::get('/games/{id}/edit', [AdminGameController::class, 'edit'])->name('games.edit');
        Route::put('/games/{id}', [AdminGameController::class, 'update'])->name('games.update');

        // System Health & Reliability Status
        Route::get('/health', [AdminHealthController::class, 'index'])->name('health.index');
        Route::get('/status', [AdminHealthController::class, 'index'])->name('status.index');
    });
});
