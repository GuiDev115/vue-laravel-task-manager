<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Estatísticas básicas
        if ($user->isAdmin()) {
            $stats = [
                'total_tasks' => Task::count(),
                'completed_tasks' => Task::completed()->count(),
                'pending_tasks' => Task::pending()->count(),
                'total_users' => User::count(),
            ];
        } else {
            $stats = [
                'total_tasks' => $user->tasks()->count(),
                'completed_tasks' => $user->tasks()->completed()->count(),
                'pending_tasks' => $user->tasks()->pending()->count(),
                'total_users' => null,
            ];
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'user' => $user->only(['id', 'name', 'email', 'role']),
        ]);
    }
}
