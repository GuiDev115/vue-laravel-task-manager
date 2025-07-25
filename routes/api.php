<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\DebugController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::get('/debug/auth', [DebugController::class, 'checkAuth']);

// Rotas protegidas com Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::apiResource('tasks', TaskController::class);
    Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    Route::get('tasks/export/csv', [TaskController::class, 'export'])->name('tasks.export');
});

Route::middleware(['web'])->group(function () {
    Route::get('/user-web', function (Request $request) {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        return $request->user();
    });
    
    Route::middleware(['auth'])->group(function () {
        Route::get('tasks-web', [TaskController::class, 'index']);
    });
});
