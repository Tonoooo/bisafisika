<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\DB;

// Widget gabungan yang akan menampilkan gambar, tombol, dan tabel
class DashboardButtonsWidget extends BaseWidget
{
    //Opsional: Tambahkan heading untuk widget secara keseluruhan
    protected static ?string $heading = '';

    // Tentukan view Blade kustom untuk widget ini
    protected static string $view = 'filament.widgets.dashboard-buttons-widget';

    // Atur lebar kolom widget menjadi satu baris penuh
    protected int | string | array $columnSpan = 'full';

    // Metode untuk mendefinisikan tabel leaderboard
    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->select('users.id', 'users.name', 'schools.name as school_name', DB::raw('sum(user_quizzes.score) as total_score'))
                    ->join('user_quizzes', 'users.id', '=', 'user_quizzes.user_id')
                    ->join('schools', 'users.school_id', '=', 'schools.id')
                    ->whereHas('roles', function ($query) {
                        $query->where('name', 'siswa');
                    })
                    ->groupBy('users.id', 'users.name', 'schools.name')
                    ->orderByDesc('total_score')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nama'),
                TextColumn::make('school_name')
                    ->label('Sekolah'),
                TextColumn::make('total_score')
                    ->label('TS')
                    ->sortable(),
            ])
            ->paginated(false);
    }
} 