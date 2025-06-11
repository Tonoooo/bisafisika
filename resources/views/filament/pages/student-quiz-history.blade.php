<x-filament-panels::page>
    <div class="mb-4">
        <h2 class="text-2xl font-bold">Riwayat Quiz - {{ $this->user->name }}</h2>
        <p class="text-gray-600">
            @if($this->user->school)
                {{ $this->user->school->name }}
            @else
                Sekolah: Lainnya
            @endif
            @if($this->user->level && $this->user->class)
                - Kelas {{ $this->user->level }}{{ $this->user->class }}
            @else
                - Kelas: Lainnya
            @endif
        </p>
    </div>

    {{ $this->table }}
</x-filament-panels::page> 