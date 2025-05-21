<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_score',
        'total_quizzes',
        'average_score'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Method untuk update score
    public static function updateScore($userId)
    {
        $leaderboard = Leaderboard::where('user_id', $userId)->get();
        
        $totalScore = $leaderboard->sum('score');
        $totalQuizzes = $leaderboard->count();
        $averageScore = $totalQuizzes > 0 ? $totalScore / $totalQuizzes : 0;

        self::updateOrCreate(
            ['user_id' => $userId],
            [
                'total_score' => $totalScore,
                'total_quizzes' => $totalQuizzes,
                'average_score' => $averageScore
            ]
        );
    }
} 