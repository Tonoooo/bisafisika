<?php

namespace App\Filament\Resources\BabResource\Pages;

use App\Filament\Resources\BabResource;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Builder;

class ViewQuizzes extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = BabResource::class;

    protected static string $view = 'filament.resources.bab-resource.pages.view-quizzes';

    public $record;

    public function getTitle(): string
    {
        $bab = \App\Models\Bab::find($this->record);
        if (!$bab) {
            abort(404, 'Bab tidak ditemukan');
        }
        return "Daftar Quiz - {$bab->name}";
    }

    public function table(Table $table): Table
    {
        $bab = \App\Models\Bab::find($this->record);
        
        if (!$bab) {
            return $table;
        }

        return $table
            ->query(
                Quiz::query()
                    ->where('bab_id', $bab->id)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Quiz')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->sortable(),
                Tables\Columns\TextColumn::make('close_date')
                    ->label('Tanggal Selesai')
                    ->sortable(),
                Tables\Columns\TextColumn::make('time_limit')
                    ->label('Batas Waktu (menit)'),
                Tables\Columns\TextColumn::make('attempt_limit')
                    ->label('Batas Percobaan'),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ])
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(10)
            ->deferLoading()
            ->recordAction(null)
            ->recordUrl(null)
            ->selectable(false);
    }
} 