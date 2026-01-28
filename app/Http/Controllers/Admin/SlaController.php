<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sla;
use Illuminate\Http\Request;

class SlaController extends Controller
{
    /**
     * ==========================================================
     * MAPA DE PRIORIDADES (ENUM DO BANCO → LABEL)
     * ==========================================================
     */
    private const PRIORITIES = [
        'critica' => 'Crítica',
        'alta' => 'Alta',
        'media' => 'Média',
        'baixa' => 'Baixa',
    ];

    /**
     * ==========================================================
     * LISTAGEM
     * ==========================================================
     */
    public function index()
    {
        $slas = Sla::orderBy('priority')->get();

        return view('admin.slas.index', compact('slas'));
    }

    /**
     * ============================================================
     * FORMULÁRIO DE CRIAÇÃO DE SLA
     * ============================================================
     */
    public function create()
    {
        /**
         * ------------------------------------------------------------
         * PRODUTOS ATIVOS
         * ------------------------------------------------------------
         */
        $products = Product::active()
            ->orderBy('name')
            ->get();

        return view('admin.slas.create', compact('products'));
    }

    /**
     * ==========================================================
     * STORE
     * ==========================================================
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'priority' => 'required|in:'.implode(',', array_keys(self::PRIORITIES)),
            'response_time_hours' => 'required|integer|min:1',
            'resolution_time_hours' => 'required|integer|min:1',
        ]);

        Sla::create([
            'name' => $validated['name'],
            'priority' => $validated['priority'],
            'response_time_hours' => $validated['response_time_hours'],
            'resolution_time_hours' => $validated['resolution_time_hours'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('admin.slas.index')
            ->with('success', 'Regra de SLA criada com sucesso.');
    }

    /**
     * ==========================================================
     * EDIT
     * ==========================================================
     */
    public function edit(Sla $sla)
    {
        $priorities = self::PRIORITIES;

        return view('admin.slas.edit', compact('sla', 'priorities'));
    }

    /**
     * ==========================================================
     * UPDATE (CORRIGIDO)
     * ==========================================================
     */
    public function update(Request $request, Sla $sla)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'priority' => 'required|in:'.implode(',', array_keys(self::PRIORITIES)),
            'response_time_hours' => 'required|integer|min:1',
            'resolution_time_hours' => 'required|integer|min:1',
        ]);

        $sla->update([
            'name' => $validated['name'],
            'priority' => $validated['priority'],
            'response_time_hours' => $validated['response_time_hours'],
            'resolution_time_hours' => $validated['resolution_time_hours'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('admin.slas.index')
            ->with('success', 'Regra de SLA atualizada com sucesso.');
    }

    /**
     * ==========================================================
     * ATIVAR / DESATIVAR
     * ==========================================================
     */
    public function toggleStatus(Sla $sla)
    {
        $sla->update([
            'is_active' => ! $sla->is_active,
        ]);

        return back();
    }
}
