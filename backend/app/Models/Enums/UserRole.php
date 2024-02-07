<?php

namespace App\Models\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasLabel, HasColor
{
    case MANAGER = 'manager';
    case ADMIN = 'admin';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::MANAGER => 'primary',
            self::ADMIN => 'danger',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MANAGER => 'Menedzser',
            self::ADMIN => 'Adminisztrátor',
        };
    }
}
