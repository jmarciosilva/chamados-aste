<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProblemCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProblemCategorySeeder extends Seeder
{
    /**
     * ------------------------------------------------------------------
     * CATEGORIAS DE PROBLEMAS – SEED INICIAL
     * ------------------------------------------------------------------
     * Cada categoria pertence obrigatoriamente a um Produto.
     * Estas categorias servem como base inicial e podem ser
     * alteradas via CRUD Admin futuramente.
     */
    public function run(): void
    {
        $categories = [
            'Acesso / Login' => [
                [
                    'name' => 'Problemas de Login',
                    'description' => 'Senha inválida, usuário bloqueado, acesso a sistemas.',
                ],
            ],

            'SIGE' => [
                [
                    'name' => 'Pedidos e Fiscal',
                    'description' => 'Pedidos, fiscal, compras, erros em telas.',
                ],
            ],

            'WEBB LOJA' => [
                [
                    'name' => 'Social Seller',
                    'description' => 'Vendas, Social Seller, entrada de NF.',
                ],
            ],

            'PDV' => [
                [
                    'name' => 'Venda no PDV',
                    'description' => 'Venda travada, produto sem estoque.',
                ],
            ],

            'OmniChannel' => [
                [
                    'name' => 'Integrações',
                    'description' => 'Integração loja física e online.',
                ],
            ],

            'E-commerce' => [
                [
                    'name' => 'Pedidos Online',
                    'description' => 'Pedidos online, pagamentos, checkout.',
                ],
            ],

            'Vejo Varejo' => [
                [
                    'name' => 'Operações de Loja',
                    'description' => 'Vendas, estoque, preços, clientes.',
                ],
            ],

            'Infraestrutura / Equipamentos' => [
                [
                    'name' => 'Infraestrutura',
                    'description' => 'Impressora, Wi-Fi, rede, celular.',
                ],
            ],

            'Centelha B2B' => [
                [
                    'name' => 'Pedidos Atacado',
                    'description' => 'Pedidos atacado, portal B2B.',
                ],
            ],
        ];

        foreach ($categories as $productName => $items) {

            $product = Product::where('name', $productName)->first();

            if (! $product) {
                continue;
            }

            foreach ($items as $index => $category) {

                ProblemCategory::firstOrCreate(
                    [
                        'slug' => Str::slug($productName.'-'.$category['name']),
                    ],
                    [
                        'product_id' => $product->id,
                        'name' => $category['name'],
                        'description' => $category['description'],
                        'service_type' => 'incident',
                        'default_priority' => 'low',
                        'is_active' => true,
                        'sort_order' => $index + 1,
                    ]
                );
            }
        }
    }
}
