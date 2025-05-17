<?php

namespace App\Livewire;

// app/Http/Livewire/QuizResult.php

use App\Models\Leaderboard;
use App\Models\UserQuiz;
use Livewire\Component;

class QuizResult extends Component
{
    public $userQuizId;

    public $userQuiz;

    public $score;

    public function mount($userQuizId)
    {
        $this->userQuizId = $userQuizId;
        $this->userQuiz = UserQuiz::findOrFail($this->userQuizId);

        // Check if user has any questions to avoid division by zero
        $totalQuestions = $this->userQuiz->userQuestions->count();
        if ($totalQuestions === 0) {
            $this->score = 0;
        } else {
            $this->score = $this->userQuiz->userQuestions->sum(function ($userQuestion) {
                $userAnswers = $userQuestion->userAnswers;
                if ($userAnswers->isEmpty()) {
                    return 0;
                }

                return $userAnswers->where('is_correct', true)->count();
            });

            // Calculate the score as a percentage
            $this->score = ($this->score / $totalQuestions) * 100;
        }

        // Ensure the authenticated user is the owner of the quiz
        if ($this->userQuiz->user_id !== auth()->id()) {
            abort(403);
        }

        // Update leaderboard only if the new score is higher or if the leaderboard doesn't exist
        $leaderboard = Leaderboard::where('user_id', auth()->id())
            ->where('quiz_id', $this->userQuiz->quiz_id)
            ->first();

        if (! $leaderboard || $this->score > $leaderboard->score) {
            Leaderboard::updateOrCreate(
                ['user_id' => auth()->id(), 'quiz_id' => $this->userQuiz->quiz_id],
                ['score' => $this->score]
            );
        }
    }

    public function render()
    {
        return view('livewire.quiz-result', [
            'userQuiz' => $this->userQuiz,
            'score' => $this->score,
        ]);
    }
}
