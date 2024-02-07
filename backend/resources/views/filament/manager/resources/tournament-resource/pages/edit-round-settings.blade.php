@php use App\Models\Enums\EventStatus; @endphp
<x-filament-panels::page>
    <x-filament-panels::form
        :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
        wire:submit="create"
    >
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    @if ($record->status !== EventStatus::UPCOMING)
        <p class="text-sm text-center">Ez a verseny már
            {{ mb_strtolower($record->status->getLabel()) . ($record->status === EventStatus::ONGOING ? ' van' : '') }},
            ezért nem lehet módosítani a fordulók beállításait!</p>
    @endif

    <x-filament-panels::page.unsaved-data-changes-alert/>
</x-filament-panels::page>
