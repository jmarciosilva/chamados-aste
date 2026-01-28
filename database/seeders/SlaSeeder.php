<?php

namespace Database\Seeders;

use App\Enums\Priority;
use App\Enums\ServiceType;
use App\Models\Product;
use App\Models\Sla;
use Illuminate\Database\Seeder;

class SlaSeeder extends Seeder
{
    /**
     * ------------------------------------------------------------------
     * SLA POR PRODUTO – MODELO ITIL DEFINITIVO
     * ------------------------------------------------------------------
     * Regra:
     * Produto + Tipo de Serviço + Prioridade
     * ------------------------------------------------------------------
     */
    public function run(): void
    {
        /**
         * --------------------------------------------------------------
         * SLA PADRÃO (FALLBACK)
         * --------------------------------------------------------------
         */
        $defaultSlas = [
            Priority::LOW->value      => [24, 72],
            Priority::MEDIUM->value   => [8, 24],
            Priority::HIGH->value     => [4, 12],
            Priority::CRITICAL->value => [2, 4],
        ];

        /**
         * --------------------------------------------------------------
         * SLAs ESPECÍFICOS POR PRODUTO
         * --------------------------------------------------------------
         */
        $customSlas = [
            'PDV' => [
                Priority::CRITICAL->value => [1, 2],
                Priority::HIGH->value     => [2, 6],
            ],
            'Centelha B2B' => [
                Priority::CRITICAL->value => [2, 6],
                Priority::HIGH->value     => [4, 12],
            ],
        ];

        /**
         * --------------------------------------------------------------
         * EXECUÇÃO
         * --------------------------------------------------------------
         */
        $products = Product::where('is_active', true)->get();

        foreach ($products as $product) {

            foreach (ServiceType::cases() as $serviceType) {

                foreach (Priority::cases() as $priority) {

                    $times = $customSlas[$product->name][$priority->value]
                        ?? $defaultSlas[$priority->value];

                    Sla::firstOrCreate(
                        [
                            'product_id'  => $product->id,
                            'service_type'=> $serviceType->value,
                            'priority'    => $priority->value,
                        ],
                        [
                            'response_time_hours'   => $times[0],
                            'resolution_time_hours' => $times[1],
                            'is_default'            => ! isset($customSlas[$product->name]),
                            'is_active'             => true,
                        ]
                    );
                }
            }
        }
    }
}
