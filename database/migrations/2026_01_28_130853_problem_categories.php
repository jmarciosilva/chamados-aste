<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ------------------------------------------------------------------
     * TABELA: problem_categories
     * ------------------------------------------------------------------
     * Classificação ITIL do problema
     * Sempre vinculada a UM produto
     */
    public function up(): void
    {
        Schema::create('problem_categories', function (Blueprint $table) {
            $table->id();

            // Produto ao qual a categoria pertence
            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            // Nome da categoria (ex: Problemas de Login)
            $table->string('name');

            // Slug único (URLs, seeds)
            $table->string('slug')->unique();

            // Descrição funcional
            $table->text('description')->nullable();

            // Tipo de serviço ITIL
            // incident | service_request | improvement | purchase
            $table->string('service_type');

            // Prioridade padrão ao abrir chamado
            $table->string('default_priority')->default('low');

            // Categoria ativa/inativa
            $table->boolean('is_active')->default(true);

            // Ordenação para UI
            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Rollback completo
     */
    public function down(): void
    {
        Schema::dropIfExists('problem_categories');
    }
};
