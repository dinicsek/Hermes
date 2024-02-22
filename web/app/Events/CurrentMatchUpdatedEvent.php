<?php

namespace App\Events;

use App\Data\TournamentMatchData;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class CurrentMatchUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(
        public TournamentMatchData $matchData
    )
    {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('scoreboard.' . $this->matchData->tournament_code),
        ];
    }
}
