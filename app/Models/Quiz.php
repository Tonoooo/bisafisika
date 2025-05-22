<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// app/Models/Quiz.php
class Quiz extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'start_date', 'close_date', 'time_limit', 'attempt_limit', 'bab_id'];

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'quiz_question');
    }

    public function bab()
    {
        return $this->belongsTo(Bab::class);
    }
}
