<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ------------------------------------------------------------------
     * TABELA: products
     * ------------------------------------------------------------------
     * Representa sistemas / plataformas atendidas pelo suporte
     * Ex: PDV, SIGE, Centelha, OmniChannel, etc.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Nome exibido do produto
            $table->string('name');

            // Slug interno (URLs, regras, integrações)
            $table->string('slug')->unique();

            // Descrição administrativa
            $table->text('description')->nullable();

            // Produto disponível para abertura de chamados
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Rollback completo
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
