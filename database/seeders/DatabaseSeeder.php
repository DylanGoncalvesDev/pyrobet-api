<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       User::create([
            'name'     => 'player123',
            'email'    => 'player@pyrobet.com',
            'password' => Hash::make('password123'),
            'role'     => 'user',
        ]);

       User::create([
            'name'     => 'Admin PyroBet',
            'email'    => 'admin@pyrobet.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);
    }
}
