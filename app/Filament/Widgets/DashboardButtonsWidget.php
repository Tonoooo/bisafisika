<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class DashboardButtonsWidget extends Widget
{
    protected static string $view = 'filament.widgets.dashboard-buttons-widget';

    // Atur lebar kolom widget menjadi satu baris penuh
    protected int | string | array $columnSpan = 'full';
}
