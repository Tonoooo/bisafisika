<x-filament::widget>
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden h-full flex flex-col">
                <div class="relative p-6 bg-gradient-to-br from-primary-500 to-primary-600 ">
                    <div class="relative z-10">
                        <div class="flex items-center mb-4 justify-center">
                            <img src="{{ asset('images/einstein.png') }}" alt="Logo" class="w-16 h-16 mr-4">
                            <div>
                                <h2 class="text-2xl font-bold">Selamat Datang</h2>
                                <p class="text-xl font-light -mt-1">{{ auth()->user()->name }}!</p>
                            </div>
                        </div>
                        <div class="flex justify-center">
                            <p class="mt-2 text-sm text-primary-200 max-w-xs text-center">Siap taklukkan tantangan fisika hari ini dan jadi yang terbaik?</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 flex-grow flex flex-col">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <a href="{{ url('/admin/take-quiz') }}" class="group flex flex-col justify-center items-center p-4 bg-gray-100 dark:bg-gray-700/80 hover:bg-white dark:hover:bg-gray-700 rounded-lg shadow-md transition-all duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-lg">
                            <x-filament::icon icon="heroicon-o-academic-cap" class="w-8 h-8 mb-2 text-primary-500 dark:text-primary-400 transition-transform duration-300 group-hover:scale-110" />
                            <p class="font-semibold text-gray-800 dark:text-gray-200">Mulai Kuis</p>
                        </a>
    
                        @if(auth()->user()->hasRole('teacher'))
                        <a href="{{ url('/admin/leaderboards') }}" class="group flex flex-col justify-center items-center p-4 bg-gray-100 dark:bg-gray-700/80 hover:bg-white dark:hover:bg-gray-700 rounded-lg shadow-md transition-all duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-lg">
                            <x-filament::icon icon="heroicon-o-trophy" class="w-8 h-8 mb-2 text-primary-500 dark:text-primary-400 transition-transform duration-300 group-hover:scale-110" />
                            <p class="font-semibold text-gray-800 dark:text-gray-200">Papan Peringkat</p>
                        </a>
                        @endif
    
                        <a href="{{ route('filament.admin.pages.quiz-history') }}" class="group flex flex-col justify-center items-center p-4 bg-gray-100 dark:bg-gray-700/80 hover:bg-white dark:hover:bg-gray-700 rounded-lg shadow-md transition-all duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-lg">
                            <x-filament::icon icon="heroicon-o-clock" class="w-8 h-8 mb-2 text-primary-500 dark:text-primary-400 transition-transform duration-300 group-hover:scale-110" />
                            <p class="font-semibold text-gray-800 dark:text-gray-200">Riwayat Kuis</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>    
    </div>
</x-filament::widget>