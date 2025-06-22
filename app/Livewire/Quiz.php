<?php

namespace App\Livewire;

use App\Models\UserAnswer;
use App\Models\UserQuiz;
use App\Models\Leaderboard;
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
        try {
            
            $totalScore = 0;
            $totalQuestions = count($this->questions);
            
            $userQuiz = UserQuiz::findOrFail($this->userQuizId);
            
            foreach ($this->answers as $userQuestionId => $answerContent) {
                $userQuestion = $this->questions->find($userQuestionId);
                $isCorrect = in_array($answerContent, array_column($userQuestion->question->answers, 'content')) &&
                             array_column($userQuestion->question->answers, 'is_correct')[array_search($answerContent, array_column($userQuestion->question->answers, 'content'))];

                UserAnswer::create([
                    'user_question_id' => $userQuestionId,
                    'answer_content' => $answerContent,
                    'is_correct' => $isCorrect,
                ]);

                if ($isCorrect) {
                    $totalScore++;
                }
            }

            $finalScore = ($totalScore / $totalQuestions) * 100;

            $leaderboard = Leaderboard::create([
                'user_id' => auth()->id(),
                'quiz_id' => $userQuiz->quiz_id,
                'score' => $finalScore
            ]);
            

            return redirect()->route('quiz.results', $this->userQuizId);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.quiz');
    }
}
