<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - BisaFisika</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-4xl w-full mx-4">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-800 mb-2">Selamat Datang di BisaFisika</h1>
                <p class="text-gray-600">Pilih tipe akun untuk mendaftar</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <a href="{{ route('register.student') }}" class="block">
                    <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-shadow cursor-pointer h-full transform hover:-translate-y-1 transition-transform">
                        <div class="text-center">
                            <img src="{{ asset('images/gambar_siswa.png') }}" alt="Student" class="w-40 h-40 mx-auto mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Daftar sebagai Siswa</h2>
                        </div>
                    </div>
                </a>

                <a href="{{ route('register.teacher') }}" class="block">
                    <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-shadow cursor-pointer h-full transform hover:-translate-y-1 transition-transform">
                        <div class="text-center">
                            <img src="{{ asset('images/gambar_guru.png') }}" alt="Teacher" class="w-40 h-40 mx-auto mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Daftar sebagai Guru</h2>
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