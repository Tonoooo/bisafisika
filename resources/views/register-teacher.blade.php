<x-layouts.guest>
    {{-- Wadah utama dengan latar belakang #f6f0f0 dan layout grid responsif --}}
    {{-- Di desktop (lg), bagi menjadi 2 kolom. Di mobile, tetap 1 kolom. --}}
    <div class="min-h-screen bg-[#f6f0f0] flex items-center justify-center py-12 px-4">
        {{-- Kontainer untuk menampung kedua 'kolom' desain --}}
        {{-- Gunakan grid responsif untuk 2 kolom di layar besar --}}
        <div class="max-w-6xl w-full grid grid-cols-1 lg:grid-cols-2 rounded-xl overflow-hidden shadow-2xl">
 
            {{-- KOLOM KIRI (untuk ilustrasi/area non-form) --}}
            {{-- Beri warna background #d5c7a3 dan pastikan mengisi tinggi --}}
            <div class="hidden lg:flex items-center justify-center bg-[#d5c7a3] p-8 h-full">
                 {{-- Placeholder atau area untuk ilustrasi --}}
                 <div class="text-center text-white">
                      {{-- Anda bisa tambahkan ilustrasi di sini --}}
                      <x-filament::icon icon="heroicon-o-academic-cap" class="w-24 h-24 mx-auto text-white mb-4" /> {{-- Contoh: Tambahkan icon --}}
                      <p class="text-2xl font-semibold">Halo Guru!</p> {{-- Sesuaikan teks --}}
                      <p class="mt-2 text-base opacity-90">Daftarkan diri Anda dan kelola aktivitas belajar mengajar.</p> {{-- Sesuaikan teks --}}
                 </div>
            </div>
 
            {{-- KOLOM KANAN (untuk Formulir Pendaftaran) --}}
            {{-- Gunakan warna latar belakang #f2e2b1 --}}
            <div class="bg-[#f2e2b1] p-8 flex items-center justify-center w-full">
                 <div class="w-full max-w-md">
                      <h2 class="text-3xl font-bold mb-8 text-center text-gray-800">Daftar Akun Guru</h2> {{-- Perbarui judul --}}
 
                      {{-- Livewire component untuk formulir guru --}}
                      {{-- Formulir akan berada di sini dengan styling internal --}}
                      <livewire:register-teacher />
 
                 </div>
            </div>
        </div>
    </div>
</x-layouts.guest> 