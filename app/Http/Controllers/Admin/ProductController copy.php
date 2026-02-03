<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * ============================================================
     * LISTAGEM
     * ============================================================
     */
    public function index()
    {
        $products = Product::orderBy('name')->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    /**
     * ============================================================
     * FORMULÁRIO DE CRIAÇÃO
     * ============================================================
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * ============================================================
     * ARMAZENAR
     * ============================================================
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            
            // ✅ VALIDAÇÃO DOS SLAs (8 campos)
            'sla.low.response_hours' => 'required|integer|min:1',
            'sla.low.resolution_hours' => 'required|integer|min:1',
            'sla.medium.response_hours' => 'required|integer|min:1',
            'sla.medium.resolution_hours' => 'required|integer|min:1',
            'sla.high.response_hours' => 'required|integer|min:1',
            'sla.high.resolution_hours' => 'required|integer|min:1',
            'sla.critical.response_hours' => 'required|integer|min:1',
            'sla.critical.resolution_hours' => 'required|integer|min:1',
        ]);

        // Cria o produto
        Product::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'sla_config' => $data['sla'], // ✅ Laravel converte automaticamente para JSON
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produto criado com sucesso.');
    }

    /**
     * ============================================================
     * FORMULÁRIO DE EDIÇÃO
     * ============================================================
     */
    public function edit(Product $product)
    {
        // Garante que o produto tem configuração de SLA
        if (!$product->sla_config) {
            $product->initializeSlaConfig();
        }

        return view('admin.products.edit', compact('product'));
    }

    /**
     * ============================================================
     * ATUALIZAR
     * ============================================================
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            
            // ✅ VALIDAÇÃO DOS SLAs (8 campos)
            'sla.low.response_hours' => 'required|integer|min:1',
            'sla.low.resolution_hours' => 'required|integer|min:1',
            'sla.medium.response_hours' => 'required|integer|min:1',
            'sla.medium.resolution_hours' => 'required|integer|min:1',
            'sla.high.response_hours' => 'required|integer|min:1',
            'sla.high.resolution_hours' => 'required|integer|min:1',
            'sla.critical.response_hours' => 'required|integer|min:1',
            'sla.critical.resolution_hours' => 'required|integer|min:1',
        ]);

        // Atualiza o produto
        $product->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'sla_config' => $data['sla'], // ✅ Laravel converte automaticamente para JSON
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produto atualizado com sucesso.');
    }

    /**
     * ============================================================
     * ATIVAR / INATIVAR
     * ============================================================
     */
    public function destroy(Product $product)
    {
        $product->update([
            'is_active' => !$product->is_active,
        ]);

        return back()->with('success', 'Status do produto atualizado.');
    }
}