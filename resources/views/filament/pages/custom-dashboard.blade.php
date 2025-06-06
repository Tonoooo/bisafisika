<x-filament-panels::page>
    {{-- Widget Gambar Elang (satu baris penuh) --}}
    <div class="col-span-full">
        @include('filament.widgets.eagle-image-widget')
    </div>

    {{-- Widget Tombol Dashboard (satu baris penuh) --}}
    <div class="col-span-full">
        @include('filament.widgets.dashboard-buttons-widget')
    </div>

    {{-- Widget Tabel Leaderboard (satu baris penuh) --}}
    <div class="col-span-full">
        @include('filament.widgets.top-students-leaderboard-widget')
    </div>
</x-filament-panels::page> 