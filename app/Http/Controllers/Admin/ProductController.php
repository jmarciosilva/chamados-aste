<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImpactAnswer;
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
        $data = $this->validateProduct($request);

        // ------------------------------------------------------------------
        // CRIA O PRODUTO
        // ------------------------------------------------------------------
        $product = Product::create([
            'name'        => $data['name'],
            'slug'        => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'is_active'   => $request->boolean('is_active', true),
            'sla_config'  => $data['sla'],
        ]);

        // ------------------------------------------------------------------
        // SALVA PERGUNTA DE IMPACTO
        // ------------------------------------------------------------------
        if (!empty($data['impact'])) {
            $this->storeImpactQuestion($product, $data['impact']);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Serviço criado com sucesso.');
    }

    /**
     * ============================================================
     * FORMULÁRIO DE EDIÇÃO
     * ============================================================
     */
    public function edit(Product $product)
    {
        if (! $product->sla_config) {
            $product->initializeSlaConfig();
        }

        $product->load('impactQuestion.answers');

        return view('admin.products.edit', compact('product'));
    }

    /**
     * ============================================================
     * ATUALIZAR
     * ============================================================
     */
    public function update(Request $request, Product $product)
    {
        $data = $this->validateProduct($request, $product->id);

        // ------------------------------------------------------------------
        // ATUALIZA PRODUTO
        // ------------------------------------------------------------------
        $product->update([
            'name'        => $data['name'],
            'slug'        => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'is_active'   => $request->boolean('is_active'),
            'sla_config'  => $data['sla'],
        ]);

        // ------------------------------------------------------------------
        // ATUALIZA PERGUNTA DE IMPACTO
        // ------------------------------------------------------------------
        if (!empty($data['impact'])) {
            $product->impactQuestion()?->answers()->delete();
            $product->impactQuestion()?->delete();

            $this->storeImpactQuestion($product, $data['impact']);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Serviço atualizado com sucesso.');
    }

    /**
     * ============================================================
     * ATIVAR / INATIVAR
     * ============================================================
     */
    public function destroy(Product $product)
    {
        $product->update([
            'is_active' => ! $product->is_active,
        ]);

        return back()->with('success', 'Status do serviço atualizado.');
    }

    /**
     * ============================================================
     * MÉTODOS AUXILIARES (PRIVATE)
     * ============================================================
     */

    /**
     * Validação centralizada
     */
    private function validateProduct(Request $request, ?int $productId = null): array
    {
        return $request->validate([
            'name'        => 'required|string|max:255|unique:products,name,' . $productId,
            'description' => 'nullable|string',
            'is_active'   => 'boolean',

            // SLA
            'sla.low.response_hours'       => 'required|integer|min:1',
            'sla.low.resolution_hours'     => 'required|integer|min:1',
            'sla.medium.response_hours'    => 'required|integer|min:1',
            'sla.medium.resolution_hours'  => 'required|integer|min:1',
            'sla.high.response_hours'      => 'required|integer|min:1',
            'sla.high.resolution_hours'    => 'required|integer|min:1',
            'sla.critical.response_hours'  => 'required|integer|min:1',
            'sla.critical.resolution_hours'=> 'required|integer|min:1',

            // Pergunta de impacto
            'impact.question' => 'required|string|max:255',
            'impact.answers'  => 'required|array|size:4',
        ]);
    }

    /**
     * Persistência da pergunta de impacto
     */
    private function storeImpactQuestion(Product $product, array $impact): void
    {
        $question = $product->impactQuestion()->create([
            'question' => $impact['question'],
        ]);

        foreach ($impact['answers'] as $priority => $label) {
            $question->answers()->create([
                'label'    => $label,
                'priority' => $priority,
            ]);
        }
    }
}
