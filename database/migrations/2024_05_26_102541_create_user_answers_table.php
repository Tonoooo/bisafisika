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
        // 2023_05_26_000005_create_user_answers_table.php
        Schema::create('user_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_question_id')->constrained()->onDelete('cascade');
            $table->text('answer_content');
            $table->boolean('is_correct');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_answers');
    }
};
