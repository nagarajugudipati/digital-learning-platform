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
            'name' => 'Admin User',
            'email' => 'admin@nabha.edu',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'school' => 'Government Senior Secondary School, Nabha',
            'is_active' => true,
        ]);

        // Teachers
        $teachers = [
            ['name' => 'Rajesh Kumar', 'email' => 'teacher@nabha.edu', 'subject' => 'Mathematics'],
            ['name' => 'Priya Sharma', 'email' => 'priya@nabha.edu', 'subject' => 'Science'],
            ['name' => 'Gurpreet Singh', 'email' => 'gurpreet@nabha.edu', 'subject' => 'English'],
            ['name' => 'Sunita Devi', 'email' => 'sunita@nabha.edu', 'subject' => 'Hindi'],
            ['name' => 'Amrit Kaur', 'email' => 'amrit@nabha.edu', 'subject' => 'Social Studies'],
        ];

        foreach ($teachers as $teacher) {
            User::create([
                'name' => $teacher['name'],
                'email' => $teacher['email'],
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'subject_specialization' => $teacher['subject'],
                'school' => 'Government Senior Secondary School, Nabha',
                'is_active' => true,
            ]);
        }

        // Students
        $students = [
            ['name' => 'Arjun Singh', 'email' => 'student@nabha.edu', 'class' => 'Class 9'],
            ['name' => 'Riya Verma', 'email' => 'riya@nabha.edu', 'class' => 'Class 8'],
            ['name' => 'Harpreet Kaur', 'email' => 'harpreet@nabha.edu', 'class' => 'Class 10'],
            ['name' => 'Mandeep Kumar', 'email' => 'mandeep@nabha.edu', 'class' => 'Class 7'],
            ['name' => 'Pooja Rani', 'email' => 'pooja@nabha.edu', 'class' => 'Class 6'],
            ['name' => 'Vikram Patel', 'email' => 'vikram@nabha.edu', 'class' => 'Class 9'],
            ['name' => 'Simran Bajwa', 'email' => 'simran@nabha.edu', 'class' => 'Class 10'],
            ['name' => 'Rahul Sharma', 'email' => 'rahul@nabha.edu', 'class' => 'Class 8'],
        ];

        foreach ($students as $student) {
            User::create([
                'name' => $student['name'],
                'email' => $student['email'],
                'password' => Hash::make('password'),
                'role' => 'student',
                'class_level' => $student['class'],
                'school' => 'Government Senior Secondary School, Nabha',
                'is_active' => true,
            ]);
        }

        $this->command->info('✅ Users seeded: 1 admin, 5 teachers, 8 students');
    }
}
