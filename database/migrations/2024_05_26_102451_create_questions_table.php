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
        // 2023_05_26_000001_create_questions_table.php
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->text('rumus');
            $table->text('random_ranges');
            $table->string('image_path')->nullable();
            $table->json('answers'); // array of answers, one of them marked as correct
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
