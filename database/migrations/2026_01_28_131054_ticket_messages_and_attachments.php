<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * --------------------------------------------------------------
         * MENSAGENS DO TICKET
         * --------------------------------------------------------------
         */
        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')->constrained();

            $table->text('message');

            // Nota interna (visível apenas para agentes)
            $table->boolean('is_internal_note')->default(false);

            $table->timestamps();
        });

        /**
         * --------------------------------------------------------------
         * ANEXOS DO TICKET
         * --------------------------------------------------------------
         */
        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('original_name');
            $table->string('file_path');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size');

            $table->foreignId('uploaded_by')->constrained('users');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_attachments');
        Schema::dropIfExists('ticket_messages');
    }
};
