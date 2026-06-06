<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'name' => 'System Administrator',
            'password' => 'admin123', // Change this in production!
            'role' => 'admin',
            'phone_number' => '09123456789',
            'is_active' => true,
        ]);
    }
}