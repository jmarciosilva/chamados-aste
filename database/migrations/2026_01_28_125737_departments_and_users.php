<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ------------------------------------------------------------------
     * UP
     * ------------------------------------------------------------------
     * Consolida:
     * - Criação da tabela departments
     * - Expansão da tabela users com campos corporativos e ITIL
     *
     * IMPORTANTE:
     * - Esta migration deve rodar APÓS a migration base de users
     *   (0001_01_01_000000_create_users_table.php)
     */
    public function up(): void
    {
        /**
         * --------------------------------------------------------------
         * TABELA: departments
         * --------------------------------------------------------------
         * Representa áreas organizacionais da empresa
         * Ex: TI, Financeiro, Comercial, Operações
         */
        Schema::create('departments', function (Blueprint $table) {
            $table->id();

            // Nome do departamento
            $table->string('name');

            // Departamento ativo/inativo
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

        /**
         * --------------------------------------------------------------
         * ALTERAÇÃO: users
         * --------------------------------------------------------------
         * Adiciona contexto corporativo e perfil ITIL
         */
        Schema::table('users', function (Blueprint $table) {

            // Departamento ao qual o usuário pertence
            $table->foreignId('department_id')
                ->nullable()
                ->constrained()
                ->after('id');

            /**
             * Perfil de acesso ao sistema
             * user  → solicitante
             * agent → atendente
             * admin → administrador
             */
            $table->enum('role', ['user', 'agent', 'admin'])
                ->default('user')
                ->after('department_id');

            /**
             * Tipo do agente (quando role = agent)
             * operator   → nível 1 / service desk
             * specialist → nível 2 / especialista
             */
            $table->enum('agent_type', ['operator', 'specialist'])
                ->default('specialist')
                ->after('role');

            // Cargo ou função do colaborador
            $table->string('job_title')
                ->nullable()
                ->after('agent_type');

            // Telefone corporativo
            $table->string('phone')
                ->nullable()
                ->after('job_title');

            // Usuário ativo/inativo no sistema
            $table->boolean('is_active')
                ->default(true)
                ->after('phone');
        });
    }

    /**
     * ------------------------------------------------------------------
     * DOWN
     * ------------------------------------------------------------------
     * Reverte tudo de forma segura:
     * - Remove campos adicionados em users
     * - Remove a tabela departments
     *
     * OBS:
     * - A ordem importa (FKs primeiro)
     */
    public function down(): void
    {
        /**
         * --------------------------------------------------------------
         * REMOVE CAMPOS DE USERS
         * --------------------------------------------------------------
         */
        Schema::table('users', function (Blueprint $table) {

            // Remove FK primeiro
            $table->dropForeign(['department_id']);

            // Remove colunas adicionadas
            $table->dropColumn([
                'department_id',
                'role',
                'agent_type',
                'job_title',
                'phone',
                'is_active',
            ]);
        });

        /**
         * --------------------------------------------------------------
         * DROP DA TABELA DEPARTMENTS
         * --------------------------------------------------------------
         */
        Schema::dropIfExists('departments');
    }
};
