<?php

namespace App\Filament\Resources\BabResource\Pages;

use App\Filament\Resources\BabResource;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use App\Models\Quiz;
use App\Models\Bab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Form;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;

class ViewQuizzes extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = BabResource::class;

    protected static string $view = 'filament.resources.bab-resource.pages.view-quizzes';

    public $record;

    public function mount($record): void
    {
        $this->record = Bab::find($record);
        
        if (!$this->record) {
            abort(404, 'Bab tidak ditemukan');
        }
    }

    public function getTitle(): string
    {
        return "Daftar Quiz - {$this->record->name}";
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Quiz::query()
                    ->where('bab_id', $this->record->id)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->label('Judul Quiz'),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable()
                    ->label('Waktu Mulai'),
                Tables\Columns\TextColumn::make('close_date')
                    ->dateTime()
                    ->sortable()
                    ->label('Waktu Selesai'),
                Tables\Columns\TextColumn::make('time_limit')
                    ->numeric()
                    ->sortable()
                    ->label('Durasi (menit)'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form([
                        Select::make('bab_id')
                            ->label('Bab')
                            ->relationship('bab', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->label('Judul Quiz'),
                        DateTimePicker::make('start_date')
                            ->required()
                            ->label('Waktu Mulai'),
                        DateTimePicker::make('close_date')
                            ->required()
                            ->label('Waktu Selesai'),
                        TextInput::make('time_limit')
                            ->numeric()
                            ->required()
                            ->label('Durasi (menit)'),
                        TextInput::make('attempt_limit')
                            ->numeric()
                            ->required()
                            ->label('Batas Percobaan'),
                    ]),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
} 