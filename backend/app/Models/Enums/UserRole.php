<?php

namespace App\Models\Enums;

enum UserRole: string
{
    case MANAGER = 'manager';
    case ADMIN = 'admin';
}
