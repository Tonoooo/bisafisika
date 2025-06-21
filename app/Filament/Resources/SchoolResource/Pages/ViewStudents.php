<?php

namespace App\Filament\Resources\SchoolResource\Pages;

use App\Filament\Resources\SchoolResource;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use App\Models\User;
use App\Models\School;
use Illuminate\Database\Eloquent\Builder;
use Closure;

class ViewStudents extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = SchoolResource::class;

    protected static string $view = 'filament.resources.school-resource.pages.view-students';

    public $record;

    public function getTitle(): string
    {
        $school = School::find($this->record);
        if (!$school) {
            abort(404, 'Sekolah tidak ditemukan');
        }
        return "Daftar Siswa - {$school->name}";
    }

    protected function getTableQuery(): ?Builder
    {
        $school = School::find($this->record);
        
        if (!$school) {
            return null; // Return null jika sekolah tidak ditemukan
        }

        return User::query()
            ->where('school_id', $school->id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'siswa');
            });
    }

    public function table(Table $table): Table
    {
        $school = School::find($this->record);
        
        if (!$school) {
            return $table;
        }

        return $table
            ->query(
                User::query()
                    ->where('school_id', $school->id)
                    ->whereHas('roles', function ($query) {
                        $query->where('name', 'siswa');
                    })
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Siswa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('level')
                    ->label('Tingkat')
                    ->sortable(),
                Tables\Columns\TextColumn::make('class')
                    ->label('Kelas')
                    ->sortable(),
                // Tables\Columns\TextColumn::make('status')
                //     ->label('Status')
                //     ->badge()
                //     ->color(fn (string $state): string => match ($state) {
                //         'verified' => 'success',
                //         'pending' => 'warning',
                //     }),
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

    // Override method ini untuk menonaktifkan aksi klik
    public function getTableRecordActionUsing(): ?Closure
    {
        return null;
    }

    // Override method ini untuk menonaktifkan URL
    public function getTableRecordUrlUsing(): ?Closure
    {
        return null;
    }

    // Override method ini untuk menonaktifkan seleksi baris
    public function isTableRecordSelectable(): bool
    {
        return false; // Menonaktifkan seleksi baris
    }
}