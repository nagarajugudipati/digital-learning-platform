<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teacher_id', 'lesson_id', 'title', 'description',
        'subject', 'class_level', 'time_limit', 'passing_marks',
        'total_marks', 'status', 'max_attempts',
    ];

    protected $casts = [
        'time_limit' => 'integer',
        'passing_marks' => 'integer',
        'total_marks' => 'integer',
        'max_attempts' => 'integer',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function studentAttempts(int $studentId)
    {
        return $this->hasMany(QuizAttempt::class)->where('student_id', $studentId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function recalculateTotalMarks(): void
    {
        $this->update(['total_marks' => $this->questions()->sum('marks')]);
    }

    public function canAttempt(int $studentId): bool
    {
        $attemptCount = $this->attempts()
            ->where('student_id', $studentId)
            ->where('status', 'completed')
            ->count();
        return $attemptCount < $this->max_attempts;
    }
}
