<?php

namespace App\Models\Enums;

enum TournamentMatchWinner: string
{
    case HOME_TEAM = 'home';
    case AWAY_TEAM = 'away';
}
