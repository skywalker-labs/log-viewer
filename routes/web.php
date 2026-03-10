<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Skywalker\LogViewer\Http\Controllers\LogViewerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(config('log-viewer.route.attributes', []), function () {
    Route::name('log-viewer::')->group(function () {
        Route::get('/', [LogViewerController::class, 'index'])->name('dashboard');
        Route::get('search', [LogViewerController::class, 'globalSearch'])->name('global-search');
        Route::get('journey/{id}', [LogViewerController::class, 'journey'])->name('journey');
        Route::post('notes', [LogViewerController::class, 'storeNote'])->name('notes.store');
        Route::post('searches', [LogViewerController::class, 'saveSearch'])->name('searches.save');
        Route::post('notifications', [LogViewerController::class, 'saveNotificationSettings'])->name('notifications.save');
        Route::get('ai-explain', [LogViewerController::class, 'explainError'])->name('ai-explain');
        Route::post('push-to-tracker', [LogViewerController::class, 'pushToTracker'])->name('push-to-tracker');
        Route::post('cleanup-logs', [LogViewerController::class, 'cleanupLogs'])->name('cleanup-logs');
        Route::get('reports/download', [LogViewerController::class, 'downloadReport'])->name('reports.download');
        Route::post('reports/email', [LogViewerController::class, 'sendEmailReport'])->name('reports.email');
        Route::get('compare', [LogViewerController::class, 'compare'])->name('compare');

        Route::prefix('logs')->name('logs.')->group(function () {
            Route::get('/', [LogViewerController::class, 'listLogs'])->name('list');
            Route::delete('delete', [LogViewerController::class, 'delete'])->name('delete');
            Route::post('bulk-delete', [LogViewerController::class, 'bulkDelete'])->name('bulk-delete');
            Route::get('live', [LogViewerController::class, 'live'])->name('live');
            Route::get('tail', [LogViewerController::class, 'tail'])->name('tail');

            Route::prefix('{date}')->group(function () {
                Route::get('/', [LogViewerController::class, 'show'])->name('show');
                Route::get('download', [LogViewerController::class, 'download'])->name('download');
                Route::get('export', [LogViewerController::class, 'export'])->name('export');
                Route::get('{level}', [LogViewerController::class, 'showByLevel'])->name('filter');
                Route::get('{level}/search', [LogViewerController::class, 'search'])->name('search');
            });
        });
    });
});
