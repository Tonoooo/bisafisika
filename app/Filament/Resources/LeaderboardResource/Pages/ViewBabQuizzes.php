<?php

namespace App\Filament\Resources\LeaderboardResource\Pages;

use App\Filament\Resources\LeaderboardResource;
use App\Models\Bab;
use App\Models\Quiz;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ViewBabQuizzes extends Page
{
    protected static string $resource = LeaderboardResource::class;

    protected static string $view = 'filament.resources.leaderboard-resource.pages.view-bab-quizzes';

    public Bab $record;

    public function mount($babId): void
    {
        $this->record = Bab::findOrFail($babId);
    }

    public function getTitle(): string
    {
        return "Leaderboard: {$this->record->name}";
    }

    public function getQuizzes()
    {
        return Quiz::where('bab_id', $this->record->id)->get();
    }
}
