<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = Task::with('user');

        // Se não for admin, mostrar apenas suas próprias tarefas
        if (!$user->isAdmin()) {
            $query = $query->where('user_id', $user->id);
        }

        // Filtros
        if ($request->has('status')) {
            if ($request->status === 'completed') {
                $query = $query->completed();
            } elseif ($request->status === 'pending') {
                $query = $query->pending();
            }
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query = $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tasks = $query->orderBy('due_date', 'asc')
                      ->orderBy('created_at', 'desc')
                      ->paginate($request->per_page ?? 10);

        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Apenas admins podem criar tarefas
        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date|after_or_equal:today',
            'user_id' => 'required|exists:users,id',
        ]);

        $task = Task::create($validated);
        $task->load('user');

        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): JsonResponse
    {
        $user = Auth::user();
        
        // Verificar se o usuário pode ver esta tarefa
        if (!$user->isAdmin() && $task->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->load('user');
        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $user = Auth::user();
        
        // Verificar permissões
        if (!$user->isAdmin() && $task->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $rules = [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'due_date' => 'sometimes|nullable|date|after_or_equal:today',
            'completed' => 'sometimes|boolean',
        ];

        // Apenas admins podem alterar o usuário da tarefa
        if ($user->isAdmin()) {
            $rules['user_id'] = 'sometimes|exists:users,id';
        }

        $validated = $request->validate($rules);

        $task->update($validated);
        $task->load('user');

        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        $user = Auth::user();
        
        // Verificar permissões
        if (!$user->isAdmin() && $task->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }

    /**
     * Toggle task completion status.
     */
    public function toggle(Task $task): JsonResponse
    {
        $user = Auth::user();
        
        // Verificar permissões
        if (!$user->isAdmin() && $task->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->completed = !$task->completed;
        $task->save();
        $task->load('user');

        return response()->json($task);
    }

    /**
     * Export tasks to CSV.
     */
    public function export(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = Auth::user();
        $query = Task::with('user');

        // Se não for admin, exportar apenas suas próprias tarefas
        if (!$user->isAdmin()) {
            $query = $query->where('user_id', $user->id);
        }

        $tasks = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="tasks.csv"',
        ];

        return response()->streamDownload(function () use ($tasks) {
            $handle = fopen('php://output', 'w');
            
            // Cabeçalhos do CSV
            fputcsv($handle, [
                'ID',
                'Title',
                'Description',
                'Due Date',
                'Status',
                'User',
                'Created At',
                'Updated At'
            ]);

            // Dados
            foreach ($tasks as $task) {
                fputcsv($handle, [
                    $task->id,
                    $task->title,
                    $task->description,
                    $task->due_date?->format('Y-m-d'),
                    $task->completed ? 'Completed' : 'Pending',
                    $task->user->name,
                    $task->created_at->format('Y-m-d H:i:s'),
                    $task->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 'tasks.csv', $headers);
    }
}
