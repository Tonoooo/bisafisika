<?php

// app/Filament/Resources/LeaderboardResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaderboardResource\Pages;
use App\Models\Leaderboard;
use App\Models\School;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeaderboardResource extends Resource
{
    protected static ?string $model = Leaderboard::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Define the form schema if you want to allow editing leaderboard entries
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Siswa')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.school.name')
                    ->label('Sekolah')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.level')
                    ->label('Tingkat')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.class')
                    ->label('Kelas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quiz.title')
                    ->label('Judul Quiz')
                    ->sortable(),
                Tables\Columns\TextColumn::make('score')
                    ->label('Nilai')
                    ->sortable(),
            ])
            ->filters([
                // Filter berdasarkan Sekolah
                Tables\Filters\SelectFilter::make('school')
                    ->label('Sekolah')
                    ->relationship('user.school', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn () => auth()->user()->hasRole('super_admin')),

                // Filter berdasarkan Tingkat
                Tables\Filters\SelectFilter::make('level')
                    ->label('Tingkat')
                    ->options([
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->whereHas('user', function ($query) use ($value) {
                                $query->where('level', $value);
                            })
                        );
                    })
                    ->visible(fn () => auth()->user()->hasRole('super_admin')),

                // Filter berdasarkan Kelas
                Tables\Filters\SelectFilter::make('class')
                    ->label('Kelas')
                    ->options([
                        'A' => 'A',
                        'B' => 'B',
                        'C' => 'C',
                        'D' => 'D',
                        'E' => 'E',
                        'F' => 'F',
                        'G' => 'G',
                        'H' => 'H',
                        'I' => 'I',
                        'J' => 'J',
                        'K' => 'K',
                        'L' => 'L',
                        'M' => 'M',
                        'N' => 'N',
                        'O' => 'O',
                        'P' => 'P',
                        'Q' => 'Q',
                        'R' => 'R',
                        'S' => 'S',
                        'T' => 'T',
                        'U' => 'U',
                        'V' => 'V',
                        'W' => 'W',
                        'X' => 'X',
                        'Y' => 'Y',
                        'Z' => 'Z',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->whereHas('user', function ($query) use ($value) {
                                $query->where('class', $value);
                            })
                        );
                    })
                    ->visible(fn () => auth()->user()->hasRole('super_admin')),
            ])
            ->defaultSort('score', 'desc'); // Mengurutkan berdasarkan nilai tertinggi
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            // Super admin bisa lihat semua data
            return $query;
        } elseif ($user->hasRole('guru')) {
            // Guru hanya bisa lihat data dari sekolahnya
            return $query->whereHas('user', function ($query) use ($user) {
                $query->where('school_id', $user->school_id);
            });
        } else {
            // Siswa hanya bisa lihat data dari sekolah, tingkat, dan kelasnya
            return $query->whereHas('user', function ($query) use ($user) {
                $query->where('school_id', $user->school_id)
                    ->where('level', $user->level)
                    ->where('class', $user->class);
            });
        }
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
        ];
    }
}
