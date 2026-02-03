<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProblemCategory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProblemCategoryController extends Controller
{
    /**
     * ============================================================
     * LISTAGEM
     * ============================================================
     */
    public function index()
    {
        $categories = ProblemCategory::with('product')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.problem-categories.index', compact('categories'));
    }

    /**
     * ============================================================
     * FORMULÁRIO DE CRIAÇÃO
     * ============================================================
     */
    public function create()
    {
        $products = Product::active()->orderBy('name')->get();

        return view('admin.problem-categories.create', compact('products'));
    }

    /**
     * ============================================================
     * ARMAZENAR
     * ============================================================
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_type' => 'required|in:incident,service_request,improvement,purchase',
            'default_priority' => 'required|in:low,medium,high,critical',
            'sort_order' => 'nullable|integer',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = true;

        ProblemCategory::create($data);

        return redirect()
            ->route('admin.problem-categories.index')
            ->with('success', 'Categoria criada com sucesso.');
    }

    /**
     * ============================================================
     * FORMULÁRIO DE EDIÇÃO
     * ============================================================
     */
    public function edit(ProblemCategory $problemCategory)
    {
        $products = Product::active()->orderBy('name')->get();

        return view('admin.problem-categories.edit', [
            'problemCategory' => $problemCategory,
            'products' => $products,
        ]);
    }

    /**
     * ============================================================
     * ATUALIZAR
     * ============================================================
     */
    public function update(Request $request, ProblemCategory $problemCategory)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_type' => 'required|in:incident,service_request,improvement,purchase',
            'default_priority' => 'required|in:low,medium,high,critical',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $data['slug'] = Str::slug($data['name']);

        $problemCategory->update($data);

        return redirect()
            ->route('admin.problem-categories.index')
            ->with('success', 'Categoria atualizada com sucesso.');
    }

    /**
     * ============================================================
     * INATIVAR
     * ============================================================
     */
    public function destroy(ProblemCategory $problemCategory)
    {
        $problemCategory->update([
            'is_active' => false,
        ]);

        return back()->with('success', 'Categoria inativada com sucesso.');
    }
}
