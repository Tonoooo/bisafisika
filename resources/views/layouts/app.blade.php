<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }} ">
<head>
    <meta charset="utf-8">
    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon_io/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon_io/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon_io/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon_io/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('images/favicon_io/site.webmanifest') }}">
    
    <style>
        [x-cloak] { display: none !important; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="antialiased bg-gray-100">
    <div class="flex flex-col min-h-screen">
        @include('layouts.navigation')
        <!-- Page Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>
        @include('layouts.footer')
    </div>
    @livewire('notifications')
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [['\\(', '\\)']],
                displayMath: [['$$', '$$']]
            }
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <script>
        function renderMathJax() {
            if (window.MathJax) {
                setTimeout(() => {
                    MathJax.typesetPromise();
                }, 50);
            }
        }
        document.addEventListener('livewire:load', function () {
            window.Livewire.hook('element.updated', (el, component) => {
                renderMathJax();
            });
            window.Livewire.hook('message.processed', (message, component) => {
                renderMathJax();
            });
        });
    </script>
</body>
</html>
