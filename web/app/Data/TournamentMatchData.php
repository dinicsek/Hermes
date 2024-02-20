<?php

namespace App\Data;

use App\Models\Enums\TournamentMatchWinner;
use Livewire\Wireable;
use Spatie\LaravelData\Concerns\WireableData;
use Spatie\LaravelData\Data;

class TournamentMatchData extends Data implements Wireable
{
    use WireableData;

    public function __construct(
        public string                 $home_team_name,
        public string                 $away_team_name,
        public int                    $home_team_id,
        public int                    $away_team_id,
        public ?int                   $home_team_score,
        public ?int                   $away_team_score,
        public ?string                $started_at,
        public ?string                $ended_at,
        public int                    $round,
        public ?int                   $sort,
        public bool                   $is_stakeless,
        public int                    $tournament_id,
        public string                 $tournament_name,
        public bool                   $is_final,
        public ?TournamentMatchWinner $winner,
    )
    {
    }
}
