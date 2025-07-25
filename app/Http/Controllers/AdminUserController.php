<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AdminUserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filtro por pesquisa
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Filtro por role
        if ($request->filled('role')) {
            $query->where('role', $request->get('role'));
        }

        $users = $query->orderBy('created_at', 'desc')
                      ->paginate(10)
                      ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role']),
            'stats' => [
                'total' => User::count(),
                'admins' => User::where('role', 'admin')->count(),
                'users' => User::where('role', 'user')->count(),
            ]
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return Inertia::render('Admin/Users/Create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users.index')
                        ->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['tasks' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }]);

        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
            'taskStats' => [
                'total' => $user->tasks()->count(),
                'pending' => $user->tasks()->where('status', 'pending')->count(),
                'in_progress' => $user->tasks()->where('status', 'in_progress')->count(),
                'completed' => $user->tasks()->where('status', 'completed')->count(),
            ]
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,user',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        if ($validated['password']) {
            $user->update([
                'password' => Hash::make($validated['password'])
            ]);
        }

        return redirect()->route('admin.users.index')
                        ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Não permitir que o admin delete a si mesmo
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                            ->with('error', 'Você não pode deletar sua própria conta.');
        }

        // Verificar se é o último admin
        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return redirect()->route('admin.users.index')
                            ->with('error', 'Não é possível deletar o último administrador do sistema.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
                        ->with('success', 'Usuário deletado com sucesso!');
    }
}
