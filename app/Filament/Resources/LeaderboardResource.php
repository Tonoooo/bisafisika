<?php

// app/Filament/Resources/LeaderboardResource.php

namespace App\Filament\Resources;

use App\Exports\LeaderboardExport;
use App\Filament\Resources\LeaderboardResource\Pages;
use App\Models\Bab;
use App\Models\Quiz;
use App\Models\UserQuiz;
use App\Models\StudentScore;
use App\Models\School;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Model;


class LeaderboardResource extends Resource
{
    protected static ?string $model = Bab::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Leaderboard';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false; 
    }

    public static function canDelete(Model $record): bool
    {
        return false; 
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Bab')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quizzes_count')
                    ->label('Jumlah Level/Quiz')
                    ->counts('quizzes')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('view_quizzes')
                    ->label('Lihat Level')
                    ->icon('heroicon-o-list-bullet')
                    ->color('primary')
                    ->url(fn (Bab $record): string => static::getUrl('quizzes', ['babId' => $record])),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaderboards::route('/'),
            'quizzes' => Pages\ViewBabQuizzes::route('/{babId}/quizzes'),
            'scores' => Pages\ViewQuizLeaderboard::route('/{babId}/quizzes/{quizId}/scores'),
        ];
    }
}
