<?php

use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:web'])->group(function () {
    // User info route
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Tasks API routes
    Route::apiResource('tasks', TaskController::class);
    Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    Route::get('tasks/export/csv', [TaskController::class, 'export'])->name('tasks.export');
});
