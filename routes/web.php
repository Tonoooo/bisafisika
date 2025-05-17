<?php

use App\Http\Controllers\QuizController;
use App\Livewire\QuizList;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/quizzes', QuizList::class)->name('quiz.list');
    Route::get('/quiz/{quizId}/start', [QuizController::class, 'startQuiz'])->name('quiz.start');
    Route::get('/quiz/{userQuizId}/question/{questionIndex}', \App\Livewire\QuizQuestion::class)->name('quiz.question');
    Route::get('/quiz/results/{userQuizId}', \App\Livewire\QuizResult::class)->name('quiz.results');
    Route::get('/quiz/history', \App\Livewire\QuizHistory::class)->name('quiz.history');
});

Route::get('/', function () {
    return redirect('/admin');
});

// // Protect admin routes with custom middleware
// Route::middleware(['auth', 'checkadmin'])->prefix('admin')->group(function () {
//     // Filament's internal routes for admin panel, including restricted areas like Roles.
//     // Filament registers these automatically, so you don't need to add anything here manually.
// });
