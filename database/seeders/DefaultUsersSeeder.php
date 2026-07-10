<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Default Students
        Student::create([
            'account_id' => 'CLR-2026-00001',
            'student_id' => '2023-00001',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => 'user@student.com',
            'course' => 'BSIT',
            'password' => Hash::make('12345678'),
        ]);

        Student::create([
            'account_id' => 'CLR-2026-00002',
            'student_id' => '2023-00002',
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'email' => 'maria@student.com',
            'course' => 'BSCS',
            'password' => Hash::make('password123'),
        ]);

        // Default Departments (Staff)
        Department::create([
            'name' => 'Library',
            'description' => 'Library Clearance',
            'staff_email' => 'library@school.com',
            'staff_password' => Hash::make('12345678'),
            'is_active' => true,
        ]);

        Department::create([
            'name' => 'Registrar',
            'description' => 'Registrar Office',
            'staff_email' => 'registrar@school.com',
            'staff_password' => Hash::make('registrar123'),
            'is_active' => true,
        ]);

        Department::create([
            'name' => 'Accounting',
            'description' => 'Accounting Office',
            'staff_email' => 'accounting@school.com',
            'staff_password' => Hash::make('accounting123'),
            'is_active' => true,
        ]);

        Department::create([
            'name' => 'Dean\'s Office',
            'description' => 'College Dean',
            'staff_email' => 'dean@school.com',
            'staff_password' => Hash::make('dean123'),
            'is_active' => true,
        ]);

        $this->command->info('Default users created successfully!');
    }
}