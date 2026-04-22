<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;

class LessonPolicy
{
    public function update(User $user, Lesson $lesson): bool
    {
        return $user->id === $lesson->teacher_id || $user->isAdmin();
    }

    public function delete(User $user, Lesson $lesson): bool
    {
        return $user->id === $lesson->teacher_id || $user->isAdmin();
    }
}
