<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Tasks routes
    Route::get('/tasks', [TaskController::class, 'indexPage'])->name('tasks.index');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Temporary database test routes (REMOVE AFTER TESTING!)
Route::get('/db-test', function () {
    try {
        DB::connection()->getPdo();
        $tables = DB::select('SHOW TABLES');
        $migrations = DB::table('migrations')->get();
        
        return response()->json([
            'status' => 'Connected successfully!',
            'database' => env('DB_DATABASE'),
            'host' => env('DB_HOST'),
            'tables_count' => count($tables),
            'migrations_count' => $migrations->count(),
            'tables' => $tables,
            'migrations' => $migrations->pluck('migration')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'Connection failed',
            'error' => $e->getMessage(),
            'database_config' => [
                'host' => env('DB_HOST'),
                'port' => env('DB_PORT'),
                'database' => env('DB_DATABASE'),
                'username' => env('DB_USERNAME')
            ]
        ], 500);
    }
});

Route::get('/run-migrations', function () {
    if (app()->environment('production')) {
        try {
            // Run migrations
            Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = Artisan::output();
            
            // Run seeders
            Artisan::call('db:seed', ['--force' => true]);
            $seedOutput = Artisan::output();
            
            return response()->json([
                'success' => true,
                'migrate_output' => $migrateOutput,
                'seed_output' => $seedOutput
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    return response()->json(['error' => 'Only available in production'], 403);
});

require __DIR__.'/auth.php';
