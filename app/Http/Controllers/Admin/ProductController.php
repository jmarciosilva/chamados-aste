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
        ]);

        $data['slug'] = Str::slug($data['name']);

        Product::create($data);

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
        ]);

        $data['slug'] = Str::slug($data['name']);

        $product->update($data);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produto atualizado com sucesso.');
    }

    /**
     * ============================================================
     * INATIVAR (NÃO EXCLUIR)
     * ============================================================
     */
    public function destroy(Product $product)
    {
        $product->update([
            'is_active' => ! $product->is_active,
        ]);

        return back()->with('success', 'Status do produto atualizado.');
    }
}
