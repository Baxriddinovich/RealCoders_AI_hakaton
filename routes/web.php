<?php

use App\Http\Controllers\AiAnalysisController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlantSpotController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportPdfController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\TelegramBotController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/plants/{plantSpot}', [PlantSpotController::class, 'show'])->name('plants.show');

Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');
Route::patch('/alerts/{alert}', [AlertController::class, 'update'])->name('alerts.update');

Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

Route::get('/ai-tahlil', [AiAnalysisController::class, 'index'])->name('ai-analysis.index');
Route::post('/ai-tahlil', [AiAnalysisController::class, 'analyze'])->name('ai-analysis.analyze');

Route::get('/greenhouses/{greenhouse}/report.pdf', [ReportPdfController::class, 'download'])->name('reports.pdf');

Route::post('/scans', [ScanController::class, 'start'])->name('scans.start');
Route::get('/scans/status', [ScanController::class, 'statuses'])->name('scans.status');

Route::post('/telegram/webhook', [TelegramBotController::class, 'webhook'])->name('telegram.webhook');
