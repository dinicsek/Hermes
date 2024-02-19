<?php

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;

enum RoundMode: string implements HasLabel
{
    case ELIMINATION = 'elimination';
    case GROUP = 'group';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ELIMINATION => 'Kieséses',
            self::GROUP => 'Csoportos',
        };
    }
}
