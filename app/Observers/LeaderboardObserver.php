<?php

namespace App\Observers;

use App\Models\Leaderboard;
use App\Models\StudentScore;

class LeaderboardObserver
{
    public function created(Leaderboard $leaderboard)
    {
        StudentScore::updateScore($leaderboard->user_id);
    }

    public function updated(Leaderboard $leaderboard)
    {
        StudentScore::updateScore($leaderboard->user_id);
    }

    public function deleted(Leaderboard $leaderboard)
    {
        StudentScore::updateScore($leaderboard->user_id);
    }
} 