@php use App\Models\Enums\EventStatus; @endphp
@php use App\Models\Enums\TournamentMatchWinner;use Illuminate\Support\Carbon; @endphp
<div>
    <x-navigation/>
    <div class="px-4 pb-4">
        <h1 class="text-3xl mt-2">{{ $tournamentData->name }}</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-2 mb-5">{{ $tournamentData->description }}</p>
        @if($currentTournamentMatchData === null)
            <div class="flex justify-center items-center mb-4">
                <h1 class="text-xl text-danger-600 dark:text-danger-400 text-center">Úgy néz ki, hogy nincs folyamatban
                    levő meccs.</h1>
            </div>
        @else
            <x-filament::section class="mb-4" collapsible="true">
                <x-slot name="heading">Folyamatban lévő meccs</x-slot>
                <div class="flex justify-center pt-4 text-2xl" x-data="
                {
                    elapsedTime: 0,
                    intervalId: null,
                    calculateElapsedTime: function() {
                        if ($wire.currentTournamentMatchData.started_at === null && $wire.currentTournamentMatchData.ended_at === null) {
                            this.elapsedTime = 0; // If either start or end is not set, elapsed time is 0
                        } else if ($wire.currentTournamentMatchData.started_at !== null && ($wire.currentTournamentMatchData.ended_at === null || new Date() < new Date($wire.currentTournamentMatchData.ended_at))) {
                            const startedAtDate = new Date($wire.currentTournamentMatchData.started_at);
                            this.elapsedTime = Math.floor((new Date() - startedAtDate) / 1000); // Calculate elapsed time in seconds
                        } else {
                            const startedAtDate = new Date($wire.currentTournamentMatchData.started_at);
                            const endedAtDate = new Date($wire.currentTournamentMatchData.ended_at);
                            this.elapsedTime = Math.floor((endedAtDate - startedAtDate) / 1000); // Calculate elapsed time in seconds
                        }
                    },
                    updateElapsedTime: function() {
                        if ($wire.currentTournamentMatchData.started_at !== null && ($wire.currentTournamentMatchData.ended_at === null || new Date() < new Date($wire.currentTournamentMatchData.ended_at) )) {
                            const startedAtDate = new Date($wire.currentTournamentMatchData.started_at);
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
                        if ($wire.currentTournamentMatchData.started_at !== null && ($wire.currentTournamentMatchData.ended_at === null || new Date() < new Date($wire.currentTournamentMatchData.ended_at) )) {
                            updateElapsedTime();
                        }
                    });
                })()
            ">
                    <p class="font-medium">
                        {{ $currentTournamentMatchData->round }}. forduló
                        <span x-cloak x-show="elapsedTime !== 0">
                        <span x-show="elapsedTime >= 0">
                            -
                            <span x-text="Math.floor(elapsedTime / 3600).toString().padStart(2, '0')"></span>:<span
                                x-text="Math.floor((elapsedTime % 3600) / 60).toString().padStart(2, '0')"></span>:<span
                                x-text="(elapsedTime % 60).toString().padStart(2, '0')"></span>
                        </span>
                        <span x-show="elapsedTime < 0">
                            - Negatív idő
                        </span>
                    </span>
                        @if($currentTournamentMatchData->is_stakeless)
                            <span>
                    - Tét nélküli
                </span>
                        @endif
                        @if($currentTournamentMatchData->is_final)
                            <span>
                    - Döntő
                </span>
                        @endif
                    </p>

                </div>
                <div class="flex flex-col gap-4 my-4">
                    <p class="text-center"><span class="text-center text-xl mb-2 text-gray-400 dark:text-gray-500">Hazai csapat:</span><br/><span
                            class="text-xl font-medium text-center mb-4">{{ $currentTournamentMatchData->home_team_name }} @if ($currentTournamentMatchData->winner === TournamentMatchWinner::HOME_TEAM)
                                <span>🏆</span>
                            @endif</span><span
                            class="font-bold text-xl">{{ ' - ' . $currentTournamentMatchData->home_team_score }}</span>
                    </p>
                    <p class="text-center"><span class="text-center text-xl mb-2 text-gray-400 dark:text-gray-500">Vendég csapat:</span><br/><span
                            class="text-xl font-medium text-center mb-4">{{ $currentTournamentMatchData->away_team_name }} @if ($currentTournamentMatchData->winner === TournamentMatchWinner::AWAY_TEAM)
                                <span>🏆</span>
                            @endif</span><span
                            class="font-bold text-xl">{{ ' - ' . $currentTournamentMatchData->away_team_score }}</span>
                    </p>
                </div>
            </x-filament::section>
        @endif
        <div class="flex w-full justify-center mb-4">
            <x-filament::tabs>
                <x-filament::tabs.item :active="$activeTab === 'matches'" wire:click="setActiveTab('matches')">
                    Meccsek
                </x-filament::tabs.item>
                <x-filament::tabs.item :active="$activeTab === 'teams'" wire:click="setActiveTab('teams')">
                    Csapatok
                </x-filament::tabs.item>
            </x-filament::tabs>
        </div>
        @if ($activeTab === 'matches')
            {{ $this->table }}
        @else
            <livewire:ongoing-teams-table :tournament-id="$tournamentId"/>
        @endif
    </div>
</div>

@script
<script>
    Echo.channel('scoreboard.' + $wire.tournamentCode)
        .listen('CurrentMatchUpdatedEvent', (e) => {
            $wire.updateCurrentTournamentMatchData(e.matchData);
        });
</script>
@endscript
