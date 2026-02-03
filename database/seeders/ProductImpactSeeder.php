<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImpactQuestion;
use Illuminate\Database\Seeder;

class ProductImpactSeeder extends Seeder
{
    /**
     * ------------------------------------------------------------------
     * PERGUNTAS DE IMPACTO POR PRODUTO
     * ------------------------------------------------------------------
     * Cada PRODUTO possui:
     * - 1 pergunta de impacto
     * - 4 respostas
     * - Cada resposta define uma criticidade inicial
     *
     * A criticidade pode ser ajustada posteriormente pelo operador.
     */
    public function run(): void
    {
        $impacts = [

            // ----------------------------------------------------------
            // ACESSO / LOGIN
            // ----------------------------------------------------------
            'Acesso / Login' => [
                'question' => 'Você consegue acessar os sistemas no momento?',
                'answers' => [
                    'low'      => 'Consigo acessar normalmente, apenas com lentidão',
                    'medium'   => 'Consigo acessar apenas alguns sistemas',
                    'high'     => 'Não consigo acessar sistemas importantes',
                    'critical' => 'Não consigo acessar nenhum sistema',
                ],
            ],

            // ----------------------------------------------------------
            // SISTEMA SIGE
            // ----------------------------------------------------------
            'Sistema SIGE' => [
                'question' => 'Qual o impacto do problema no faturamento?',
                'answers' => [
                    'low'      => 'Impacto mínimo, sem atraso',
                    'medium'   => 'Atraso pontual em pedidos',
                    'high'     => 'Pedidos bloqueados',
                    'critical' => 'Faturamento totalmente parado',
                ],
            ],

            // ----------------------------------------------------------
            // WEBB LOJA
            // ----------------------------------------------------------
            'WEBB LOJA' => [
                'question' => 'O problema impede novas vendas?',
                'answers' => [
                    'low'      => 'Apenas dificuldades pontuais',
                    'medium'   => 'Algumas vendas não concluem',
                    'high'     => 'Maioria das vendas bloqueada',
                    'critical' => 'Nenhuma venda possível',
                ],
            ],

            // ----------------------------------------------------------
            // SISTEMA PDV
            // ----------------------------------------------------------
            'Sistema PDV' => [
                'question' => 'O PDV está permitindo realizar vendas?',
                'answers' => [
                    'low'      => 'Funciona com lentidão',
                    'medium'   => 'Funciona parcialmente',
                    'high'     => 'Venda bloqueada em alguns caixas',
                    'critical' => 'Nenhuma venda pode ser realizada',
                ],
            ],

            // ----------------------------------------------------------
            // SISTEMA OMNICHANNEL
            // ----------------------------------------------------------
            'Sistema OmniChannel' => [
                'question' => 'As integrações estão sincronizando corretamente?',
                'answers' => [
                    'low'      => 'Atrasos pequenos',
                    'medium'   => 'Falhas pontuais',
                    'high'     => 'Integrações interrompidas',
                    'critical' => 'Nenhuma sincronização ativa',
                ],
            ],

            // ----------------------------------------------------------
            // PLATAFORMA E-COMMERCE
            // ----------------------------------------------------------
            'Plataforma E-commerce' => [
                'question' => 'Clientes conseguem finalizar compras?',
                'answers' => [
                    'low'      => 'Compras com lentidão',
                    'medium'   => 'Falhas ocasionais',
                    'high'     => 'Muitos pedidos falhando',
                    'critical' => 'Checkout totalmente indisponível',
                ],
            ],

            // ----------------------------------------------------------
            // SISTEMA VEJO VAREJO
            // ----------------------------------------------------------
            'Sistema Vejo Varejo' => [
                'question' => 'O problema afeta operações de loja?',
                'answers' => [
                    'low'      => 'Apenas pequenos impactos',
                    'medium'   => 'Operação parcialmente afetada',
                    'high'     => 'Vendas ou estoque comprometidos',
                    'critical' => 'Operação da loja parada',
                ],
            ],

            // ----------------------------------------------------------
            // INFRAESTRUTURA / EQUIPAMENTOS
            // ----------------------------------------------------------
            'Infraestrutura / Equipamentos' => [
                'question' => 'O equipamento está funcional?',
                'answers' => [
                    'low'      => 'Funciona com limitações',
                    'medium'   => 'Funciona parcialmente',
                    'high'     => 'Equipamento indisponível',
                    'critical' => 'Operação totalmente parada',
                ],
            ],

            // ----------------------------------------------------------
            // SISTEMA CENTELHA B2B
            // ----------------------------------------------------------
            'Sistema Centelha B2B' => [
                'question' => 'Pedidos do portal Centelha estão sendo processados?',
                'answers' => [
                    'low'      => 'Processamento lento',
                    'medium'   => 'Pedidos com erro',
                    'high'     => 'Pedidos bloqueados',
                    'critical' => 'Portal indisponível',
                ],
            ],
        ];

        foreach ($impacts as $productName => $impactData) {

            $product = Product::where('name', $productName)->first();

            if (! $product) {
                continue;
            }

            // ----------------------------------------------------------
            // PERGUNTA DE IMPACTO
            // ----------------------------------------------------------
            $question = ProductImpactQuestion::firstOrCreate(
                [
                    'product_id' => $product->id,
                ],
                [
                    'question' => $impactData['question'],
                ]
            );

            // ----------------------------------------------------------
            // RESPOSTAS DE IMPACTO
            // ----------------------------------------------------------
            foreach ($impactData['answers'] as $priority => $label) {
                $question->answers()->firstOrCreate(
                    [
                        'priority' => $priority,
                    ],
                    [
                        'label' => $label,
                    ]
                );
            }
        }
    }
}
