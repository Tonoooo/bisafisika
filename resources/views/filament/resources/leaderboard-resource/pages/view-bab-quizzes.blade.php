<x-filament-panels::page>
    <div class="mb-6">
        <a href="{{ \App\Filament\Resources\LeaderboardResource::getUrl('index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-500">
            &larr; Kembali ke Daftar Bab
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
        @php
            $colors = [
                'bg-blue-50 border-blue-200 text-blue-600',
                'bg-emerald-50 border-emerald-200 text-emerald-600',
                'bg-rose-50 border-rose-200 text-rose-600',
                'bg-amber-50 border-amber-200 text-amber-600',
                'bg-cyan-50 border-cyan-200 text-cyan-600',
                'bg-violet-50 border-violet-200 text-violet-600',
            ];
        @endphp

        @foreach($this->getQuizzes() as $index => $quiz)
            @php
                $colorClass = $colors[$index % count($colors)];
            @endphp
            <a href="{{ \App\Filament\Resources\LeaderboardResource::getUrl('scores', ['babId' => $this->record->id, 'quizId' => $quiz->id]) }}" 
               class="flex flex-col items-center justify-center p-6 transition-all duration-200 border-2 rounded-xl hover:shadow-lg hover:scale-105 {{ $colorClass }}">
                <div class="p-3 mb-4 rounded-lg bg-white/50">
                    <x-heroicon-o-academic-cap class="w-10 h-10" />
                </div>
                <span class="text-lg font-bold text-center">{{ $quiz->title }}</span>
            </a>
        @endforeach
    </div>
</x-filament-panels::page>
