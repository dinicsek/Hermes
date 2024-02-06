<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test Manager',
            'email' => 'manager@test.test',
            'role' => 'manager',
        ]);

        User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@test.test',
            'role' => 'admin',
        ]);
    }
}
