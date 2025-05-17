<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Filament::serving(function () {
            $user = auth()->user();

            // Register navigation items available to all authenticated users

            Filament::registerNavigationItems([
                NavigationItem::make('Take Quiz')
                    ->icon('heroicon-o-play')
                    ->url(route('quiz.list'))
                    ->sort(2),

                NavigationItem::make('Quiz History')
                    ->icon('heroicon-o-play')
                    ->url(route('quiz.history'))
                    ->sort(3),
            ]);

        });
    }
}
