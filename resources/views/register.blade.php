<x-layouts.guest>
    <div class="container mx-auto px-4 py-8 md:mt-20">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <a href="{{ route('register.student') }}" class="block">
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow cursor-pointer h-full">
                    <div class="text-center">
                        <img src="{{ asset('images/gambar_siswa.png') }}" alt="Siswa" class="w-32 h-32 mx-auto mb-4">
                        <h2 class="text-2xl font-bold text-gray-800">Daftar sebagai Siswa</h2>
                    </div>
                    </div>
            </a>
            <a href="{{ route('register.teacher') }}" class="block">
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow cursor-pointer h-full">
                    <div class="text-center">
                        <img src="{{ asset('images/gambar_guru.png') }}" alt="Guru" class="w-32 h-32 mx-auto mb-4">
                        <h2 class="text-2xl font-bold text-gray-800">Daftar sebagai Guru</h2>
                    </div>
                    </div>
            </a>
            <a href="{{ route('register.lecturer') }}" class="block">
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow cursor-pointer h-full">
                    <div class="text-center">
                        <img src="{{ asset('images/gambar_lecturer.png') }}" alt="Lecturer" class="w-32 h-32 mx-auto mb-4">
                        <h2 class="text-2xl font-bold text-gray-800">Daftar sebagai Lecturer</h2>
                    </div>
                </div>
            </a>
        </div>
    </div>
</x-layouts.guest>