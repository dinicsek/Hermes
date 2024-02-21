@php use Illuminate\Support\Carbon; @endphp
<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex justify-between items-center">
                <div class="flex gap-2">
                    <p>Jelenlegi meccs kezelése</p>
                    @if($currentTournamentData !== null)
                        <x-filament::badge>{{ $currentTournamentData->tournament_name }}</x-filament::badge>
                    @endif
                </div>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="tournament_id">
                        <option value="0">-</option>
                        @foreach($this->selectableTournaments as $tournament)
                            <option value="{{ $tournament['id'] }}">{{ $tournament['name'] }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
        </x-slot>
        @if ($currentTournamentData !== null)
            <div class="flex gap-2 justify-center" x-data="
                {
                    elapsedTime: 0,
                    intervalId: null,
                    calculateElapsedTime: function() {

                        if ($wire.currentTournamentData.started_at === null && $wire.currentTournamentData.ended_at === null) {
                            this.elapsedTime = 0; // If either start or end is not set, elapsed time is 0
                        } else if ($wire.currentTournamentData.started_at !== null && ($wire.currentTournamentData.ended_at === null || new Date() < new Date($wire.currentTournamentData.ended_at))) {
                            const startedAtDate = new Date($wire.currentTournamentData.started_at);
                            this.elapsedTime = Math.floor((new Date() - startedAtDate) / 1000); // Calculate elapsed time in seconds
                        } else {
                            const startedAtDate = new Date($wire.currentTournamentData.started_at);
                            const endedAtDate = new Date($wire.currentTournamentData.ended_at);
                            this.elapsedTime = Math.floor((endedAtDate - startedAtDate) / 1000); // Calculate elapsed time in seconds
                        }
                    },
                    updateElapsedTime: function() {
                        if ($wire.currentTournamentData.started_at !== null && ($wire.currentTournamentData.ended_at === null || new Date() < new Date($wire.currentTournamentData.ended_at) )) {
                            const startedAtDate = new Date($wire.currentTournamentData.started_at);
                            this.intervalId = setInterval(() => {
                                const now = new Date();
                                this.elapsedTime = Math.floor((now - startedAtDate) / 1000); // Calculate elapsed time in seconds
                            }, 1000);
                        }
                    }
                }
            " x-init="
                (() => {
                    calculateElapsedTime();
                    updateElapsedTime();

                    $wire.on('match-changed', () => {
                        clearInterval(intervalId); // Clear existing interval
                        calculateElapsedTime();
                        updateElapsedTime();
                    });
                })()
            ">
                <p class="font-medium text-xl">
                    {{ $currentTournamentData->round }}. forduló <span x-cloak x-show="elapsedTime !== 0"> - <span
                            x-text="Math.floor(elapsedTime / 3600)"></span>:<span
                            x-text="Math.floor((elapsedTime % 3600) / 60)"></span>:<span
                            x-text="elapsedTime % 60"></span></span>
                </p>
                @if($currentTournamentData->is_stakeless)
                    <x-filament::badge color="success">
                        Tét nélküli
                    </x-filament::badge>
                @endif
                @if($currentTournamentData->is_final)
                    <x-filament::badge color="danger">
                        Döntő
                    </x-filament::badge>
                @endif
            </div>
            <div class="mb-6 flex justify-center gap-4">
                <p class="text-center text-sm text-gray-400 dark:text-gray-500">Meccs
                    kezdete: {{ $currentTournamentData->started_at !== null ? Carbon::parse($currentTournamentData->started_at)->format('H:i:s') : '-' }}</p>
                <p class="text-center text-sm text-gray-400 dark:text-gray-500">Meccs
                    vége: {{ $currentTournamentData->ended_at !== null ? Carbon::parse($currentTournamentData->ended_at)->format('H:i:s') : '-' }}</p>
            </div>
            <div class="grid grid-cols-2">
                <div>
                    <div class="flex gap-2 justify-center">
                        <p>Hazai csapat:</p>
                        <x-filament::badge color="info">{{ $currentTournamentData->home_team_name }}</x-filament::badge>
                    </div>
                    <p class="text-center">Pontszám: <span
                            class="font-bold">{{ $currentTournamentData->home_team_score ?? 0 }}</span></p>
                </div>
                <div>
                    <div class="flex gap-2 justify-center">
                        <p>Vendég csapat:</p>
                        <x-filament::badge
                            color="danger">{{ $currentTournamentData->away_team_name }}</x-filament::badge>
                    </div>
                    <p class="text-center">Pontszám: <span
                            class="font-bold">{{ $currentTournamentData->away_team_score ?? 0 }}</span></p>
                </div>
            </div>
        @else
            <p>Úgy néz ki még nincsenek meccsek a választott versenyben!</p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
