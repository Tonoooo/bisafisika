<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;

class CustomPasswordResetRequest extends BaseRequestPasswordReset
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->autocomplete(),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getRequestPasswordResetFormAction(),
            Action::make('back-to-login')
                ->label('Kembali ke Login')
                ->url(route('filament.admin.auth.login'))
                ->color('gray'),
        ];
    }

    protected function getRequestPasswordResetFormAction(): Action
    {
        return Action::make('request-password-reset')
            ->label('Kirim Link Reset Password')
            ->submit('requestPasswordReset');
    }
} 