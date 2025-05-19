<x-filament-panels::page.simple>
    <x-slot name="subheading">
        Belum punya akun? 
        <a href="{{ route('register') }}" class="text-primary-600 hover:text-primary-500">
            Daftar di sini
        </a>
    </x-slot>

    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>
</x-filament-panels::page.simple>