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

                // Update score in user_quizzes table
                $this->userQuiz->update([
                    'score' => $this->score,
                    'is_completed' => true
                ]);

                // Update student score
                StudentScore::updateScore($this->userQuiz->user_id);
            }

            // Cek akses berdasarkan role
            $user = auth()->user();
            $isOwner = $this->userQuiz->user_id === $user->id;
            $isAdmin = $user->roles->contains('name', 'super_admin');
            $isTeacher = $user->roles->contains('name', 'guru');

            // Jika bukan pemilik quiz, cek apakah user adalah admin atau guru
            if (!$isOwner) {
                if ($isAdmin) {
                    // Admin bisa melihat semua hasil
                    Log::info('Admin accessing quiz result', [
                        'admin_id' => $user->id,
                        'quiz_id' => $this->userQuiz->quiz_id,
                        'student_id' => $this->userQuiz->user_id
                    ]);
                } elseif ($isTeacher) {
                    // Guru hanya bisa melihat hasil siswa di sekolahnya
                    $studentSchoolId = $this->userQuiz->user->school_id;
                    if ($studentSchoolId !== $user->school_id) {
                        abort(403, 'Anda tidak memiliki akses untuk melihat hasil quiz ini.');
                    }
                    Log::info('Teacher accessing quiz result', [
                        'teacher_id' => $user->id,
                        'quiz_id' => $this->userQuiz->quiz_id,
                        'student_id' => $this->userQuiz->user_id
                    ]);
                } else {
                    abort(403, 'Anda tidak memiliki akses untuk melihat hasil quiz ini.');
                }
            }

            // Update leaderboard hanya jika user adalah pemilik quiz
            if ($isOwner) {
                $leaderboard = Leaderboard::updateOrCreate(
                    ['user_id' => auth()->id(), 'quiz_id' => $this->userQuiz->quiz_id],
                    ['score' => $this->score]
                );

                Log::info('Leaderboard updated', [
                    'user_id' => auth()->id(),
                    'quiz_id' => $this->userQuiz->quiz_id,
                    'score' => $this->score
                ]);

                // Update quiz status
                $this->userQuiz->update(['is_completed' => true]);
                
                Log::info('Quiz marked as completed', [
                    'user_quiz_id' => $this->userQuizId,
                    'user_id' => auth()->id()
                ]);

                // Hitung ulang total quiz dan score dari user_quizzes
                $totalQuizzes = UserQuiz::where('user_id', auth()->id())
                    ->where('is_completed', true)
                    ->count();

                // Hitung total score dari semua quiz yang sudah dikerjakan
                $totalScore = UserQuiz::where('user_id', auth()->id())
                    ->where('is_completed', true)
                    ->sum('score');

                $averageScore = $totalQuizzes > 0 ? $totalScore / $totalQuizzes : 0;

                Log::info('Calculated scores from user_quizzes', [
                    'user_id' => auth()->id(),
                    'total_quizzes' => $totalQuizzes,
                    'total_score' => $totalScore,
                    'average_score' => $averageScore,
                    'raw_scores' => UserQuiz::where('user_id', auth()->id())
                        ->where('is_completed', true)
                        ->select('quiz_id', 'score', 'created_at')
                        ->get()
                        ->toArray()
                ]);

                // Update StudentScore
                $studentScore = StudentScore::updateOrCreate(
                    ['user_id' => auth()->id()],
                    [
                        'total_score' => $totalScore,
                        'total_quizzes' => $totalQuizzes,
                        'average_score' => $averageScore,
                    ]
                );

                Log::info('StudentScore updated', [
                    'user_id' => auth()->id(),
                    'student_score' => $studentScore->toArray()
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in QuizResult mount', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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
