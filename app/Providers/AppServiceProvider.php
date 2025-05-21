<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Leaderboard;
use App\Observers\LeaderboardObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Leaderboard::observe(LeaderboardObserver::class);
    }
}
