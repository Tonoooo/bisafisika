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
            ->where('start_date', '<=', now())
            ->where('close_date', '>=', now())
            ->whereNotNull('close_date')
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
        
        // Cek apakah quiz masih aktif (belum melewati close_date)
        if (!$quiz || $quiz->close_date < now()) {
            $this->js("alert('Quiz ini sudah ditutup dan tidak dapat dikerjakan.')");
            return;
        }

        // Cek apakah quiz sudah dibuka (sudah melewati start_date)
        if ($quiz->start_date > now()) {
            $this->js("alert('Quiz ini belum dibuka.')");
            return;
        }
        
        // Hitung berapa kali user sudah mengerjakan quiz ini
        $attemptCount = \App\Models\UserQuiz::where('user_id', $user->id)
            ->where('quiz_id', $quizId)
            ->count();
            
        if ($attemptCount >= $quiz->attempt_limit) {
            $this->js("alert('Anda telah mencapai batas maksimal pengerjaan quiz ini ({$quiz->attempt_limit} kali).')");
            return;
        }
        
        return redirect()->route('quiz.prepare', $quizId);
    }

    protected function getViewData(): array
    {
        return [
            'babs' => Bab::all(),
        ];
    }
} 