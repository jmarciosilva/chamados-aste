<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportGroup;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Controller responsável pelo CRUD de Grupos de Atendimento
 * (ITIL v4 - Support Groups)
 */
class SupportGroupController extends Controller
{
    /**
     * Lista os grupos de atendimento
     */
    public function index()
    {
        $groups = SupportGroup::orderBy('name')->paginate(10);

        return view('admin.support-groups.index', compact('groups'));
    }

    /**
     * Formulário de criação de grupo
     */
    public function create()
    {
        // Lista apenas técnicos/agentes
        $technicians = User::where('role', 'agent')
            ->orderBy('name')
            ->get();

        return view('admin.support-groups.create', compact('technicians'));
    }

    /**
     * Salva um novo grupo de atendimento
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:support_groups,code',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'is_entry_point' => 'sometimes|boolean',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        /**
         * Criação do grupo
         * created_by vem do usuário autenticado (admin)
         */
        $group = SupportGroup::create([
            'name' => $data['name'],
            'code' => strtoupper($data['code']),
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'is_entry_point' => $data['is_entry_point'] ?? false,
            'created_by' => auth()->id(),
        ]);

        // Associação dos técnicos ao grupo
        if (! empty($data['users'])) {
            $group->users()->sync($data['users']);
        }

        return redirect()
            ->route('admin.support-groups.index')
            ->with('success', 'Grupo de atendimento criado com sucesso.');
    }

    /**
     * Formulário de edição do grupo
     */
    public function edit(SupportGroup $supportGroup)
    {
        $technicians = User::where('role', 'agent')
            ->orderBy('name')
            ->get();

        return view('admin.support-groups.edit', compact('supportGroup', 'technicians'));
    }

    /**
     * Atualiza um grupo de atendimento
     */
    public function update(Request $request, SupportGroup $supportGroup)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:support_groups,code,'.$supportGroup->id,
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'is_entry_point' => 'sometimes|boolean',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        // Atualiza dados do grupo
        $supportGroup->update([
            'name' => $data['name'],
            'code' => strtoupper($data['code']),
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? false,
            'is_entry_point' => $data['is_entry_point'] ?? false,
        ]);

        // Sincroniza técnicos do grupo
        $supportGroup->users()->sync($data['users'] ?? []);

        return redirect()
            ->route('admin.support-groups.index')
            ->with('success', 'Grupo de atendimento atualizado com sucesso.');
    }

    /**
     * Remove um grupo de atendimento
     */
    public function destroy(SupportGroup $supportGroup)
    {
        $supportGroup->delete();

        return redirect()
            ->route('admin.support-groups.index')
            ->with('success', 'Grupo de atendimento removido.');
    }

    /**
     * --------------------------------------------------------------
     * GERENCIAR TÉCNICOS DO GRUPO
     * --------------------------------------------------------------
     */
    public function agents(SupportGroup $supportGroup)
    {
        $agents = User::where('role', 'agent')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'admin.support-groups.agents',
            compact('supportGroup', 'agents')
        );
    }

    /**
     * --------------------------------------------------------------
     * ATUALIZAR TÉCNICOS DO GRUPO
     * --------------------------------------------------------------
     */
    public function updateAgents(Request $request, SupportGroup $supportGroup)
    {
        $data = $request->validate([
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        $supportGroup->users()->sync($data['users'] ?? []);

        return redirect()
            ->route('admin.support-groups.index')
            ->with('success', 'Técnicos do grupo atualizados com sucesso.');
    }
}
