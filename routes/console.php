<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('nabha:stats', function () {
    $users = \App\Models\User::count();
    $lessons = \App\Models\Lesson::count();
    $quizzes = \App\Models\Quiz::count();
    $this->info("Nabha Digital Learning Stats:");
    $this->line("Users: {$users} | Lessons: {$lessons} | Quizzes: {$quizzes}");
})->purpose('Show platform statistics');
