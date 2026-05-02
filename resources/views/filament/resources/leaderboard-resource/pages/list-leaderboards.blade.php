<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
        @php
            $colors = [
                'bg-pink-50 border-pink-200 text-pink-600',
                'bg-blue-50 border-blue-200 text-blue-600',
                'bg-green-50 border-green-200 text-green-600',
                'bg-purple-50 border-purple-200 text-purple-600',
                'bg-yellow-50 border-yellow-200 text-yellow-600',
                'bg-indigo-50 border-indigo-200 text-indigo-600',
                'bg-orange-50 border-orange-200 text-orange-600',
                'bg-teal-50 border-teal-200 text-teal-600',
            ];
        @endphp

        @foreach($this->getBabs() as $index => $bab)
            @php
                $colorClass = $colors[$index % count($colors)];
            @endphp
            <a href="{{ \App\Filament\Resources\LeaderboardResource::getUrl('quizzes', ['babId' => $bab->id]) }}" 
               class="flex flex-col items-center justify-center p-6 transition-all duration-200 border-2 rounded-xl hover:shadow-lg hover:scale-105 {{ $colorClass }}">
                <div class="p-3 mb-4 rounded-lg bg-white/50">
                    <x-heroicon-o-book-open class="w-10 h-10" />
                </div>
                <span class="text-lg font-bold text-center">{{ $bab->name }}</span>
            </a>
        @endforeach
    </div>
</x-filament-panels::page>
