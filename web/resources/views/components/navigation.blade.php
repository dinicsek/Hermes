@props(['absolute' => false])

<nav @class(['flex w-full justify-between py-3 pl-4 pr-3', 'absolute top-0 left-0 right-0' => $absolute])>
    <a href="/" class="text-2xl font-medium text-transparent bg-clip-text bg-gradient-to-tr from-sky-500 to-blue-600">
        Hermes</a>
    <div class="flex gap-4">
        <x-filament::button tag="a" href="{{ route('tournaments') }}">Versenyek</x-filament::button>
        <x-filament::button tag="a" href="{{ route('filament.common.auth.login') }}" color="gray">Belépés
        </x-filament::button>
    </div>
</nav>
