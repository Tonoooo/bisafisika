<?php

namespace App\Observers;

use App\Models\Leaderboard;
use App\Models\StudentScore;

class LeaderboardObserver
{
    /**
     * Handle the Leaderboard "created" event.
     */
    public function created(Leaderboard $leaderboard): void
    {
        StudentScore::updateScore($leaderboard->user_id);
    }

    /**
     * Handle the Leaderboard "updated" event.
     */
    public function updated(Leaderboard $leaderboard): void
    {
        StudentScore::updateScore($leaderboard->user_id);
    }

    /**
     * Handle the Leaderboard "deleted" event.
     */
    public function deleted(Leaderboard $leaderboard): void
    {
        StudentScore::updateScore($leaderboard->user_id);
    }
} 