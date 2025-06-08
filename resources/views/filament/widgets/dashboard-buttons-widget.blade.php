<x-filament::widget>
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- KOLOM KIRI: KARTU SELAMAT DATANG & AKSI --}}
        <div class="lg:col-span-2">
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden h-full flex flex-col">

                {{-- [DIPERBAIKI] Banner Atas dengan Gradien yang Lebih Aman & Layout yang Disesuaikan --}}
                {{-- Menggunakan gradien dari primary-500 ke primary-600 yang pasti ada di Filament --}}
                <div class="relative p-6 bg-gradient-to-br from-primary-500 to-primary-600 ">
                    <div class="relative z-10">
                        <h2 class="text-3xl font-bold">Selamat Datang,</h2>
                        <p class="text-xl font-light -mt-1">{{ auth()->user()->name }}!</p>
                        <p class="mt-2 text-sm text-primary-200 max-w-xs">Siap taklukkan tantangan fisika hari ini dan jadi yang terbaik?</p>
                    </div>

                    {{-- [DIPERBAIKI] Posisi dan Ukuran Gambar Elang Disesuaikan --}}
                    {{-- Posisi diatur agar tidak keluar dari banner dan ukurannya lebih pas --}}
                    {{-- Transformasi menggunakan nilai negatif untuk menarik gambar ke dalam, bukan mendorong keluar --}}
                    {{-- <img src="{{ asset('images/elang.png') }}" alt="Elang BisaFisika" 
                         class="right-0 w-24 h-auto pointer-events-none " 
                         style="filter: drop-shadow(0 5px 15px rgba(0,0,0,0.3)); width: 250px "> --}}

                </div>

                {{-- Wadah Tombol Aksi (Tidak ada perubahan, posisinya akan benar sekarang) --}}
                <div class="p-6 flex-grow flex flex-col">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Aksi Cepat</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- Tombol 1: Mulai Kuis --}}
                        <a href="{{ url('/admin/quiz') }}" class="group flex flex-col justify-center items-center p-4 bg-gray-100 dark:bg-gray-700/80 hover:bg-white dark:hover:bg-gray-700 rounded-lg shadow-md transition-all duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-lg">
                            <x-filament::icon icon="heroicon-o-academic-cap" class="w-8 h-8 mb-2 text-primary-500 dark:text-primary-400 transition-transform duration-300 group-hover:scale-110" />
                            <p class="font-semibold text-gray-800 dark:text-gray-200">Mulai Kuis</p>
                        </a>
    
                        @if(auth()->user()->hasRole('teacher'))
                        <a href="{{ url('/admin/leaderboards') }}" class="group flex flex-col justify-center items-center p-4 bg-gray-100 dark:bg-gray-700/80 hover:bg-white dark:hover:bg-gray-700 rounded-lg shadow-md transition-all duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-lg">
                            <x-filament::icon icon="heroicon-o-trophy" class="w-8 h-8 mb-2 text-primary-500 dark:text-primary-400 transition-transform duration-300 group-hover:scale-110" />
                            <p class="font-semibold text-gray-800 dark:text-gray-200">Papan Peringkat</p>
                        </a>
                        @endif
    
                        {{-- Tombol 3: Riwayat Kuis --}}
                        <a href="{{ route('filament.admin.pages.quiz-history') }}" class="group flex flex-col justify-center items-center p-4 bg-gray-100 dark:bg-gray-700/80 hover:bg-white dark:hover:bg-gray-700 rounded-lg shadow-md transition-all duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-lg">
                            <x-filament::icon icon="heroicon-o-clock" class="w-8 h-8 mb-2 text-primary-500 dark:text-primary-400 transition-transform duration-300 group-hover:scale-110" />
                            <p class="font-semibold text-gray-800 dark:text-gray-200">Riwayat Kuis</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    
        {{-- KOLOM KANAN: LEADERBOARD --}}
        {{-- <div class="lg:col-span-3">
            <x-filament::card class="h-full flex flex-col">
                 <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">Papan Peringkat Teratas</h3>
                 <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Lihat posisi para jawara fisika saat ini.</p>
                 
                 <div class="mt-4">
                    {{ $this->table }}
                 </div>
            </x-filament::card>
        </div> --}}
    
    </div>
</x-filament::widget>