<?php

namespace Database\Seeders;

use App\Models\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductionDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admins = User::whereRole(UserRole::ADMIN)->first();

        if ($admins === null) {
            User::create([
                'name' => 'Admin',
                'role' => UserRole::ADMIN,
                'email' => config('app.admin_email'),
                'password' => config('app.admin_password')
            ]);
        }
    }
}
