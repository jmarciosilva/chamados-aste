<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ------------------------------------------------------------------
     * TABELA: slas
     * ------------------------------------------------------------------
     * Regra de SLA baseada em:
     * Produto + Tipo de Serviço + Prioridade
     */
    public function up(): void
    {
        Schema::create('slas', function (Blueprint $table) {
            $table->id();

            // Produto atendido
            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            // Tipo de serviço ITIL
            $table->string('service_type');

            // Prioridade (enum Priority)
            $table->string('priority');

            // Tempo máximo para primeira resposta
            $table->unsignedInteger('response_time_hours');

            // Tempo máximo para resolução
            $table->unsignedInteger('resolution_time_hours');

            // SLA padrão (fallback do produto)
            $table->boolean('is_default')->default(false);

            // Regra ativa/inativa
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Garante unicidade da regra
            $table->unique(
                ['product_id', 'service_type', 'priority'],
                'slas_unique_rule'
            );
        });
    }

    /**
     * Rollback completo
     */
    public function down(): void
    {
        Schema::dropIfExists('slas');
    }
};
