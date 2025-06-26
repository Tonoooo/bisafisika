<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Resources\QuestionResource\RelationManagers;
use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use App\Traits\HasRepeaterIndex;
use Filament\Forms\Components\Select;

class QuestionResource extends Resource
{
    use HasRepeaterIndex;

    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Quiz Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Repeater::make('random_variables')
                    ->schema([
                        Forms\Components\Grid::make(5)
                            ->schema([
                                Forms\Components\TextInput::make('variabel')
                                    ->label('Placeholder')
                                    ->default(function ($get, $set, $state, $component) {
                                        $statePath = $component->getStatePath();
                                        $items = $get('../../random_variables') ?? [];
                                        $uuid = explode('.', $statePath)[2] ?? null;
                                        if (empty($items) || !$uuid) {
                                            return "randomnumber1";
                                        }
                                        $keys = array_keys($items);
                                        $index = array_search($uuid, $keys) !== false ? array_search($uuid, $keys) + 1 : 1;
                                        return "randomnumber" . $index;
                                    })
                                    ->required()
                                    ->afterStateUpdated(function ($state, $get, $component) {
                                        $statePath = $component->getStatePath();
                                        $items = $get('../../random_variables') ?? [];
                                        $values = array_column($items, 'variabel');
                                        $currentIndex = array_search($state, $values);
                                        $count = array_count_values($values)[$state] ?? 0;

                                        if ($count > 1) {
                                            $component->addError('variabel', 'Placeholder harus unik di dalam soal ini.');
                                        }
                                    })
                                    ->helperText('Misalnya:panjang,gaya.Tanpa spasi')
                                    ->placeholder('Masukkan placeholder, misalnya randomnumber1'),

                                Forms\Components\Select::make('type')
                                    ->label('Tipe Bilangan')
                                    ->options([
                                        'integer' => 'Bilangan Bulat',
                                        'decimal' => 'Bilangan Desimal',
                                    ])
                                    ->default('integer')
                                    ->required(),

                                Forms\Components\TextInput::make('min_value')
                                    ->label('Nilai Minimum')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->helperText('Gunakan titik(.) jika desimal')
                                    ->placeholder('1'),

                                Forms\Components\Placeholder::make('to')
                                    ->content('hingga'),

                                Forms\Components\TextInput::make('max_value')
                                    ->label('Nilai Maksimum')
                                    ->numeric()
                                    ->default(100)
                                    ->required()
                                    ->placeholder('100')
                                    ->rules([
                                        fn ($get) => function ($attribute, $value, $fail) use ($get) {
                                            $minValue = floatval($get('min_value'));
                                            $maxValue = floatval($value);
                                            if ($maxValue < $minValue) {
                                                $fail('Nilai maksimum harus lebih besar atau sama dengan nilai minimum.');
                                            }
                                        },
                                    ]),
                            ])
                            ->columns(5),
                    ])
                    ->label('Variabel Random Angka')
                    ->minItems(1)
                    ->defaultItems(1)
                    ->addActionLabel('Tambah Variabel')
                    ->reorderable(false)
                    ->required()
                    ->rules(['required', 'array', 'min:1'])
                    ->columnSpanFull()
                    ->afterStateHydrated(function ($component, $state, $record) {
                        // Log::info('Random Variables After State Hydrated', [
                        //     'state' => $state,
                        //     'record' => $record ? $record->toArray() : null,
                        // ]);

                        if (is_null($state) && $record) {
                            $randomVariables = $record->random_variables;
                            // Log::info('Hydrating Random Variables from Accessor', ['random_variables' => $randomVariables]);
                            $component->state($randomVariables);
                        }
                    })
                    ->afterStateUpdated(function ($state) {
                        // Log::info('Random Variables State Updated', ['state' => $state]);
                        if (empty($state)) {
                            // Log::warning('Random Variables is Empty on Update');
                        }
                    }),

                Forms\Components\Textarea::make('content')
                    ->label('Question Content')
                    ->required()
                    ->columnSpanFull()
                    ->helperText('Use %randomnumber% as a placeholder for random numbers.'),

                Forms\Components\Repeater::make('rumus')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Placeholder::make('nama variabel hasil')
                                    ->content(function ($get, $set, $state, $component) {
                                        $statePath = $component->getStatePath();
                                        $items = $get('../../rumus') ?? [];
                                        $uuid = explode('.', $statePath)[2] ?? null;
                                        if (empty($items) || !$uuid) {
                                            return "variabelhasil1";
                                        }
                                        $keys = array_keys($items);
                                        $index = array_search($uuid, $keys) !== false ? array_search($uuid, $keys) + 1 : 1;
                                        return "variabelhasil" . $index;
                                    })
                                    ->helperText('Gunakan operator: +, -, *, /. Fungsi: cos(), sin(), tan() (dalam derajat), sqrt(). Pangkat: a**b (contoh: 2**3). Desimal gunakan titik(.). Contoh: 2 * %tegangan% * cos(30) + sqrt(16)'),

                                Forms\Components\Textarea::make('variabel_rumus')
                                    ->label('Rumus')
                                    ->rows(4)
                                    ->columnSpan(2)
                                    ->required()
                                ]),
                    ])
                    ->label('Variabel Rumus')
                    ->addActionLabel('Tambah Rumus')
                    ->reorderable(false)
                    ->afterStateHydrated(function ($component, $state) {
                        // Log::info('Rumus State Hydrated', ['state' => $state]);
                    }),

                Forms\Components\Repeater::make('answers')
                    ->schema([
                        Forms\Components\Textarea::make('content')
                            ->label('Answer Content')
                            ->required(),
                        Forms\Components\Checkbox::make('is_correct')
                            ->label('Is Correct')
                            ->default(false),
                    ])
                    ->label('Answers')
                    ->minItems(2)
                    ->required()
                    ->afterStateHydrated(function ($component, $state, $record) {
                        // Log::info('Answers After State Hydrated', [
                        //     'state' => $state,
                        //     'record' => $record ? $record->toArray() : null,
                        // ]);

                        if (is_null($state) && $record) {
                            $answers = $record->attributes['answers'] ?? [];
                            $answers = is_string($answers) ? json_decode($answers, true) : $answers;
                            // Log::info('Hydrating Answers from Record', ['answers' => $answers]);
                            $component->state($answers);
                        }
                    }),

                Forms\Components\FileUpload::make('image_path')
                    ->label('Image')
                    ->directory('questions')
                    ->disk('public') 
                    ->visibility('public') 
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('content')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Image')
                    ->disk('public') 
                    ->visibility('public'), 
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}