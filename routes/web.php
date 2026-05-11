<?php

use App\Http\Controllers\QuizController;
use App\Http\Controllers\RegisterController;
use App\Livewire\QuizList;
use App\Livewire\RegisterUser;
use Illuminate\Support\Facades\Route;

// Rute publik untuk registrasi
//Route::get('/register', RegisterUser::class)->name('register');

Route::get('/teacher/waiting', function () {
    return view('livewire.waiting', [], ['layout' => 'layouts.guest']);
})->name('teacher.waiting')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/quizzes', QuizList::class)->name('quiz.list');
    Route::get('/quiz/{quizId}/prepare', \App\Livewire\QuizPreparation::class)->name('quiz.prepare');
    Route::get('/quiz/{quizId}/start', [QuizController::class, 'startQuiz'])->name('quiz.start');
    Route::get('/quiz/{userQuizId}/question/{questionIndex}', \App\Livewire\QuizQuestion::class)->name('quiz.question');
    Route::get('/quiz/results/{userQuizId}', \App\Livewire\QuizResult::class)->name('quiz.results');
    Route::get('/quiz/history', \App\Livewire\QuizHistory::class)->name('quiz.history');
    Route::get('/admin/student-quiz-history/{userId}', \App\Filament\Pages\StudentQuizHistory::class)
        ->name('filament.admin.pages.student-quiz-history');
    Route::get('/questions/preview', \App\Livewire\QuestionPreviewer::class)->name('questions.preview');
});

Route::get('/', function () {
    return view('landing');
});

Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::get('/register/student', [RegisterController::class, 'showStudentForm'])->name('register.student');
Route::get('/register/teacher', [RegisterController::class, 'showTeacherForm'])->name('register.teacher');
Route::get('/register/mahasiswa', [RegisterController::class, 'showMahasiswaForm'])->name('register.mahasiswa');
Route::get('/register/dosen', [RegisterController::class, 'showDosenForm'])->name('register.dosen');
Route::get('/register/lecturer', [RegisterController::class, 'showLecturerForm'])->name('register.lecturer');

// Rate limiting: max 5 registrasi per menit per IP
Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
});