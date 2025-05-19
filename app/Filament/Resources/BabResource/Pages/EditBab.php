<?php

namespace App\Filament\Resources\BabResource\Pages;

use App\Filament\Resources\BabResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBab extends EditRecord
{
    protected static string $resource = BabResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
