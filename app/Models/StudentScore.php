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
            $totalQuizzes = UserQuiz::where('user_id', $userId)
                ->where('is_completed', true)
                ->count();

            $scores = UserQuiz::where('user_id', $userId)
                ->where('is_completed', true)
                ->select('quiz_id', 'score', 'created_at')
                ->get();


            $totalScore = $scores->sum('score');


            $averageScore = $totalQuizzes > 0 ? $totalScore / $totalQuizzes : 0;

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
            
            throw $e;
        }
    }
} 