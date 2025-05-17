<?php

namespace App\Livewire;

use App\Models\UserAnswer;
use App\Models\UserQuiz;
use Livewire\Component;

class Quiz extends Component
{
    public $userQuizId;

    public $questions;

    public $currentQuestionIndex = 0;

    public $answers = [];

    public function mount($userQuizId)
    {
        $this->userQuizId = $userQuizId;
        $this->loadQuestions();
    }

    public function loadQuestions()
    {
        $userQuiz = UserQuiz::findOrFail($this->userQuizId);
        $this->questions = $userQuiz->userQuestions()->with('question')->get();
    }

    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function submitQuiz()
    {
        foreach ($this->answers as $userQuestionId => $answerContent) {
            $userQuestion = $this->questions->find($userQuestionId);
            $isCorrect = in_array($answerContent, array_column($userQuestion->question->answers, 'content')) &&
                         array_column($userQuestion->question->answers, 'is_correct')[array_search($answerContent, array_column($userQuestion->question->answers, 'content'))];

            UserAnswer::create([
                'user_question_id' => $userQuestionId,
                'answer_content' => $answerContent,
                'is_correct' => $isCorrect,
            ]);
        }

        // Update the leaderboard logic here

        return redirect()->route('quiz.results', $this->userQuizId);
    }

    public function render()
    {
        return view('livewire.quiz');
    }
}
