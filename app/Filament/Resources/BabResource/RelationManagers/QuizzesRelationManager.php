<?php

namespace App\Filament\Resources\BabResource\RelationManagers;

use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuizzesRelationManager extends RelationManager
{
    protected static string $relationship = 'quizzes';

    protected static ?string $recordTitleAttribute = 'title';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required(),
                Forms\Components\DateTimePicker::make('start_date')
                    ->required(),
                Forms\Components\DateTimePicker::make('close_date')
                    ->required(),
                Forms\Components\TextInput::make('time_limit')
                    ->numeric()
                    ->helpertext('menit')
                    ->required(),
                Forms\Components\TextInput::make('attempt_limit')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('questions')
                    ->label('Questions')
                    ->multiple()
                    ->relationship('questions', 'content')
                    ->options(Question::all()->pluck('content', 'id')->toArray()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
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
                Tables\Columns\TextColumn::make('attempt_limit')
                    ->numeric()
                    ->sortable()
                    ->label('Batas Percobaan'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
} 