<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * ------------------------------------------------------------------
     * LISTAGEM DE USUÁRIOS
     * ------------------------------------------------------------------
     * Exibe todos os usuários cadastrados no sistema
     * para gestão administrativa.
     */
    public function index()
    {
        $users = User::with('department')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * ------------------------------------------------------------------
     * FORMULÁRIO DE CRIAÇÃO
     * ------------------------------------------------------------------
     * Exibe a tela para cadastro de novo usuário.
     */
    public function create()
    {
        $departments = Department::orderBy('name')->get();

        return view('admin.users.create', compact('departments'));
    }

    /**
     * ------------------------------------------------------------------
     * SALVAR NOVO USUÁRIO
     * ------------------------------------------------------------------
     * Processa o formulário de criação.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6',
            'role'          => 'required|in:user,agent,admin',
            'department_id' => 'nullable|exists:departments,id',
            'job_title'     => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:50',
            'is_active'     => 'boolean',
        ]);

        User::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'password'      => Hash::make($validated['password']),
            'role'          => $validated['role'],
            'department_id' => $validated['department_id'] ?? null,
            'job_title'     => $validated['job_title'] ?? null,
            'phone'         => $validated['phone'] ?? null,
            'is_active'     => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuário criado com sucesso.');
    }

    /**
     * ------------------------------------------------------------------
     * FORMULÁRIO DE EDIÇÃO
     * ------------------------------------------------------------------
     */
    public function edit(User $user)
    {
        $departments = Department::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'departments'));
    }

    /**
     * ------------------------------------------------------------------
     * ATUALIZAR USUÁRIO
     * ------------------------------------------------------------------
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'password'      => 'nullable|min:6',
            'role'          => 'required|in:user,agent,admin',
            'department_id' => 'nullable|exists:departments,id',
            'job_title'     => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:50',
            'is_active'     => 'boolean',
        ]);

        $user->fill([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'role'          => $validated['role'],
            'department_id' => $validated['department_id'] ?? null,
            'job_title'     => $validated['job_title'] ?? null,
            'phone'         => $validated['phone'] ?? null,
            'is_active'     => $request->boolean('is_active'),
        ]);

        // Só altera a senha se o admin informar uma nova
        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    /**
     * ------------------------------------------------------------------
     * ATIVAR / INATIVAR USUÁRIO
     * ------------------------------------------------------------------
     * Em vez de excluir, apenas alterna o status.
     */
    public function toggleStatus(User $user)
    {
        $user->is_active = ! $user->is_active;
        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Status do usuário atualizado.');
    }
}
