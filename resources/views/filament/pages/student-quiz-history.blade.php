<x-filament-panels::page>
    <div class="mb-4">
        <h2 class="text-2xl font-bold">Riwayat Quiz - {{ $this->user->name }}</h2>
        <p class="text-gray-600">
            {{ $this->user->school->name }} - Kelas {{ $this->user->level }}{{ $this->user->class }}
        </p>
    </div>

    {{ $this->table }}
</x-filament-panels::page> 