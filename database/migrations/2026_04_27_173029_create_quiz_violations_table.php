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
        Schema::create('quiz_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_quiz_id')->constrained('user_quizzes')->onDelete('cascade');
            $table->string('violation_type'); // tab_switch, fullscreen_exit, window_blur
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_violations');
    }
};
