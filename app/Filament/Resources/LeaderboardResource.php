<?php

// app/Filament/Resources/LeaderboardResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaderboardResource\Pages;
use App\Models\Leaderboard;
use App\Models\School;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
// Impor Model
use Illuminate\Database\Eloquent\Model; // Import class Model


class LeaderboardResource extends Resource
{
    protected static ?string $model = Leaderboard::class;

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
                Tables\Columns\TextColumn::make('total_score')
                    ->label('Total Nilai')
                    ->sortable('total_score')
                    ->numeric(decimalPlaces: 2),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('school')
                    ->label('Sekolah')
                    ->relationship('user.school', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn () => auth()->user()->hasRole('super_admin')),

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
                    ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('guru')),

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
                    ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('guru')),
            ])
            ->defaultSort('total_score', 'desc')
            ->searchable(false);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // Buat subquery untuk mendapatkan total score dan row number
        $subquery = DB::table('leaderboards')
            ->select([
                'leaderboards.id',
                'leaderboards.user_id',
                DB::raw('SUM(leaderboards.score) OVER (PARTITION BY leaderboards.user_id) as total_score'),
                DB::raw('ROW_NUMBER() OVER (PARTITION BY leaderboards.user_id ORDER BY leaderboards.id) as rn')
            ]);

        // Query utama yang mengambil hanya baris pertama untuk setiap user
        $query->fromSub($subquery, 'ranked_leaderboards')
            ->select([
                'ranked_leaderboards.id',
                'ranked_leaderboards.user_id',
                'ranked_leaderboards.total_score'
            ])
            ->where('ranked_leaderboards.rn', 1)
            ->join('users', 'ranked_leaderboards.user_id', '=', 'users.id')
            ->with(['user' => function($query) {
                $query->with('school');
            }]);

        // Filter berdasarkan role
        if ($user->hasRole('guru')) {
            $query->where('users.school_id', $user->school_id);
        } elseif ($user->hasRole('siswa')) {
            // Modifikasi query untuk siswa
            $query->where(function($q) use ($user) {
                $q->where('users.id', $user->id)
                  ->orWhere(function($q) use ($user) {
                      $q->where('users.school_id', $user->school_id)
                        ->where('users.level', $user->level)
                        ->where('users.class', $user->class);
                  });
            });
        }

        return $query->orderByDesc('ranked_leaderboards.total_score');
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
