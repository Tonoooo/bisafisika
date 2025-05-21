<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\UserQuiz;
use App\Models\Leaderboard;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class StudentQuizHistory extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Riwayat Quiz Siswa';
    protected static ?string $title = 'Riwayat Quiz Siswa';
    protected static ?string $slug = 'student-quiz-history';
    protected static bool $shouldRegisterNavigation = false;

    public $userId;
    public $user;

    public function mount($userId = null)
    {
        if (!$userId) {
            abort(404);
        }

        $this->userId = $userId;
        $this->user = User::findOrFail($userId);
    }

    protected static string $view = 'filament.pages.student-quiz-history';

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $isAdmin = $user->roles->contains('name', 'super_admin');
        $isTeacher = $user->roles->contains('name', 'guru');

        return $table
            ->query(
                UserQuiz::query()
                    ->select([
                        'user_quizzes.*',
                        'leaderboards.score as quiz_score'
                    ])
                    ->leftJoin('leaderboards', function($join) {
                        $join->on('user_quizzes.quiz_id', '=', 'leaderboards.quiz_id')
                            ->where('leaderboards.user_id', '=', $this->userId);
                    })
                    ->where('user_quizzes.user_id', $this->userId)
                    ->where('user_quizzes.is_completed', true)
                    ->with('quiz')
            )
            ->columns([
                TextColumn::make('quiz.title')
                    ->label('Judul Quiz')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('started_at')
                    ->label('Waktu Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Waktu Selesai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('quiz_score')
                    ->label('Nilai')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->state(function (UserQuiz $record): ?float {
                        return $record->quiz_score;
                    }),
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('view_result')
                    ->label('Lihat Hasil')
                    ->url(fn (UserQuiz $record): string => route('quiz.results', ['userQuizId' => $record->id]))
                    ->openUrlInNewTab()
                    ->visible(fn () => $isAdmin || $isTeacher)
            ])
            ->defaultSort('created_at', 'desc');
    }
} 