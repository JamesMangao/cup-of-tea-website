<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@cupoftea.com'], [
            'name' => 'Admin',
            'password' => 'admin123',
            'role' => 'admin',
            'is_admin' => true,
        ]);

        User::updateOrCreate(['email' => 'user@cupoftea.com'], [
            'name' => 'User',
            'password' => 'user123',
            'role' => 'viewer',
            'is_admin' => false,
        ]);

    }
}