<?php

namespace App\Filament\Pages;

use App\Models\UserQuiz;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class QuizHistory extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Riwayat Kuis';
    protected static ?string $title = 'Riwayat Kuis';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.quiz-history';

    public function getViewData(): array
    {
        return [
            'userQuizzes' => UserQuiz::where('user_id', Auth::id())
                ->with(['quiz.bab'])
                ->latest()
                ->get(),
        ];
    }
} 