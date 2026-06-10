<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@tcc.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Staff
        User::create([
            'name' => 'Staff Member',
            'email' => 'staff@tcc.com',
            'password' => Hash::make('12345678'),
            'role' => 'staff',
            'department_id' => '1',
            'is_active' => true,
        ]);

        // Student
        User::create([
            'name' => 'Student User',
            'email' => 'student@tcc.com',
            'password' => Hash::make('12345678'),
            'role' => 'student',
            'student_id' => '2023-00001',
            'account_id' => 'CLR-2026-00001',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'course' => 'BSIT',
            'is_active' => true,
        ]);
    }
}