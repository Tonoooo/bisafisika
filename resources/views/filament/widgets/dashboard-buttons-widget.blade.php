<x-filament::widget>
    <x-filament::card>
        <div style="text-align: center; margin-bottom: 1rem;">
            <h2 class="text-2xl font-bold">Selamat Datang</h2>
        </div>
        {{-- Gambar elang dari direktori public --}}
        <div style="">
            <img src="{{ asset('images/elang.png') }}" alt="Gambar Elang" style="max-width: 100px; height: auto; margin: 0 auto;">
        </div>
        
        {{-- Menggunakan flexbox untuk menata tombol-tombol secara horizontal --}}
        <div class="flex flex-row items-center justify-center space-x-4 p-4">
            {{-- Wadah untuk Tombol Take Quiz --}}
            {{-- Gunakan flex-shrink-0 agar tombol tidak menyusut di ruang sempit --}}
            {{-- Tambahkan lebar agar rapi --}}
            <div class="flex-shrink-0 border border-gray-300 dark:border-gray-700 rounded-lg p-4 flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-800 text-center w-1/3 me-5" style="margin-right: 10px; width: 100px;">
                <a href="{{ url('/admin/take-quiz') }}" class="flex flex-col items-center justify-center w-full h-full text-gray-700 dark:text-gray-300 hover:text-primary-500 dark:hover:text-primary-400 transition">
                    {{-- Ikon Kuis --}}
                    <x-filament::icon
                        icon="heroicon-o-play"
                        class="h-5 w-5 mb-2" {{-- Ukuran ikon sesuai penyesuaian terakhir --}}
                    />
                    {{-- Teks Kuis --}}
                    <p class="text-sm font-medium">Kuis</p>
                </a>
            </div>

            {{-- Wadah untuk Tombol Leaderboard --}}
            {{-- Gunakan flex-shrink-0 agar tombol tidak menyusut di ruang sempit --}}
             {{-- Tambahkan lebar agar rapi --}}
            <div class="flex-shrink-0 border border-gray-300 dark:border-gray-700 rounded-lg p-4 flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-800 text-center w-1/3" style="margin-right: 10px; width: 100px;">
                <a href="{{ url('/admin/leaderboards') }}" class="flex flex-col items-center justify-center w-full h-full text-gray-700 dark:text-gray-300 hover:text-primary-500 dark:hover:text-primary-400 transition">
                    {{-- Ikon Peringkat --}}
                    <x-filament::icon
                        icon="heroicon-o-trophy"
                        class="h-5 w-5 mb-2" {{-- Ukuran ikon sesuai penyesuaian terakhir --}}
                    />
                    {{-- Teks Peringkat --}}
                    <p class="text-sm font-medium">Peringkat</p>
                </a>
            </div>

            {{-- Wadah untuk Tombol Riwayat Quiz --}}
            {{-- Gunakan flex-shrink-0 agar tombol tidak menyusut di ruang sempit --}}
             {{-- Tambahkan lebar agar rapi --}}
            <div class="flex-shrink-0 border border-gray-300 dark:border-gray-700 rounded-lg p-4 flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-800 text-center w-1/3" style="width: 100px;">
                 <a href="{{ url('/quiz/history') }}" class="flex flex-col items-center justify-center w-full h-full text-gray-700 dark:text-gray-300 hover:text-primary-500 dark:hover:text-primary-400 transition">
                    {{-- Ikon Riwayat --}}
                    <x-filament::icon
                        icon="heroicon-o-clock"
                        class="h-5 w-5 mb-2" {{-- Ukuran ikon sesuai penyesuaian terakhir --}}
                    />
                    {{-- Teks Riwayat --}}
                    <p class="text-sm font-medium">Riwayat</p>
                 </a>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
