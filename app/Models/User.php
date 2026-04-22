<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'class_level', 'subject_specialization',
        'phone', 'school', 'is_active', 'avatar',
        'status', 'streak_count', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'last_login_at'     => 'datetime',
            'streak_count'      => 'integer',
        ];
    }

    public function isAdmin(): bool   { return $this->role === 'admin'; }
    public function isTeacher(): bool { return $this->role === 'teacher'; }
    public function isStudent(): bool { return $this->role === 'student'; }

    public function isApprovedTeacher(): bool
    {
        if ($this->role !== 'teacher') return true;
        // null status = legacy/seeded teachers treated as approved
        return $this->status === null || $this->status === 'approved';
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'teacher_id');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'teacher_id');
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class, 'student_id');
    }

    public function progressReports()
    {
        return $this->hasMany(ProgressReport::class, 'student_id');
    }

    public function chatbotLogs()
    {
        return $this->hasMany(ChatbotLog::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments')->withTimestamps();
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return "https://ui-avatars.com/api/?name={$this->name}&background=4F46E5&color=fff&size=128";
    }

    public function getTotalQuizScoreAttribute(): float
    {
        $attempts = $this->quizAttempts()->where('status', 'completed')->get();
        if ($attempts->isEmpty()) return 0;
        return round($attempts->avg('percentage'), 1);
    }

    public function getLessonsCompletedAttribute(): int
    {
        return $this->progressReports()->where('is_completed', true)->count();
    }

    public function updateStreak(): void
    {
        $now = now();

        if (!$this->last_login_at) {
            // First ever login
            $this->update(['streak_count' => 1, 'last_login_at' => $now]);
            return;
        }

        $lastLogin = $this->last_login_at;

        if ($lastLogin->isToday()) {
            // Already logged in today — just update timestamp
            return;
        }

        if ($lastLogin->isYesterday()) {
            // Consecutive day — increment streak
            $this->update(['streak_count' => $this->streak_count + 1, 'last_login_at' => $now]);
        } else {
            // Streak broken — reset to 1
            $this->update(['streak_count' => 1, 'last_login_at' => $now]);
        }
    }
}
