<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuizResource\Pages;
use App\Models\Question;
use App\Models\Quiz;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class QuizResource extends Resource
{
    protected static ?string $model = Quiz::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
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
                // Forms\Components\CheckboxList::make('questions')
                //     ->relationship('questions', 'content')
                //     ->bulkToggleable(),
                Forms\Components\Select::make('questions')
                    ->label('Questions')
                    ->multiple()
                    ->relationship('questions', 'content')
                    ->options(Question::all()->pluck('content', 'id')->toArray()),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('start_date')->sortable(),
                Tables\Columns\TextColumn::make('close_date')->sortable(),
                Tables\Columns\TextColumn::make('time_limit'),
                Tables\Columns\TextColumn::make('attempt_limit'),
            ])
            ->filters([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuizzes::route('/'),
            'create' => Pages\CreateQuiz::route('/create'),
            'edit' => Pages\EditQuiz::route('/{record}/edit'),
        ];
    }
}
