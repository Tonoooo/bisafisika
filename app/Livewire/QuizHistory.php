<?php

namespace App\Livewire;

use App\Models\UserQuiz;
use Livewire\Component;

class QuizHistory extends Component
{
    public function render()
    {
        $userQuizzes = UserQuiz::where('user_id', auth()->id())->with('quiz')->get();

        return view('livewire.quiz-history', compact('userQuizzes'));
    }
}
