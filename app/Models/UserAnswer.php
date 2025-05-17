<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// app/Models/UserAnswer.php
class UserAnswer extends Model
{
    use HasFactory;

    protected $fillable = ['user_question_id', 'answer_content', 'is_correct'];

    public function userQuestion()
    {
        return $this->belongsTo(UserQuestion::class);
    }
}
