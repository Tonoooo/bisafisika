<x-layouts.guest>
    <div class="min-h-screen bg-[#f6f0f0] flex items-center justify-center py-12 px-4">
        <div class="max-w-6xl w-full grid grid-cols-1 lg:grid-cols-2 rounded-xl overflow-hidden shadow-2xl">

            <div class="hidden lg:flex items-center justify-center bg-[#d5c7a3] p-8 h-full">
                <div class="text-center text-white">
                     {{-- <x-filament::icon icon="heroicon-o-academic-cap" class="w-24 h-24 mx-auto text-white mb-4" /> --}}
                      <img src="{{ asset('images/einstein.png') }}" alt="Ilustrasi" class="w-60 h-w-60 mx-auto text-white mb-4">
                      <p class="text-2xl font-semibold">Selamat Datang!</p>
                      <p class="mt-2 text-base opacity-90">Bergabunglah dengan kami untuk petualangan fisika yang seru!</p>
                 </div>
            </div>
 
            <div class="bg-[#f2e2b1] p-8 flex items-center justify-center w-full">
                 <div class="w-full max-w-md">
                      <h2 class="text-3xl font-bold mb-8 text-center text-gray-800">Daftar Akun Siswa</h2> 
                    <livewire:register-student />
 
                 </div>
            </div>
        </div>
    </div>
</x-layouts.guest> 