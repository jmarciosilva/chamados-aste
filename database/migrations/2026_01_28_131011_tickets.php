<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ------------------------------------------------------------------
     * TABELA: tickets
     * ------------------------------------------------------------------
     * Entidade central do sistema de chamados
     * Contém snapshot completo do SLA
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            // Código funcional (CHMMYY-000001)
            $table->string('code', 20)->unique();

            // Assunto resumido
            $table->string('subject');

            // Descrição rica (HTML, prints colados)
            $table->longText('description')->nullable();

            // Contexto do chamado
            $table->foreignId('product_id')->constrained();
            $table->foreignId('problem_category_id')->constrained();
            $table->string('service_type');

            // Status e prioridade
            $table->string('priority');
            $table->string('status')->default('open');

            /**
             * ----------------------------------------------------------
             * SLA (SNAPSHOT)
             * ----------------------------------------------------------
             */
            $table->foreignId('sla_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->unsignedInteger('sla_response_hours')->nullable();
            $table->unsignedInteger('sla_resolution_hours')->nullable();

            $table->timestamp('sla_started_at')->nullable();
            $table->timestamp('sla_paused_at')->nullable();
            $table->unsignedInteger('sla_paused_seconds')->default(0);
            $table->string('sla_status')->default('running');

            /**
             * ----------------------------------------------------------
             * RELACIONAMENTOS
             * ----------------------------------------------------------
             */
            $table->foreignId('requester_id')->constrained('users');
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('current_group_id')
                ->nullable()
                ->constrained('support_groups');

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users');

            // Fechamento
            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Rollback completo
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
