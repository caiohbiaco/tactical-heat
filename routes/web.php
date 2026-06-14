<?php
// routes/web.php

use App\Http\Controllers\SearchController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
    });

    // Perfil
    Route::get('/profile',               [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/info',         [ProfileController::class, 'updateInfo'])->name('profile.info');
    Route::post('/profile/password',     [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar',       [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile/avatar',     [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');
    Route::delete('/profile',            [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Dashboard ────────────────────────────────────────────────────────
    Route::get('/dashboard', [SearchController::class, 'index'])->name('dashboard');

    // ── Busca individual ─────────────────────────────────────────────────
    Route::get('/search',  [SearchController::class, 'create'])->name('search.form');
    Route::post('/search', [SearchController::class, 'store'])->name('search.store');
    Route::delete('/searches/{search}', [SearchController::class, 'destroy'])->name('searches.destroy');

    // ── Resultados ───────────────────────────────────────────────────────
    Route::get('/results/{search}',      [ResultController::class, 'show'])->name('results.show');
    Route::get('/results/{search}/risk', [ResultController::class, 'risk'])->name('results.risk');

    // ── Relatório IA + PDF ───────────────────────────────────────────────
    Route::post('/results/{search}/report',     [ReportController::class, 'generate'])->name('report.generate');
    Route::get('/results/{search}/report',      [ReportController::class, 'show'])->name('report.show');
    Route::get('/results/{search}/report/pdf',  [ReportController::class, 'pdf'])->name('report.pdf');

    // ── Comparação entre cidades ─────────────────────────────────────────
    Route::get('/compare',  [CompareController::class, 'form'])->name('compare.form');
    Route::post('/compare', [CompareController::class, 'result'])->name('compare.result');
});

require __DIR__.'/auth.php';