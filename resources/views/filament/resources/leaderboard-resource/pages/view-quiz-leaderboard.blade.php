<x-filament-panels::page>
    <div class="mb-4">
        <a href="{{ \App\Filament\Resources\LeaderboardResource::getUrl('quizzes', ['babId' => $record]) }}" class="text-sm font-medium text-primary-600 hover:text-primary-500">
            &larr; Kembali ke Daftar Level
        </a>
    </div>
    {{ $this->table }}
</x-filament-panels::page>
