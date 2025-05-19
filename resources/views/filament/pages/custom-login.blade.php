<x-filament-panels::page.simple>
    @if (filament()->hasRegistration())
        <x-slot name="subheading">
            {{ __('filament-panels::pages/auth/login.actions.register.before') }}

            {{ $this->registerAction }}
        </x-slot>
    @endif

    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>
</x-filament-panels::page.simple>

@push('scripts')
    <script>
        // Override the register link
        document.addEventListener('DOMContentLoaded', function() {
            const registerLink = document.querySelector('a[href*="register"]');
            if (registerLink) {
                registerLink.href = '{{ route("register") }}';
            }
        });
    </script>
@endpush
