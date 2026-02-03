<?php

namespace Database\Seeders;

use App\Models\ProblemCategory;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProblemCategorySeeder extends Seeder
{
    /**
     * ------------------------------------------------------------------
     * CATEGORIAS DE PROBLEMAS
     * ------------------------------------------------------------------
     * NÃO existe impacto aqui.
     * Impacto pertence exclusivamente ao PRODUTO.
     */
    public function run(): void
    {
        $structure = [
            'Acesso / Login' => [
                [
                    'name' => 'Problemas de Login',
                    'description' => 'Senha inválida, usuário bloqueado ou acesso negado.',
                ],
            ],

            'SIGE' => [
                [
                    'name' => 'Pedidos e Fiscal',
                    'description' => 'Pedidos, fiscal, compras, erros em telas.',
                ],
            ],

            'PDV' => [
                [
                    'name' => 'Venda no PDV',
                    'description' => 'Venda travada, produto sem estoque.',
                ],
            ],

            'WEBB LOJA' => [
                [
                    'name' => 'Social Seller',
                    'description' => 'Vendas via WhatsApp e Web.',
                ],
            ],

            'E-commerce' => [
                [
                    'name' => 'Pedidos Online',
                    'description' => 'Checkout, pagamentos, pedidos online.',
                ],
            ],

            'OmniChannel' => [
                [
                    'name' => 'Integrações',
                    'description' => 'Integração loja física e online.',
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
                    'description' => 'Pedidos atacado, portal Centelha.',
                ],
            ],
        ];

        foreach ($structure as $productName => $categories) {

            $product = Product::where('name', $productName)->first();

            if (! $product) {
                continue;
            }

            foreach ($categories as $index => $item) {

                ProblemCategory::firstOrCreate(
                    [
                        'slug' => Str::slug($productName . '-' . $item['name']),
                    ],
                    [
                        'product_id'       => $product->id,
                        'name'             => $item['name'],
                        'description'      => $item['description'],
                        'service_type'     => 'incident',
                        'default_priority' => 'low',
                        'is_active'        => true,
                        'sort_order'       => $index + 1,
                    ]
                );
            }
        }
    }
}
