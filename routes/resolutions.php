<?php

/*
|--------------------------------------------------------------------------
| Add these routes to your web.php file inside your auth middleware group
|--------------------------------------------------------------------------
*/

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BoardResolutionController;
use App\Http\Controllers\BoardPaperController;

// ── Board Resolutions ──────────────────────────────────────────────────
Route::prefix('board-resolutions')->middleware(['auth'])->group(function () {
    Route::get('/',           [BoardResolutionController::class, 'index'])->name('resolutions.index');
    Route::get('/create',     [BoardResolutionController::class, 'create'])->name('resolutions.create');
    Route::post('/',          [BoardResolutionController::class, 'store'])->name('resolutions.store');
    Route::get('/{resolution}',       [BoardResolutionController::class, 'show'])->name('resolutions.show');
    Route::get('/{resolution}/edit',  [BoardResolutionController::class, 'edit'])->name('resolutions.edit');
    Route::put('/{resolution}',       [BoardResolutionController::class, 'update'])->name('resolutions.update');
    Route::patch('/{resolution}/status', [BoardResolutionController::class, 'updateStatus'])->name('resolutions.updateStatus');
    Route::delete('/{resolution}',    [BoardResolutionController::class, 'destroy'])->name('resolutions.destroy');
});

// ── Board Papers ───────────────────────────────────────────────────────
Route::prefix('board-papers')->middleware(['auth'])->group(function () {
    Route::get('/',           [BoardPaperController::class, 'index'])->name('papers.index');
    Route::get('/create',     [BoardPaperController::class, 'create'])->name('papers.create');
    Route::post('/',          [BoardPaperController::class, 'store'])->name('papers.store');
    Route::get('/{paper}',           [BoardPaperController::class, 'show'])->name('papers.show');
    Route::get('/{paper}/edit',      [BoardPaperController::class, 'edit'])->name('papers.edit');
    Route::put('/{paper}',           [BoardPaperController::class, 'update'])->name('papers.update');
    Route::get('/{paper}/sign',      [BoardPaperController::class, 'signForm'])->name('papers.signForm');
    Route::post('/{paper}/sign',     [BoardPaperController::class, 'sign'])->name('papers.sign');
    Route::delete('/{paper}',        [BoardPaperController::class, 'destroy'])->name('papers.destroy');
});


use App\Http\Controllers\QuickSignatureController;

Route::middleware(['auth'])->group(function () {
    Route::get('/quick-sign',        [QuickSignatureController::class, 'index'])->name('quicksign.index');
    Route::get('/quick-sign/create', [QuickSignatureController::class, 'create'])->name('quicksign.create');
    Route::post('/quick-sign',       [QuickSignatureController::class, 'store'])->name('quicksign.store');
});



use App\Http\Controllers\SignedDocumentController;

Route::prefix('pdf-sign')->middleware(['auth'])->group(function () {
    Route::get('/',                    [SignedDocumentController::class, 'index'])->name('pdfsign.index');
    Route::get('/upload',              [SignedDocumentController::class, 'create'])->name('pdfsign.upload');
    Route::post('/',                   [SignedDocumentController::class, 'store'])->name('pdfsign.store');
    Route::get('/{document}/viewer',   [SignedDocumentController::class, 'viewer'])->name('pdfsign.viewer');
    Route::post('/{document}/sign',    [SignedDocumentController::class, 'sign'])->name('pdfsign.sign');
    Route::post('/{document}/unsign',  [SignedDocumentController::class, 'unsign'])->name('pdfsign.unsign');
    Route::delete('/{document}',       [SignedDocumentController::class, 'destroy'])->name('pdfsign.destroy');
});
