<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('0000'),
            'role' => 'admin',
            'avatar' => null,
        ]);

        // Create test customer
        User::create([
            'name' => 'Customer',
            'email' => 'customer@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'avatar' => null,
        ]);

        // Create additional random customers
        User::factory()
            ->count(20)
            ->sequence(fn ($sequence) => ['role' => 'customer'])
            ->create();
    }
}
