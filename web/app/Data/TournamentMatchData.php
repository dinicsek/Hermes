<?php

namespace App\Data;

use App\Models\Enums\TournamentMatchWinner;
use DateTime;
use Livewire\Wireable;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Concerns\WireableData;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class TournamentMatchData extends Data implements Wireable
{
    use WireableData;

    public function __construct(
        public int                    $id,
        public string                 $home_team_name,
        public string                 $away_team_name,
        public int                    $home_team_id,
        public int                    $away_team_id,
        public ?int                   $home_team_score,
        public ?int                   $away_team_score,
        #[WithCast(DateTimeInterfaceCast::class)]
        #[WithTransformer(DateTimeInterfaceTransformer::class)]
        public ?DateTime              $started_at,
        #[WithCast(DateTimeInterfaceCast::class)]
        #[WithTransformer(DateTimeInterfaceTransformer::class)]
        public ?DateTime              $ended_at,
        public int                    $round,
        public ?int                   $sort,
        public bool                   $is_stakeless,
        public int                    $tournament_id,
        public string                 $tournament_name,
        public string                 $tournament_code,
        public bool                   $is_final,
        public ?TournamentMatchWinner $winner,
    )
    {
    }
}
