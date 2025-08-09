<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AiLogController;
use App\Http\Controllers\AiSqlController;
use App\Http\Controllers\SlackLogController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// AI Log Analysis Routes (No authentication required)
Route::prefix('ai')->group(function () {
    Route::post('/explain-log', [AiLogController::class, 'explainLog']);
    Route::post('/classify-log', [AiLogController::class, 'classifyLog']);
});

// AI SQL Analysis Routes (No authentication required)
Route::prefix('ai')->group(function () {
    Route::post('/explain-sql', [AiSqlController::class, 'explainSql']);
    Route::post('/optimize-sql', [AiSqlController::class, 'optimizeSql']);
});

// Slack Integration Routes (Optional)
Route::prefix('slack')->group(function () {
    Route::get('/fetch-logs', [SlackLogController::class, 'fetchLogs']);
}); 