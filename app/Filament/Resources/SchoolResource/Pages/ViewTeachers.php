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

class ViewTeachers extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = SchoolResource::class;

    protected static string $view = 'filament.resources.school-resource.pages.view-teachers';

    public $record;

    public function getTitle(): string
    {
        $school = School::find($this->record);
        if (!$school) {
            abort(404, 'Sekolah tidak ditemukan');
        }
        return "Daftar Guru - {$school->name}";
    }

    protected function getTableQuery(): ?Builder
    {
        $school = School::find($this->record);
        
        if (!$school) {
            return null; 
        }

        return User::query()
            ->where('school_id', $school->id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'guru');
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
                        $query->where('name', 'guru');
                    })
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Guru')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('level')
                //     ->label('Tingkat')
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('class')
                //     ->label('Kelas')
                //     ->sortable(),
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

    public function getTableRecordActionUsing(): ?Closure
    {
        return null;
    }

    public function getTableRecordUrlUsing(): ?Closure
    {
        return null;
    }

    public function isTableRecordSelectable(): bool
    {
        return false; 
    }
} 