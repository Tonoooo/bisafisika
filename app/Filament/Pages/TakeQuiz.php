<?php

namespace App\Filament\Pages;

use App\Models\Bab;
use Filament\Pages\Page;

class TakeQuiz extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Take Quiz';
    protected static ?string $title = 'Pilih Bab';
    protected static ?string $slug = 'take-quiz';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.take-quiz';

    public $selectedBab = null;
    public $quizzes = [];

    public function mount()
    {
        $this->quizzes = collect();
    }

    public function selectBab($babId)
    {
        $this->selectedBab = Bab::find($babId);
        $this->quizzes = $this->selectedBab->quizzes()
            ->whereHas('questions')
            ->orderBy('title')
            ->get();
    }

    public function backToBabs()
    {
        $this->selectedBab = null;
        $this->quizzes = collect();
    }

    public function startQuiz($quizId)
    {
        $quiz = \App\Models\Quiz::find($quizId);
        $user = auth()->user();
        
        // Hitung berapa kali user sudah mengerjakan quiz ini
        $attemptCount = \App\Models\UserQuiz::where('user_id', $user->id)
            ->where('quiz_id', $quizId)
            ->count();
            
        if ($attemptCount >= $quiz->attempt_limit) {
            $this->js("alert('Anda telah mencapai batas maksimal pengerjaan quiz ini ({$quiz->attempt_limit} kali).')");
            return;
        }
        
        return redirect()->route('quiz.start', $quizId);
    }

    protected function getViewData(): array
    {
        return [
            'babs' => Bab::all(),
        ];
    }
} 