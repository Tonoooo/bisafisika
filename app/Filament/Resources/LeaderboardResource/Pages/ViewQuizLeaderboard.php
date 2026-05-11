<?php

namespace App\Filament\Resources\LeaderboardResource\Pages;

use App\Filament\Resources\LeaderboardResource;
use App\Models\Bab;
use App\Models\Quiz;
use App\Models\UserQuiz;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Exports\QuizLeaderboardExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Actions\Action;

use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\TextInput;

class ViewQuizLeaderboard extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = LeaderboardResource::class;

    protected static string $view = 'filament.resources.leaderboard-resource.pages.view-quiz-leaderboard';

    public Bab $record; // The Bab
    public Quiz $quiz;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return Excel::download(
                        new QuizLeaderboardExport($this->quiz->id, auth()->user()),
                        "leaderboard-{$this->quiz->title}-" . now()->format('Y-m-d') . ".xlsx"
                    );
                }),
        ];
    }

    public function mount($babId, $quizId): void
    {
        $this->record = Bab::findOrFail($babId);
        $this->quiz = Quiz::findOrFail($quizId);
    }

    public function getTitle(): string
    {
        return "Peringkat: {$this->quiz->title}";
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                UserQuiz::query()
                    ->where('quiz_id', $this->quiz->id)
                    ->where('is_completed', true)
                    ->when(auth()->user()->roles->contains('name', 'guru') || auth()->user()->roles->contains('name', 'dosen'), function (Builder $query) {
                        $query->whereHas('user', function ($q) {
                            $q->where('school_id', auth()->user()->school_id);
                        });
                    })
                    ->when(auth()->user()->roles->contains('name', 'siswa') || auth()->user()->roles->contains('name', 'mahasiswa'), function (Builder $query) {
                        $query->whereHas('user', function ($q) {
                            $q->where('school_id', auth()->user()->school_id)
                              ->where('level', auth()->user()->level)
                              ->where('class', auth()->user()->class);
                        });
                    })
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.school.name')
                    ->label('Sekolah')
                    ->sortable(),
                TextColumn::make('user.class')
                    ->label('Kelas')
                    ->sortable(),
                TextColumn::make('score')
                    ->label('Nilai')
                    ->sortable()
                    ->numeric(decimalPlaces: 2),
                TextColumn::make('total_violations')
                    ->label('Pelanggaran')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Selesai Pada')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('class')
                    ->label('Kelas')
                    ->form([
                        TextInput::make('class')
                            ->label('Ketik Kelas')
                            ->placeholder('cth: a, b, c'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['class'])) {
                            $query->whereHas('user', function ($q) use ($data) {
                                $q->where('class', 'like', '%' . $data['class'] . '%');
                            });
                        }
                    }),
            ])
            ->defaultSort('score', 'desc');
    }
}
