<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class StudentScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_score',
        'total_quizzes',
        'average_score',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function updateScore($userId)
    {
        try {
            // Hitung total quiz yang sudah dikerjakan
            $totalQuizzes = UserQuiz::where('user_id', $userId)
                ->where('is_completed', true)
                ->count();

            Log::info('Updating StudentScore', [
                'user_id' => $userId,
                'total_quizzes' => $totalQuizzes,
                'query' => UserQuiz::where('user_id', $userId)
                    ->where('is_completed', true)
                    ->toSql()
            ]);

            // Hitung total score dari leaderboard
            $totalScore = Leaderboard::where('user_id', $userId)
                ->sum('score');

            // Hitung rata-rata score
            $averageScore = $totalQuizzes > 0 ? $totalScore / $totalQuizzes : 0;

            // Update atau buat record baru
            $studentScore = self::updateOrCreate(
                ['user_id' => $userId],
                [
                    'total_score' => $totalScore,
                    'total_quizzes' => $totalQuizzes,
                    'average_score' => $averageScore,
                ]
            );

            Log::info('StudentScore updated', [
                'user_id' => $userId,
                'student_score' => $studentScore->toArray()
            ]);

            return $studentScore;
        } catch (\Exception $e) {
            Log::error('Error updating StudentScore', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
} 