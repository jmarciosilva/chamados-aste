<?php

namespace App\Http\Controllers;

use App\Models\SupportGroup;
use Illuminate\Http\Request;

class AgentSupportGroupController extends Controller
{
    /**
     * --------------------------------------------------------------
     * LISTAGEM DE GRUPOS
     * --------------------------------------------------------------
     */
    public function index()
    {
        $groups = SupportGroup::orderBy('name')->paginate(10);

        return view('agent.support-groups.index', compact('groups'));
    }

    /**
     * --------------------------------------------------------------
     * FORMULÁRIO DE CRIAÇÃO
     * --------------------------------------------------------------
     */
    public function create()
    {
        return view('agent.support-groups.create');
    }

    /**
     * --------------------------------------------------------------
     * ARMAZENAR NOVO GRUPO
     * --------------------------------------------------------------
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'code'            => 'required|string|max:50|unique:support_groups,code',
            'description'     => 'nullable|string',
            'is_entry_point'  => 'nullable|boolean',
        ]);

        SupportGroup::create([
            'name'           => $validated['name'],
            'code'           => strtoupper($validated['code']),
            'description'    => $validated['description'] ?? null,
            'is_entry_point' => $request->boolean('is_entry_point'),
            'is_active'      => true,
            'created_by'     => auth()->id(),
        ]);

        return redirect()
            ->route('agent.support-groups.index')
            ->with('success', 'Grupo criado com sucesso.');
    }

    /**
     * --------------------------------------------------------------
     * FORMULÁRIO DE EDIÇÃO
     * --------------------------------------------------------------
     */
    public function edit(SupportGroup $supportGroup)
    {
        return view('agent.support-groups.edit', compact('supportGroup'));
    }

    /**
     * --------------------------------------------------------------
     * ATUALIZAR GRUPO
     * --------------------------------------------------------------
     */
    public function update(Request $request, SupportGroup $supportGroup)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'is_active'      => 'nullable|boolean',
            'is_entry_point' => 'nullable|boolean',
        ]);

        $supportGroup->update([
            'name'           => $validated['name'],
            'description'    => $validated['description'] ?? null,
            'is_active'      => $request->boolean('is_active'),
            'is_entry_point' => $request->boolean('is_entry_point'),
        ]);

        return redirect()
            ->route('agent.support-groups.index')
            ->with('success', 'Grupo atualizado com sucesso.');
    }
}
