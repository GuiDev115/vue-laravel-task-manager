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
    Route::get('/tasks', [TaskController::class, 'indexPage'])->name('tasks.page');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Temporary routes for Railway deployment (REMOVE AFTER USE!)
Route::get('/test-db', function () {
    try {
        $host = env('DB_HOST');
        $port = env('DB_PORT', 3306);
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        
        $pdo = new PDO("mysql:host={$host};port={$port};dbname={$database}", $username, $password);
        
        $stmt = $pdo->query('SHOW TABLES');
        $tables = $stmt->fetchAll();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Database connected successfully!',
            'connection_info' => [
                'host' => $host,
                'port' => $port,
                'database' => $database,
                'username' => $username
            ],
            'tables_count' => count($tables),
            'tables' => $tables
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'connection_info' => [
                'host' => env('DB_HOST'),
                'port' => env('DB_PORT', 3306),
                'database' => env('DB_DATABASE'),
                'username' => env('DB_USERNAME')
            ]
        ], 500);
    }
});

Route::get('/run-migrations', function () {
    if (!app()->environment('production')) {
        return response()->json(['error' => 'Only available in production'], 403);
    }
    
    try {
        // Check database connection first
        DB::connection()->getPdo();
        
        // Run migrations
        Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = Artisan::output();
        
        // Run seeders
        Artisan::call('db:seed', ['--force' => true]);
        $seedOutput = Artisan::output();
        
        // Clear and cache
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        
        return response()->json([
            'status' => 'success',
            'message' => 'Migrations and seeds executed successfully!',
            'migrate_output' => $migrateOutput,
            'seed_output' => $seedOutput
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

require __DIR__.'/auth.php';
