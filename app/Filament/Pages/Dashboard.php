<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\EagleImageWidget;
use App\Filament\Widgets\DashboardButtonsWidget;

class Dashboard extends BaseDashboard
{
    // Mengatur file view Blade kustom untuk halaman dashboard ini
    // protected static string $view = 'filament.pages.custom-dashboard'; // Kita tidak lagi memerlukan view kustom jika kita mengontrol widget di sini

    // Anda bisa menambahkan atau mengesampingkan properti atau metode lain di sini jika diperlukan

    // Contoh: Mengatur judul halaman (opsional, jika ingin mengganti judul default)
    // protected static ?string $title = 'Dashboard Saya';

    // Mengeset jumlah kolom di halaman dashboard (coba 1 untuk satu kolom penuh)
    // protected static ?int $columns = 1; // Kembali ke default

    // Menentukan widget yang akan ditampilkan dan urutannya
    // protected function getWidgets(): array // Hapus atau komentari metode ini
    // {
    //     return [
    //         EagleImageWidget::class,
    //         DashboardButtonsWidget::class,
    //         TopStudentsLeaderboardWidget::class,
    //     ];
    // }

    // Jika Anda menggunakan header atau footer widgets, Anda bisa override metode berikut:
    protected function getHeaderWidgets(): array
    {
        return [
            // Daftar widget di sini
            // DashboardButtonsWidget::class,
            // TopStudentsLeaderboardWidget::class,
        ];
    }

    // protected function getFooterWidgets(): array
    // {
    //     return [
    //         // Daftar widget footer di sini
    //     ];
    // }
} 