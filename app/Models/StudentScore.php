<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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

            // Ambil semua score untuk logging
            $scores = UserQuiz::where('user_id', $userId)
                ->where('is_completed', true)
                ->select('quiz_id', 'score', 'created_at')
                ->get();

            // Log semua score yang ada
            Log::info('Scores for user ' . $userId, [
                'scores' => $scores->toArray()
            ]);

            // Hitung total score dari semua quiz yang diselesaikan
            $totalScore = $scores->sum('score');

            // Log hasil perhitungan
            Log::info('Score calculation for user ' . $userId, [
                'total_quizzes' => $totalQuizzes,
                'total_score' => $totalScore,
                'scores_sum' => $scores->sum('score')
            ]);

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

            return $studentScore;
        } catch (\Exception $e) {
            Log::error('Error updating StudentScore', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
} 