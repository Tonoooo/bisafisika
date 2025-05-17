<?php

namespace App\Livewire;

use App\Models\Quiz;
use Livewire\Component;

class QuizList extends Component
{
    public $quizzes;

    public function mount()
    {
        $this->quizzes = Quiz::where('start_date', '<=', now())
            ->where('close_date', '>=', now())
            ->get();
    }

    public function render()
    {
        return view('livewire.quiz-list', [
            'quizzes' => $this->quizzes,
        ]);
    }
}
