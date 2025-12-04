<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@email.com',
            'admin' => true,
            'password' => Hash::make('admin'),
        ]);

        User::factory()->create([
            'name' => 'User',
            'email' => 'user@email.com',
            'admin' => false,
            'password' => Hash::make('user'),
        ]);

        // User::factory(50)->create();
        User::factory(300)->create();
    }
}
