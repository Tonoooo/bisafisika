<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// app/Models/UserQuestion.php
class UserQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['user_quiz_id', 'question_id', 'question_text', 'random_values','answers', 'image_path'];

    protected $casts = [
        'random_values' => 'array',
    ];

    public function userQuiz()
    {
        return $this->belongsTo(UserQuiz::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class);
    }
}
