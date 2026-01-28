<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ------------------------------------------------------------------
     * HISTÓRICO DE MOVIMENTAÇÃO ITIL
     * ------------------------------------------------------------------
     */
    public function up(): void
    {
        Schema::create('ticket_group_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('from_group_id')
                ->nullable()
                ->constrained('support_groups');

            $table->foreignId('to_group_id')
                ->constrained('support_groups');

            $table->foreignId('changed_by')
                ->constrained('users');

            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_group_histories');
    }
};
