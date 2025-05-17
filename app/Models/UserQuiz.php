<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// app/Models/UserQuiz.php
class UserQuiz extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'quiz_id', 'started_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function userQuestions()
    {
        return $this->hasMany(UserQuestion::class);
    }
}
