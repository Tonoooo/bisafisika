<?php

namespace App\Filament\Resources\LeaderboardResource\Pages;

use App\Filament\Resources\LeaderboardResource;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use App\Models\Bab;

class ListLeaderboards extends Page
{
    protected static string $resource = LeaderboardResource::class;

    protected static string $view = 'filament.resources.leaderboard-resource.pages.list-leaderboards';

    public function getBabs()
    {
        return Bab::all();
    }
}
