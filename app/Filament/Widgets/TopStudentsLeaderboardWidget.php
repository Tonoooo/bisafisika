<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\Layout\View;

class TopStudentsLeaderboardWidget extends BaseWidget
{
    protected static ?string $heading = 'Papan Peringkat Teratas';

    protected int | string | array $columnSpan = 'full';



    protected function getTableQuery(): Builder
    {
        return User::query()
            ->select('users.id', 'users.name', 'schools.name as school_name', DB::raw('sum(user_quizzes.score) as total_score'))
            ->join('user_quizzes', 'users.id', '=', 'user_quizzes.user_id')
            ->join('schools', 'users.school_id', '=', 'schools.id')
            ->whereHas('roles', function ($query) {
                $query->where('name', 'siswa');
            })
            ->groupBy('users.id', 'users.name', 'schools.name')
            ->orderByDesc('total_score')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('rank')
                ->label('Peringkat')
                ->getStateUsing(static function (\stdClass $rowLoop): string {
                    $rank = $rowLoop->iteration;
                    return match ($rank) {
                        1 => '🥇 ' . $rank,
                        2 => '🥈 ' . $rank,
                        3 => '🥉 ' . $rank,
                        default => $rank,
                    };
                })->alignCenter(),

            TextColumn::make('name')
                ->label('Nama Siswa')
                ->searchable(),

            TextColumn::make('school_name')
                ->label('Sekolah')
                ->searchable(),
                
            TextColumn::make('total_score')
                ->label('Total Skor')
                ->sortable()
                ->badge()
                ->color('warning') 
                ->alignCenter(),
        ];
    }
    
    protected function isTablePaginationEnabled(): bool
    {
        return false; 
    }
}