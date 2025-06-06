<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\Layout\View; // Import View

class TopStudentsLeaderboardWidget extends BaseWidget
{
    // Heading tidak lagi diperlukan di sini karena kita menaruhnya di file Blade
    protected static ?string $heading = 'Papan Peringkat Teratas';

    // Kita akan atur column span di halaman Dashboard, bukan di sini
    protected int | string | array $columnSpan = 'full';

    // Widget ini tidak lagi membutuhkan view kustom karena kita memisahkannya.
    // Jika kode di atas Anda simpan di file view widget ini, biarkan baris ini.
    // Jika Anda membuat widget baru, Anda bisa hapus baris ini.
    // protected static string $view = 'filament.widgets.top-students-leaderboard-widget';


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
            // Kolom Peringkat Baru: Menampilkan nomor urut sebagai peringkat
            TextColumn::make('rank')
                ->label('Peringkat')
                ->getStateUsing(static function (\stdClass $rowLoop): string {
                    $rank = $rowLoop->iteration;
                    // Menambahkan ikon untuk top 3
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
                ->badge() // Mengubah tampilan menjadi lencana
                ->color('warning') // Memberi warna pada lencana
                ->alignCenter(),
        ];
    }
    
    protected function isTablePaginationEnabled(): bool
    {
        return false; // Mematikan paginasi karena kita hanya menampilkan top 5
    }
}