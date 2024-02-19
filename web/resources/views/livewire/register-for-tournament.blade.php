<x-filament-panels::page.simple>
    <x-filament-panels::form wire:submit="submitRegistration">
        {{ $this->form }}

        @if($this->isTournamentFull)
            <p class="text-sm text-danger-600 dark:text-danger-400 text-center">Ez a verseny már sajnos megtelt!</p>
        @endif

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>
</x-filament-panels::page.simple>

@push('styles')
    @vite('resources/css/app.css')
@endpush
