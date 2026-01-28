<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ------------------------------------------------------------------
     * TABELA: support_groups
     * ------------------------------------------------------------------
     * Grupos ITIL responsáveis pelo atendimento
     * Ex: Service Desk, Suporte ERP, Suporte PDV
     */
    public function up(): void
    {
        Schema::create('support_groups', function (Blueprint $table) {
            $table->id();

            // Nome do grupo
            $table->string('name');

            // Código interno único (SERVICE_DESK, ERP, PDV)
            $table->string('code')->unique();

            // Grupo ativo/inativo
            $table->boolean('is_active')->default(true);

            // Indica se é o grupo de entrada padrão
            $table->boolean('is_entry_point')->default(false);

            // Descrição funcional
            $table->text('description')->nullable();

            // Usuário administrador que criou o grupo
            $table->foreignId('created_by')
                ->constrained('users');

            $table->timestamps();
        });

        /**
         * --------------------------------------------------------------
         * PIVOT: support_group_user
         * --------------------------------------------------------------
         * Define quais usuários pertencem a quais grupos
         */
        Schema::create('support_group_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('support_group_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Rollback completo (pivot primeiro)
     */
    public function down(): void
    {
        Schema::dropIfExists('support_group_user');
        Schema::dropIfExists('support_groups');
    }
};
