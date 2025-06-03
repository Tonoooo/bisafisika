<?php

// app/Filament/Resources/LeaderboardResource.php

namespace App\Filament\Resources;

use App\Exports\LeaderboardExport;
use App\Filament\Resources\LeaderboardResource\Pages;
use App\Models\Leaderboard;
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
// Impor Model
use Illuminate\Database\Eloquent\Model; // Import class Model


class LeaderboardResource extends Resource
{
    protected static ?string $model = StudentScore::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Total Leaderboard';

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

     // Ubah tipe hint parameter $record di sini
     public static function canEdit(Model $record): bool
    {
        return false; // Tidak mengizinkan edit entri Leaderboard
    }

    // Ubah tipe hint parameter $record di sini
    public static function canDelete(Model $record): bool
    {
        return false; // Tidak mengizinkan hapus entri Leaderboard
    }

    // Anda mungkin juga perlu mengubah ini jika diaktifkan dan error serupa muncul
    // public static function canForceDelete(Model $record): bool
    // {
    //     return false;
    // }

    // public static function canRestore(Model $record): bool
    // {
    //     return false;
    // }

    // public static function canReplicate(Model $record): bool
    // {
    //     return false;
    // }


    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($livewire) {
                        $query = $livewire->getFilteredTableQuery();
                        return Excel::download(new LeaderboardExport($query), 'leaderboard-' . now()->format('Y-m-d') . '.xlsx');
                    })
            ])
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.school.name')
                    ->label('Sekolah')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.level')
                    ->label('Tingkat')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.class')
                    ->label('Kelas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_score')
                    ->label('Total Nilai')
                    ->sortable()
                    ->numeric(decimalPlaces: 2),
                Tables\Columns\TextColumn::make('total_quizzes')
                    ->label('Total Quiz')
                    ->sortable(),
                Tables\Columns\TextColumn::make('average_score')
                    ->label('Rata-rata Nilai')
                    ->sortable()
                    ->numeric(decimalPlaces: 2),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('school')
                    ->label('Sekolah')
                    ->relationship('user.school', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn () => auth()->user()->roles->contains('name', 'super_admin')),

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
                    ->visible(fn () => auth()->user()->roles->contains('name', 'super_admin') || auth()->user()->roles->contains('name', 'guru')),

                Tables\Filters\SelectFilter::make('class')
                    ->label('Kelas')
                    ->options([
                        'A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E', 'F' => 'F', 'G' => 'G', 'H' => 'H',
                        'I' => 'I', 'J' => 'J', 'K' => 'K', 'L' => 'L', 'M' => 'M', 'N' => 'N', 'O' => 'O', 'P' => 'P',
                        'Q' => 'Q', 'R' => 'R', 'S' => 'S', 'T' => 'T', 'U' => 'U', 'V' => 'V', 'W' => 'W', 'X' => 'X',
                        'Y' => 'Y', 'Z' => 'Z',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->whereHas('user', function ($query) use ($value) {
                                $query->where('class', $value);
                            })
                        );
                    })
                    ->visible(fn () => auth()->user()->roles->contains('name', 'super_admin') || auth()->user()->roles->contains('name', 'guru')),
            ])
            ->defaultSort('total_score', 'desc')
            ->recordUrl(fn (StudentScore $record): string => route('filament.admin.pages.student-quiz-history', ['userId' => $record->user_id]));
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // Filter hanya user dengan role siswa
        $query->whereHas('user', function ($query) {
            $query->whereHas('roles', function ($query) {
                $query->where('name', 'siswa');
            });
        });

        if ($user->roles->contains('name', 'guru')) {
            $query->whereHas('user', function ($query) use ($user) {
                $query->where('school_id', $user->school_id);
            });
        } elseif ($user->roles->contains('name', 'siswa')) {
            $query->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere(function($q) use ($user) {
                      $q->whereHas('user', function($q) use ($user) {
                          $q->where('school_id', $user->school_id)
                            ->where('level', $user->level)
                            ->where('class', $user->class);
                      });
                  });
            });
        }

        return $query;
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
