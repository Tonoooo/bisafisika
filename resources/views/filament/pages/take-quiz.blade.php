<x-filament-panels::page>
    <div class="space-y-6">
        @if(!$selectedBab)
            <div class="flex flex-wrap gap-3 justify-start">
                @php
                    $colors = [
                        [
                            'bg' => '#fce7f3', // pink pastel
                            'border' => '#f9a8d4',
                            'icon' => '#db2777'
                        ],
                        [
                            'bg' => '#dbeafe', // biru pastel
                            'border' => '#93c5fd',
                            'icon' => '#2563eb'
                        ],
                        [
                            'bg' => '#dcfce7', // hijau pastel
                            'border' => '#86efac',
                            'icon' => '#16a34a'
                        ],
                        [
                            'bg' => '#f3e8ff', // ungu pastel
                            'border' => '#c4b5fd',
                            'icon' => '#7e22ce'
                        ],
                        [
                            'bg' => '#fef3c7', // kuning pastel
                            'border' => '#fcd34d',
                            'icon' => '#d97706'
                        ],
                        [
                            'bg' => '#e0e7ff', // indigo pastel
                            'border' => '#a5b4fc',
                            'icon' => '#4f46e5'
                        ],
                        [
                            'bg' => '#cffafe', // cyan pastel
                            'border' => '#67e8f9',
                            'icon' => '#0891b2'
                        ],
                        [
                            'bg' => '#ffedd5', // oranye pastel
                            'border' => '#fdba74',
                            'icon' => '#ea580c'
                        ]
                    ];
                @endphp
                @foreach($babs as $index => $bab)
                    @php
                        $colorIndex = $index % count($colors);
                        $color = $colors[$colorIndex];
                    @endphp
                    <div class="p-4 transition-all w-40 duration-200 rounded-lg cursor-pointer hover:shadow-md border-2"
                         style="background-color: {{ $color['bg'] }}; border-color: {{ $color['border'] }};"
                         wire:click="selectBab({{ $bab->id }})">
                        <div class="flex flex-col items-center justify-center text-center">
                            <div class="p-2 mb-2 rounded-full">
                                <x-heroicon-o-book-open class="w-5 h-5" style="color: {{ $color['icon'] }};" />
                            </div>
                            <h3 class="text-sm font-medium text-gray-700 truncate w-full">{{ $bab->name }}</h3>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="max-w-3xl p-6 mx-auto bg-white rounded-lg shadow">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">{{ $selectedBab->name }}</h2>
                    <button wire:click="backToBabs" class="p-2 text-gray-500 transition-colors duration-200 rounded-full hover:bg-gray-100">
                        <x-heroicon-o-arrow-left class="w-5 h-5" />
                    </button>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    @forelse($quizzes as $quiz)
                        <div class="p-4 transition-all duration-200 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 hover:shadow-md"
                             wire:click="startQuiz({{ $quiz->id }})">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-medium text-gray-900">{{ $quiz->title }}</h3>
                                    <div class="flex items-center mt-1 space-x-2 text-sm text-gray-500">
                                        <x-heroicon-o-document-text class="w-4 h-4" />
                                        <span>{{ $quiz->questions->count() }} soal</span>
                                    </div>
                                </div>
                                <x-heroicon-o-chevron-right class="w-5 h-5 text-gray-400" />
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 p-4 text-center text-gray-500 bg-gray-50 rounded-lg">
                            <x-heroicon-o-exclamation-circle class="w-8 h-8 mx-auto mb-2 text-gray-400" />
                            <p>Belum ada quiz untuk bab ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page> 