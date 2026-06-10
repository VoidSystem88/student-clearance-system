<?php
// database/seeders/ReminderSeeder.php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReminderSeeder extends Seeder
{
    public function run()
    {
        // Get all students
        $students = User::where('role', 'student')->get();
        
        // Reminder about new AI feature
        foreach ($students as $student) {
            Notification::create([
                'type' => 'new_feature',
                'title' => '🤖 Meet Void AI Assistant!',
                'message' => 'Your new AI clearance assistant is now live! Ask questions, get instant answers, and even teach it new things. Click the robot icon on your screen to start chatting!',
                'data' => json_encode(['icon' => 'fa-robot', 'color' => 'purple']),
                'user_id' => $student->id,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        // Optional: Add clearance deadline reminders
        foreach ($students as $student) {
            Notification::create([
                'type' => 'reminder',
                'title' => '📋 Clearance Deadline Approaching',
                'message' => 'Reminder: The clearance submission deadline is in 7 days. Please complete all pending requirements.',
                'data' => json_encode(['icon' => 'fa-clock', 'color' => 'yellow']),
                'user_id' => $student->id,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}