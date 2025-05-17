<?php

namespace App\Livewire;

// app/Http/Livewire/QuizQuestion.php

use App\Models\UserAnswer;
use App\Models\UserQuiz;
use Livewire\Component;

class QuizQuestion extends Component
{
    public $userQuizId;

    public $questionIndex;

    public $question;

    public $totalQuestions;

    public $answer;

    public function mount($userQuizId, $questionIndex)
    {
        $this->userQuizId = $userQuizId;
        $this->questionIndex = $questionIndex;
        $userQuiz = UserQuiz::findOrFail($this->userQuizId);
        $this->question = $userQuiz->userQuestions[$this->questionIndex];
        $this->totalQuestions = $userQuiz->userQuestions->count();
        // Shuffle the answers
        $answers = $this->question->question->answers;
        shuffle($answers);
        $this->question->question->shuffled_answers = collect($answers);
        if ($userQuiz->is_completed) {
            return redirect()->route('quiz.results', $this->userQuizId);
        }
        if ($userQuiz->user_id !== auth()->id()) {
            abort(403);
        }
    }

    public function submitAnswer()
    {
        // Decode the JSON string to an array
        $answers = json_decode($this->question->answers, true);

        // Ensure $answers is an array before proceeding
        if (! is_array($answers)) {
            // Handle the error or set $answers to an empty array
            $answers = [];
            Log::error('Failed to decode JSON answers in question ID: '.$this->question->id);
        }

        $selectedAnswer = $this->answer;
        $isCorrect = false;

        foreach ($answers as $answer) {
            if (trim($answer['content']) === trim($selectedAnswer)) {
                $isCorrect = $answer['is_correct'];
                break;
            }
        }

        UserAnswer::updateOrCreate(
            ['user_question_id' => $this->question->id],
            ['answer_content' => $this->answer, 'is_correct' => $isCorrect]
        );

        if ($this->questionIndex + 1 < $this->totalQuestions) {
            return redirect()->route('quiz.question', ['userQuizId' => $this->userQuizId, 'questionIndex' => $this->questionIndex + 1]);
        } else {
            $this->question->userQuiz->update(['is_completed' => true]);

            return redirect()->route('quiz.results', $this->userQuizId);
        }
    }

    public function render()
    {
        return view('livewire.quiz-question', [
            'question' => $this->question,
            'questionIndex' => $this->questionIndex,
            'totalQuestions' => $this->totalQuestions,
        ]);
    }
}
