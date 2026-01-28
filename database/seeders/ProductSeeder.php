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
     * Cada produto representa um sistema, plataforma ou serviço atendido.
     * Produtos são usados por:
     * - Categorias de Problema
     * - SLAs
     * - Tickets
     */
    public function run(): void
    {
        $products = [
            'Acesso / Login',
            'SIGE',
            'WEBB LOJA',
            'PDV',
            'OmniChannel',
            'E-commerce',
            'Vejo Varejo',
            'Infraestrutura / Equipamentos',
            'Centelha B2B',
        ];

        foreach ($products as $name) {
            Product::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => $name,
                    'is_active' => true,
                ]
            );

        }
    }
}
