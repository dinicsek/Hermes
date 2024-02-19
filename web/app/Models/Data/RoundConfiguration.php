<?php

namespace App\Models\Data;

use App\Models\Enums\RoundMode;
use Spatie\LaravelData\Data;

class RoundConfiguration extends Data
{
    public function __construct(
        public int       $round,
        public RoundMode $mode,
        public ?int      $groupCount,
        public ?int      $advancingCount,
        public ?int      $eliminationLevels,
    )
    {
    }
}
