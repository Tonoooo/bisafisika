<?php

namespace App\Livewire;

use App\Models\Quiz;
use App\Models\UserQuiz;
use Livewire\Component;

class QuizPreparation extends Component
{
    public $quiz;
    public $quizId;
    public $remainingAttempts;

    public function mount($quizId)
    {
        $this->quizId = $quizId;
        $this->quiz = Quiz::with('bab')->findOrFail($quizId);

        // Hitung sisa attempt
        $userAttempts = UserQuiz::where('user_id', auth()->id())
            ->where('quiz_id', $quizId)
            ->count();
        $this->remainingAttempts = max(0, $this->quiz->attempt_limit - $userAttempts);
    }

    public function startQuiz()
    {
        // Redirect ke controller yang sudah ada untuk generate soal
        return redirect()->route('quiz.start', $this->quizId);
    }

    public function render()
    {
        return view('livewire.quiz-preparation');
    }
}
