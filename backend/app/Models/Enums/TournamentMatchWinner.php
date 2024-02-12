<?php

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;

enum TournamentMatchWinner: string implements HasLabel
{
    case HOME_TEAM = 'home';
    case AWAY_TEAM = 'away';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::HOME_TEAM => 'Hazai csapat',
            self::AWAY_TEAM => 'Vendég csapat',
        };
    }
}
