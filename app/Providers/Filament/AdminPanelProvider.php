<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use App\Filament\Widgets\DashboardButtonsWidget;
use App\Filament\Pages\QuizHistory;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\CustomLogin::class)
            //->registration()
            ->passwordReset()
            ->profile()
            ->brandLogo(asset('images/banner_logo.png'))
            ->brandLogoHeight('60px')
            ->brandName('BelajarFisika')
            ->favicon(asset('images/favicon_io/favicon.ico'))
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                QuizHistory::class,
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ])
            //->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                DashboardButtonsWidget::class,
            ])
            
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\CheckUserStatus::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                $user = auth()->user();

                $items = [];

                if (!$user) {
                    return $builder->items([]);
                }

                $items = [
                    NavigationItem::make('Dashboard')
                        ->icon('heroicon-o-home')
                        ->url(fn (): string => \Filament\Pages\Dashboard::getUrl())
                        ->visible(fn () => $user->roles->contains('name', 'super_admin') || 
                                         $user->roles->contains('name', 'guru') || 
                                         $user->roles->contains('name', 'siswa')),
                ];

                if ($user->roles->contains('name', 'super_admin')) {
                    $items = array_merge($items, [
                        NavigationItem::make('Questions')
                            ->icon('heroicon-o-question-mark-circle')
                            ->url(fn (): string => \App\Filament\Resources\QuestionResource::getUrl()),
                        NavigationItem::make('Quiz')
                            ->icon('heroicon-o-academic-cap')
                            ->url(fn (): string => \App\Filament\Resources\QuizResource::getUrl()),
                        NavigationItem::make('Bab')
                            ->icon('heroicon-o-book-open')
                            ->url(fn (): string => \App\Filament\Resources\BabResource::getUrl()),
                        NavigationItem::make('Take Quiz')
                            ->icon('heroicon-o-academic-cap')
                            ->url(fn (): string => \App\Filament\Pages\TakeQuiz::getUrl()),
                        NavigationItem::make('Leaderboard')
                            ->icon('heroicon-o-trophy')
                            ->url(fn (): string => \App\Filament\Resources\LeaderboardResource::getUrl()),
                        NavigationItem::make('Pengguna')
                            ->icon('heroicon-o-users')
                            ->url(fn (): string => \App\Filament\Resources\UserResource::getUrl()),
                        NavigationItem::make('Sekolah')
                            ->icon('heroicon-o-building-office')
                            ->url(fn (): string => \App\Filament\Resources\SchoolResource::getUrl()),
                        NavigationItem::make('Riwayat Quiz')
                            ->icon('heroicon-o-clock')
                            ->url(fn (): string => \App\Filament\Pages\QuizHistory::getUrl()),
                        NavigationItem::make('Roles & Permissions')
                            ->icon('heroicon-o-shield-check')
                            ->url('/admin/shield/roles'),
                    ]);
                } elseif ($user->roles->contains('name', 'guru')) {
                    $items = array_merge($items, [
                        NavigationItem::make('Leaderboard')
                            ->icon('heroicon-o-trophy')
                            ->url(fn (): string => \App\Filament\Resources\LeaderboardResource::getUrl()),
                        NavigationItem::make('Take Quiz')
                            ->icon('heroicon-o-academic-cap')
                            ->url(fn (): string => \App\Filament\Pages\TakeQuiz::getUrl()),
                        NavigationItem::make('Riwayat Quiz')
                            ->icon('heroicon-o-clock')
                            ->url(fn (): string => \App\Filament\Pages\QuizHistory::getUrl()),
                    ]);
                } else {
                    $items = array_merge($items, [
                        NavigationItem::make('Take Quiz')
                            ->icon('heroicon-o-academic-cap')
                            ->url(fn (): string => \App\Filament\Pages\TakeQuiz::getUrl()),
                        NavigationItem::make('Riwayat Quiz')
                            ->icon('heroicon-o-clock')
                            ->url(fn (): string => \App\Filament\Pages\QuizHistory::getUrl()),
                        ]);
                }

                return $builder->items($items);
            })
            ->darkMode(false);
    }
}