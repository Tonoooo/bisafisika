<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - BelajarFisika</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-4xl w-full mx-4">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-800 mb-2">Selamat Datang di BelajarFisika</h1>
                <p class="text-gray-600">Pilih tipe akun untuk mendaftar</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="{{ route('register.student') }}" class="block">
                    <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl cursor-pointer h-full transform hover:-translate-y-1 transition">
                        <div class="text-center">
                            <img src="{{ asset('images/gambar_siswa.png') }}" alt="Student" class="w-32 h-32 mx-auto mb-4">
                            <h2 class="text-xl font-bold text-gray-800 mb-2">Siswa (SMA)</h2>
                        </div>
                    </div>
                </a>

                <a href="{{ route('register.teacher') }}" class="block">
                    <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl cursor-pointer h-full transform hover:-translate-y-1 transition">
                        <div class="text-center">
                            <img src="{{ asset('images/gambar_guru.png') }}" alt="Teacher" class="w-32 h-32 mx-auto mb-4">
                            <h2 class="text-xl font-bold text-gray-800 mb-2">Guru (SMA)</h2>
                        </div>
                    </div>
                </a>

                <a href="{{ route('register.mahasiswa') }}" class="block">
                    <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl cursor-pointer h-full transform hover:-translate-y-1 transition border-t-4 border-blue-500">
                        <div class="text-center">
                            <img src="{{ asset('images/gambar_siswa.png') }}" alt="Mahasiswa" class="w-32 h-32 mx-auto mb-4">
                            <h2 class="text-xl font-bold text-gray-800 mb-2">Mahasiswa</h2>
                        </div>
                    </div>
                </a>

                <a href="{{ route('register.dosen') }}" class="block">
                    <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl cursor-pointer h-full transform hover:-translate-y-1 transition border-t-4 border-blue-500">
                        <div class="text-center">
                            <img src="{{ asset('images/gambar_guru.png') }}" alt="Dosen" class="w-32 h-32 mx-auto mb-4">
                            <h2 class="text-xl font-bold text-gray-800 mb-2">Dosen</h2>
                        </div>
                    </div>
                </a>
            </div>

            <div class="text-center mt-8">
                <p class="text-gray-600">Sudah punya akun? <a href="{{ route('filament.admin.auth.login') }}" class="text-blue-600 hover:text-blue-800 font-medium">Masuk di sini</a></p>
            </div>
        </div>
    </div>
</body>
</html>