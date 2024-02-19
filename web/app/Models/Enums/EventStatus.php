<?php

namespace App\Models\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EventStatus: string implements HasLabel, HasColor
{
    case UPCOMING = 'upcoming';
    case ONGOING = 'ongoing';
    case CONCLUDED = 'concluded';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::UPCOMING => 'primary',
            self::ONGOING => 'success',
            self::CONCLUDED => 'danger',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::UPCOMING => 'Jövőbeli',
            self::ONGOING => 'Folyamatban',
            self::CONCLUDED => 'Lezárult',
        };
    }
}
