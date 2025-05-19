<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Bisa Fisika') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) <!-- Pastikan app.js memuat Alpine.js -->
    @livewireStyles
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>