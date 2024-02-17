<?php

namespace App\Data;

use Carbon\Carbon;
use Livewire\Wireable;
use Spatie\LaravelData\Concerns\WireableData;
use Spatie\LaravelData\Data;

class TournamentData extends Data implements Wireable
{
    use WireableData;

    public function __construct(
        public string $name,
        public string $description,
        public Carbon $registration_starts_at,
        public Carbon $registration_ends_at,
        public Carbon $starts_at,
        public int    $min_team_size,
        public int    $max_team_size,
    )
    {
    }
}
