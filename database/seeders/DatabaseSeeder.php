<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(EquipmentSeeder::class);

        User::query()->updateOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'role' => 'user',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::query()->updateOrCreate([
            'email' => 'engineer@example.com',
        ], [
            'name' => 'Solar Engineer',
            'role' => 'engineer',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::query()->updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Solar Admin',
            'role' => 'admin',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
