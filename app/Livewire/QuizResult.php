<?php

namespace App\Livewire;

// app/Http/Livewire/QuizResult.php

use App\Models\Leaderboard;
use App\Models\UserQuiz;
use App\Models\StudentScore;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class QuizResult extends Component
{
    public $userQuizId;

    public $userQuiz;

    public $score;

    public function mount($userQuizId)
    {
        try {
            DB::beginTransaction();
            
            $this->userQuizId = $userQuizId;
            $this->userQuiz = UserQuiz::findOrFail($this->userQuizId);

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

                $this->score = ($this->score / $totalQuestions) * 100;

                $this->userQuiz->update([
                    'score' => $this->score,
                    'is_completed' => true
                ]);

                StudentScore::updateScore($this->userQuiz->user_id);
            }

            $user = auth()->user();
            $isOwner = $this->userQuiz->user_id === $user->id;
            $isAdmin = $user->roles->contains('name', 'super_admin');
            $isTeacher = $user->roles->contains('name', 'guru');

            if (!$isOwner) {
                if ($isAdmin) {
                    //
                } elseif ($isTeacher) {
                    $studentSchoolId = $this->userQuiz->user->school_id;
                    if ($studentSchoolId !== $user->school_id) {
                        abort(403, 'Anda tidak memiliki akses untuk melihat hasil quiz ini.');
                    }
                } else {
                    abort(403, 'Anda tidak memiliki akses untuk melihat hasil quiz ini.');
                }
            }

            if ($isOwner) {
                $leaderboard = Leaderboard::updateOrCreate(
                    ['user_id' => auth()->id(), 'quiz_id' => $this->userQuiz->quiz_id],
                    ['score' => $this->score]
                );


                $this->userQuiz->update(['is_completed' => true]);
                

                $totalQuizzes = UserQuiz::where('user_id', auth()->id())
                    ->where('is_completed', true)
                    ->count();

                $totalScore = UserQuiz::where('user_id', auth()->id())
                    ->where('is_completed', true)
                    ->sum('score');

                $averageScore = $totalQuizzes > 0 ? $totalScore / $totalQuizzes : 0;

                

                $studentScore = StudentScore::updateOrCreate(
                    ['user_id' => auth()->id()],
                    [
                        'total_score' => $totalScore,
                        'total_quizzes' => $totalQuizzes,
                        'average_score' => $averageScore,
                    ]
                );

            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            
            throw $e;
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
