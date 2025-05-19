<?php

namespace App\Filament\Resources\BabResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// Pastikan mengimpor komponen Filament yang dibutuhkan
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select; // Jika Anda butuh Select di Relation Manager Form
use Illuminate\Support\Facades\Log;
// use App\Traits\HasRepeaterIndex; // Jika trait ini relevan di sini

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions'; // Nama relasi di model Bab

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Salin field dari form QuestionResource di sini
                // Hilangkan field 'bab_id' karena kita sudah ada di dalam Bab
                Repeater::make('random_variables')
                    ->schema([
                        Grid::make(5)
                            ->schema([
                                TextInput::make('variabel')
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
                                    ->helperText('Misalnya: panjang, gaya')
                                    ->placeholder('Masukkan placeholder, misalnya randomnumber1'),

                                Select::make('type')
                                    ->label('Tipe Bilangan')
                                    ->options([
                                        'integer' => 'Bilangan Bulat',
                                        'decimal' => 'Bilangan Desimal',
                                    ])
                                    ->default('integer')
                                    ->required(),

                                TextInput::make('min_value')
                                    ->label('Nilai Minimum')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->helperText('Gunakan titik(.) jika desimal')
                                    ->placeholder('1'),

                                Placeholder::make('to')
                                    ->content('hingga'),

                                TextInput::make('max_value')
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
                    ->columnSpanFull(),
                    // ->afterStateHydrated(function ($component, $state, $record) {
                    //     // Logika hydrating jika diperlukan
                    // })
                    // ->afterStateUpdated(function ($state) {
                    //     // Logika after update jika diperlukan
                    // }),

                Textarea::make('content')
                    ->label('Question Content')
                    ->required()
                    ->columnSpanFull()
                    ->helperText('Use %randomnumber% as a placeholder for random numbers.'),

                Repeater::make('rumus')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Placeholder::make('nama variabel hasil')
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
                                    ->helperText('Gunakan operator: +, -, *, /. Fungsi: cos(), sin(), tan() (dalam derajat), sqrt(). Pangkat: a**b (contoh: 2**3). Contoh: 2 * %tegangan% * cos(30) + sqrt(16)'),

                                Textarea::make('variabel_rumus')
                                    ->label('Rumus')
                                    ->rows(4)
                                    ->columnSpan(2)
                                    ->required()
                                    ->helperText('Gunakan %randomnumberX% untuk variabel acak, dan %variabelhasilX% untuk hasil rumus sebelumnya. desimal gunakan titik(.)'),
                            ]),
                    ])
                    ->label('Variabel Rumus')
                    ->addActionLabel('Tambah Rumus')
                    ->reorderable(false),
                    // ->afterStateHydrated(function ($component, $state) {
                    //     // Logika hydrating jika diperlukan
                    // }),

                Repeater::make('answers')
                    ->schema([
                        Textarea::make('content')
                            ->label('Answer Content')
                            ->required(),
                        Checkbox::make('is_correct')
                            ->label('Is Correct')
                            ->default(false),
                    ])
                    ->label('Answers')
                    ->minItems(2)
                    ->required()
                    ->afterStateHydrated(function ($component, $state, $record) {
                        // Logika hydrating jika diperlukan
                    }),

                FileUpload::make('image_path')
                    ->label('Image')
                    ->directory('questions')
                    ->disk('public')
                    ->visibility('public')
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('content') // Menampilkan sebagian konten pertanyaan di header
            ->columns([
                // Salin kolom dari tabel QuestionResource di sini
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
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(), // Izinkan membuat pertanyaan baru di bab ini
            ])
            ->actions([
                Tables\Actions\EditAction::make(), // Izinkan mengedit pertanyaan
                Tables\Actions\DeleteAction::make(), // Izinkan menghapus pertanyaan
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
