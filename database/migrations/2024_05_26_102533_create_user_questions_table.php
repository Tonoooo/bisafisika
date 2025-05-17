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
        // 2023_05_26_000004_create_user_questions_table.php
        Schema::create('user_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_quiz_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->text('question_text'); // stores the question with generated numbers
            $table->json('random_values'); // stores the generated random values
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_questions');
    }
};
