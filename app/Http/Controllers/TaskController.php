<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class TaskController extends Controller
{

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

    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Apenas admins podem criar tarefas
        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Apenas administradores podem criar novas tarefas'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'due_date' => 'nullable|date|after_or_equal:today',
            'user_id' => 'required|exists:users,id',
        ], [
            'title.required' => 'O título é obrigatório',
            'title.string' => 'O título deve ser um texto',
            'title.max' => 'O título deve ter no máximo 255 caracteres',
            'description.string' => 'A descrição deve ser um texto',
            'description.max' => 'A descrição deve ter no máximo 1000 caracteres',
            'due_date.date' => 'A data de vencimento deve ser uma data válida',
            'due_date.after_or_equal' => 'A data de vencimento deve ser hoje ou uma data futura',
            'user_id.required' => 'Selecione um usuário para atribuir a tarefa',
            'user_id.exists' => 'O usuário selecionado não existe',
        ]);

        // Garantir que completed seja definido como false por padrão
        $validated['completed'] = false;

        $task = Task::create($validated);
        $task->load('user');
        $task->refresh(); // Garante que os valores padrão sejam carregados

        return response()->json($task, 201);
    }

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

    public function update(Request $request, Task $task): JsonResponse
    {
        $user = Auth::user();
        
        // Verificar permissões
        if (!$user->isAdmin() && $task->user_id !== $user->id) {
            return response()->json(['error' => 'Você não tem permissão para editar esta tarefa'], 403);
        }

        $rules = [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string|max:1000',
            'due_date' => 'sometimes|nullable|date|after_or_equal:today',
            'completed' => 'sometimes|boolean',
        ];

        $messages = [
            'title.required' => 'O título é obrigatório',
            'title.string' => 'O título deve ser um texto',
            'title.max' => 'O título deve ter no máximo 255 caracteres',
            'description.string' => 'A descrição deve ser um texto',
            'description.max' => 'A descrição deve ter no máximo 1000 caracteres',
            'due_date.date' => 'A data de vencimento deve ser uma data válida',
            'due_date.after_or_equal' => 'A data de vencimento deve ser hoje ou uma data futura',
            'user_id.exists' => 'O usuário selecionado não existe',
        ];

        // Apenas admins podem alterar o usuário da tarefa
        if ($user->isAdmin()) {
            $rules['user_id'] = 'sometimes|exists:users,id';
        }

        $validated = $request->validate($rules, $messages);

        $task->update($validated);
        $task->load('user');

        return response()->json($task);
    }

    public function destroy(Task $task): JsonResponse
    {
        $user = Auth::user();
        
        // Log para debug
        \Log::info('Tentativa de exclusão de tarefa', [
            'task_id' => $task->id,
            'user_id' => $user ? $user->id : 'null',
            'user_role' => $user ? $user->role : 'null',
            'task_user_id' => $task->user_id
        ]);
        
        // Verificar se o usuário está autenticado
        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }
        
        // Verificar permissões
        if (!$user->isAdmin() && $task->user_id !== $user->id) {
            return response()->json(['error' => 'Você não tem permissão para excluir esta tarefa'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Tarefa excluída com sucesso']);
    }

    public function toggle(Task $task): JsonResponse
    {
        $user = Auth::user();
        
        // Verificar permissões
        if (!$user->isAdmin() && $task->user_id !== $user->id) {
            return response()->json(['error' => 'Você não tem permissão para alterar esta tarefa'], 403);
        }

        $task->completed = !$task->completed;
        $task->save();
        $task->load('user');

        return response()->json($task);
    }

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

    public function indexPage(Request $request)
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
                      ->paginate($request->per_page ?? 5); // Padrão de 5 tarefas por página

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'users' => $user->isAdmin() ? \App\Models\User::all(['id', 'name', 'email']) : [],
            'filters' => $request->only(['status', 'search']),
            'currentUser' => $user,
        ]);
    }
}
