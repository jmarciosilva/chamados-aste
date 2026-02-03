<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ============================================================
     * REFATORAÇÃO: SLAs AGORA SÃO PARTE DO PRODUTO
     * ============================================================
     * Cada produto terá 4 SLAs fixos (LOW, MEDIUM, HIGH, CRITICAL)
     * armazenados como colunas JSON no próprio produto
     */
    public function up(): void
    {
        /**
         * ----------------------------------------------------------
         * ADICIONA COLUNAS DE SLA AO PRODUTO
         * ----------------------------------------------------------
         */
        Schema::table('products', function (Blueprint $table) {
            // SLAs por prioridade (em horas)
            $table->json('sla_config')->nullable()->after('is_active');
            
            /*
            Estrutura JSON esperada:
            {
                "low": {
                    "response_hours": 24,
                    "resolution_hours": 72
                },
                "medium": {
                    "response_hours": 8,
                    "resolution_hours": 24
                },
                "high": {
                    "response_hours": 4,
                    "resolution_hours": 12
                },
                "critical": {
                    "response_hours": 2,
                    "resolution_hours": 4
                }
            }
            */
        });

        /**
         * ----------------------------------------------------------
         * MIGRA DADOS EXISTENTES DA TABELA slas PARA products
         * ----------------------------------------------------------
         */
        DB::transaction(function () {
            $products = DB::table('products')->get();

            foreach ($products as $product) {
                // Busca SLAs existentes para este produto
                $slas = DB::table('slas')
                    ->where('product_id', $product->id)
                    ->get()
                    ->keyBy('priority');

                // Monta configuração padrão
                $slaConfig = [
                    'low' => [
                        'response_hours' => $slas->get('low')->response_time_hours ?? 24,
                        'resolution_hours' => $slas->get('low')->resolution_time_hours ?? 72,
                    ],
                    'medium' => [
                        'response_hours' => $slas->get('medium')->response_time_hours ?? 8,
                        'resolution_hours' => $slas->get('medium')->resolution_time_hours ?? 24,
                    ],
                    'high' => [
                        'response_hours' => $slas->get('high')->response_time_hours ?? 4,
                        'resolution_hours' => $slas->get('high')->resolution_time_hours ?? 12,
                    ],
                    'critical' => [
                        'response_hours' => $slas->get('critical')->response_time_hours ?? 2,
                        'resolution_hours' => $slas->get('critical')->resolution_time_hours ?? 4,
                    ],
                ];

                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['sla_config' => json_encode($slaConfig)]);
            }
        });

        /**
         * ----------------------------------------------------------
         * REMOVE RELACIONAMENTO product_id DA TABELA slas
         * ----------------------------------------------------------
         * Mantemos a tabela slas por enquanto para referência
         * histórica, mas removemos a FK
         */
        Schema::table('slas', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });
    }

    /**
     * ----------------------------------------------------------
     * ROLLBACK
     * ----------------------------------------------------------
     */
    public function down(): void
    {
        // Restaura product_id em slas
        Schema::table('slas', function (Blueprint $table) {
            $table->foreignId('product_id')
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();
        });

        // Remove sla_config de products
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('sla_config');
        });
    }
};
