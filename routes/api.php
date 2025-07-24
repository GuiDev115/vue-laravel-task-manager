<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\DebugController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    // Debug route
    Route::get('/debug/auth', [DebugController::class, 'checkAuth']);
    
    // User info route
    Route::get('/user', function (Request $request) {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        return $request->user();
    });
    
    // Tasks API routes - require authentication
    Route::middleware(['auth'])->group(function () {
        Route::apiResource('tasks', TaskController::class);
        Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
        Route::get('tasks/export/csv', [TaskController::class, 'export'])->name('tasks.export');
    });
});
