<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizViolation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_quiz_id',
        'violation_type',
    ];

    public function userQuiz()
    {
        return $this->belongsTo(UserQuiz::class);
    }
}
