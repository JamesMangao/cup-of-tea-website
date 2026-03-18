<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContentLibraryController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
Route::get('/api/dashboard/stats', [DashboardController::class, 'getStats'])->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/news',         fn() => view('news-feed'))->name('news.feed');
    Route::get('/news/article', fn() => view('article'))->name('news.article');

    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile',      [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/ai-summarizer', fn() => view('ai-summarizer'))->name('ai.summarizer');
    
    // Content Library
    Route::get('/content-library', [ContentLibraryController::class, 'index'])->name('content.library');
    Route::delete('/content-library/{id}', [ContentLibraryController::class, 'destroy'])->name('content-library.destroy');
    Route::post('/content-library/clear-all', [ContentLibraryController::class, 'clearAll'])->name('content-library.clear-all');

    // Protected API routes (require authentication)
    Route::post('/api/summarize',     [AiController::class, 'summarize']);
    Route::post('/api/store-summary', [AiController::class, 'storeSummary']);
    Route::get('/api/summaries',      [AiController::class, 'summaries']);

    // News API routes (protected)
    Route::get('/api/news',              [NewsController::class, 'news']);
    Route::get('/api/news/full',         [NewsController::class, 'full']);
    Route::post('/api/news/toggle-save', [NewsController::class, 'toggleSave'])->name('api.news.toggle-save');
    Route::post('/api/news/toggle-favorite', [NewsController::class, 'toggleFavorite'])->name('api.news.toggle-favorite');
    Route::get('/api/news/check-saved',  [NewsController::class, 'checkSaved']);
    Route::get('/api/news/saved',        [NewsController::class, 'getSaved']);

    Route::middleware('admin')->group(function () {
        Route::get('/analytics',      fn() => view('analytics'))->name('analytics');
        Route::get('/system-logs',    fn() => view('system-logs'))->name('system.logs');

        // User management
        Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::patch('/users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend');

        // Log API routes
        Route::get('/api/logs',         [LogController::class, 'index']);
        Route::get('/api/logs/export',  [LogController::class, 'export']);

        // Analytics API routes
        Route::get('/api/analytics',    [AnalyticsController::class, 'getData']);
        
        // Settings routes
        Route::get('/ai-settings', [SettingsController::class, 'ai'])->name('settings.ai');
        Route::get('/system-settings', [SettingsController::class, 'system'])->name('settings.system');
        Route::post('/api/settings/test-ai', [SettingsController::class, 'testAiConnection']);
        Route::post('/api/settings/clear-cache', [SettingsController::class, 'clearCache']);
    });
});

require __DIR__.'/auth.php';