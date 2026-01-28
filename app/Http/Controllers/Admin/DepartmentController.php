<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * ==========================================================
     * LISTAGEM DE DEPARTAMENTOS
     * ==========================================================
     */
    public function index()
    {
        $departments = Department::orderBy('name')->paginate(10);

        return view('admin.departments.index', compact('departments'));
    }

    /**
     * ==========================================================
     * FORMULÁRIO DE CRIAÇÃO
     * ==========================================================
     */
    public function create()
    {
        return view('admin.departments.create');
    }

    /**
     * ==========================================================
     * SALVAR NOVO DEPARTAMENTO
     * ==========================================================
     */
    public function store(Request $request)
    {
        /**
         * ----------------------------------------------------------
         * VALIDAÇÃO
         * - name obrigatório
         * - unique case-insensitive
         * ----------------------------------------------------------
         */
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $exists = Department::whereRaw(
                        'LOWER(name) = ?',
                        [mb_strtolower($value)]
                    )->exists();

                    if ($exists) {
                        $fail('Já existe um departamento com este nome.');
                    }
                },
            ],
        ]);

        /**
         * ----------------------------------------------------------
         * CREATE
         * ----------------------------------------------------------
         */
        Department::create([
            'name'      => trim($validated['name']),
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Departamento criado com sucesso.');
    }

    /**
     * ==========================================================
     * FORMULÁRIO DE EDIÇÃO
     * ==========================================================
     */
    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * ==========================================================
     * ATUALIZAR DEPARTAMENTO
     * ==========================================================
     */
    public function update(Request $request, Department $department)
    {
        /**
         * ----------------------------------------------------------
         * VALIDAÇÃO
         * - Ignora o próprio registro
         * ----------------------------------------------------------
         */
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($department) {
                    $exists = Department::whereRaw(
                        'LOWER(name) = ?',
                        [mb_strtolower($value)]
                    )
                    ->where('id', '!=', $department->id)
                    ->exists();

                    if ($exists) {
                        $fail('Já existe outro departamento com este nome.');
                    }
                },
            ],
            'is_active' => 'boolean',
        ]);

        /**
         * ----------------------------------------------------------
         * UPDATE
         * ----------------------------------------------------------
         */
        $department->update([
            'name'      => trim($validated['name']),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Departamento atualizado com sucesso.');
    }

    /**
     * ==========================================================
     * ATIVAR / INATIVAR
     * ==========================================================
     */
    public function toggleStatus(Department $department)
    {
        $department->update([
            'is_active' => ! $department->is_active,
        ]);

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Status do departamento atualizado.');
    }
}
