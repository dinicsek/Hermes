<?php

namespace App\Models\Enums;

enum TournamentMatchStatus: string
{
    case UPCOMING = 'upcoming';
    case ONGOING = 'ongoing';
    case CONCLUDED = 'concluded';
}
