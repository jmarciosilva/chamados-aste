<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('product_impact_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('impact_question_id')
                ->constrained('product_impact_questions')
                ->cascadeOnDelete();

            $table->string('label');
            $table->enum('priority', ['low', 'medium', 'high', 'critical']);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_impact_answers');
    }
};
