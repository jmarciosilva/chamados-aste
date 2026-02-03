<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * ------------------------------------------------------------------
     * PRODUTOS DO HELP DESK – GRUPO ASTE
     * ------------------------------------------------------------------
     * VERSÃO ATUALIZADA: Agora inclui SLAs diretamente no produto
     * 
     * Estrutura de SLA:
     * {
     *   "low": {"response_hours": 24, "resolution_hours": 72},
     *   "medium": {"response_hours": 8, "resolution_hours": 24},
     *   "high": {"response_hours": 4, "resolution_hours": 12},
     *   "critical": {"response_hours": 2, "resolution_hours": 4}
     * }
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Acesso / Login',
                'description' => 'Problemas de acesso a sistemas, login, senhas e autenticação',
                'sla' => [
                    'critical' => ['response_hours' => 1, 'resolution_hours' => 2],
                    'high' => ['response_hours' => 2, 'resolution_hours' => 4],
                    'medium' => ['response_hours' => 4, 'resolution_hours' => 8],
                    'low' => ['response_hours' => 8, 'resolution_hours' => 24],
                ],
            ],
            [
                'name' => 'Sistema SIGE',
                'description' => 'Sistema integrado de gestão empresarial - ERP',
                'sla' => [
                    'critical' => ['response_hours' => 2, 'resolution_hours' => 4],
                    'high' => ['response_hours' => 4, 'resolution_hours' => 12],
                    'medium' => ['response_hours' => 8, 'resolution_hours' => 24],
                    'low' => ['response_hours' => 24, 'resolution_hours' => 72],
                ],
            ],
            [
                'name' => 'WEBB LOJA',
                'description' => 'Plataforma de vendas Webb Loja e Social Seller',
                'sla' => [
                    'critical' => ['response_hours' => 2, 'resolution_hours' => 6],
                    'high' => ['response_hours' => 4, 'resolution_hours' => 12],
                    'medium' => ['response_hours' => 8, 'resolution_hours' => 24],
                    'low' => ['response_hours' => 24, 'resolution_hours' => 72],
                ],
            ],
            [
                'name' => 'Sistema PDV',
                'description' => 'Sistema de ponto de venda - Frente de caixa',
                'sla' => [
                    // PDV tem SLA mais agressivo por ser crítico para operação
                    'critical' => ['response_hours' => 1, 'resolution_hours' => 2],
                    'high' => ['response_hours' => 2, 'resolution_hours' => 6],
                    'medium' => ['response_hours' => 4, 'resolution_hours' => 12],
                    'low' => ['response_hours' => 8, 'resolution_hours' => 24],
                ],
            ],
            [
                'name' => 'Sistema OmniChannel',
                'description' => 'Integrador de canais de venda online e offline',
                'sla' => [
                    'critical' => ['response_hours' => 2, 'resolution_hours' => 6],
                    'high' => ['response_hours' => 4, 'resolution_hours' => 12],
                    'medium' => ['response_hours' => 8, 'resolution_hours' => 24],
                    'low' => ['response_hours' => 24, 'resolution_hours' => 72],
                ],
            ],
            [
                'name' => 'Plataforma E-commerce',
                'description' => 'Plataforma de vendas online e marketplace',
                'sla' => [
                    'critical' => ['response_hours' => 2, 'resolution_hours' => 4],
                    'high' => ['response_hours' => 4, 'resolution_hours' => 12],
                    'medium' => ['response_hours' => 8, 'resolution_hours' => 24],
                    'low' => ['response_hours' => 24, 'resolution_hours' => 72],
                ],
            ],
            [
                'name' => 'Sistema Vejo Varejo',
                'description' => 'Plataforma Vejo Varejo de gestão comercial',
                'sla' => [
                    'critical' => ['response_hours' => 2, 'resolution_hours' => 6],
                    'high' => ['response_hours' => 4, 'resolution_hours' => 12],
                    'medium' => ['response_hours' => 8, 'resolution_hours' => 24],
                    'low' => ['response_hours' => 24, 'resolution_hours' => 72],
                ],
            ],
            [
                'name' => 'Infraestrutura / Equipamentos',
                'description' => 'Hardware, redes, impressoras, telefonia e equipamentos',
                'sla' => [
                    'critical' => ['response_hours' => 2, 'resolution_hours' => 8],
                    'high' => ['response_hours' => 4, 'resolution_hours' => 24],
                    'medium' => ['response_hours' => 8, 'resolution_hours' => 48],
                    'low' => ['response_hours' => 24, 'resolution_hours' => 120],
                ],
            ],
            [
                'name' => 'Sistema Centelha B2B',
                'description' => 'Plataforma B2B Centelha para vendas no atacado',
                'sla' => [
                    'critical' => ['response_hours' => 2, 'resolution_hours' => 6],
                    'high' => ['response_hours' => 4, 'resolution_hours' => 12],
                    'medium' => ['response_hours' => 8, 'resolution_hours' => 24],
                    'low' => ['response_hours' => 24, 'resolution_hours' => 72],
                ],
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['slug' => Str::slug($productData['name'])],
                [
                    'name' => $productData['name'],
                    'description' => $productData['description'],
                    'is_active' => true,
                    'sla_config' => json_encode($productData['sla']), // ✅ SLA incluído diretamente
                ]
            );
        }
    }
}