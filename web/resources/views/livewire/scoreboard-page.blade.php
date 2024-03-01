@php use App\Models\Enums\TournamentMatchWinner;use Illuminate\Support\Carbon; @endphp
<div class="h-screen flex flex-col p-4" wire:poll.3s="refreshCurrentTournamentMatchData">
    @if($currentTournamentMatchData === null)
        <div class="flex justify-center items-center h-full">
            <h1 class="text-4xl text-danger-600 dark:text-danger-400 text-center">Úgy néz ki, hogy nincs folyamatban
                levő meccs. Nézz vissza később!</h1>
        </div>
    @else
        <div class="flex justify-center pt-8 text-5xl" x-data="
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
        <div class="w-full flex-1 gap-6 grid grid-cols-2">
            <div class="flex flex-col h-full justify-center items-center">
                <p class="text-center text-3xl mb-2 text-gray-400 dark:text-gray-500">Hazai csapat:</p>
                <p class="text-5xl font-medium text-center mb-4">{{ $currentTournamentMatchData->home_team_name }} @if ($currentTournamentMatchData->winner === TournamentMatchWinner::HOME_TEAM)
                        <span>🏆</span>
                    @endif</p>
                <p class="font-bold text-7xl">{{ $currentTournamentMatchData->home_team_score }}</p>
            </div>
            <div class="flex flex-col h-full justify-center items-center">
                <p class="text-center text-3xl mb-2 text-gray-400 dark:text-gray-500">Vendég csapat:</p>
                <p class="text-5xl font-medium text-center mb-4">{{ $currentTournamentMatchData->away_team_name }} @if ($currentTournamentMatchData->winner === TournamentMatchWinner::AWAY_TEAM)
                        <span>🏆</span>
                    @endif</p>
                <p class="font-bold text-7xl">{{ $currentTournamentMatchData->away_team_score }}</p>
            </div>
        </div>
    @endif
</div>

@script
<script>
    Echo.channel('scoreboard.' + $wire.tournamentCode)
        .listen('CurrentMatchUpdatedEvent', (e) => {
            $wire.updateCurrentTournamentMatchData(e.matchData);
        });
</script>
@endscript
