<?php

namespace App\Models\Enums;

enum UserRole : string
{
    case Manager = 'manager';
    case Admin = 'admin';
}
