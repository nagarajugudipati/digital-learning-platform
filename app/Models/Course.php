<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teacher_id', 'title', 'description', 'price', 'thumbnail',
        'subject', 'class_level', 'status', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'price'       => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail && Storage::disk('public')->exists($this->thumbnail)) {
            return asset('storage/' . $this->thumbnail);
        }
        return asset('images/default-course.svg');
    }

    public function getTotalDurationAttribute(): string
    {
        $minutes = $this->lessons->sum('duration_minutes');
        if ($minutes <= 0) return 'Self-paced';
        if ($minutes < 60) return $minutes . ' min';
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        return $m > 0 ? "{$h}h {$m}m" : "{$h}h";
    }

    /**
     * Deterministic mock rating seeded from course ID (3.5 – 5.0).
     */
    public function getMockRatingAttribute(): float
    {
        return round(min(5.0, 3.5 + ($this->id % 31) * 0.05), 1);
    }

    /**
     * Deterministic mock review count seeded from course ID (15 – 99).
     */
    public function getMockReviewCountAttribute(): int
    {
        return 15 + ($this->id * 13) % 85;
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isFree(): bool
    {
        return (float) $this->price === 0.0;
    }

    public function isEnrolled(int $userId): bool
    {
        return $this->enrollments()->where('user_id', $userId)->exists();
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Return the lesson this student last accessed, or null if none.
     */
    public function lastAccessedLesson(int $userId): ?Lesson
    {
        $lessonIds = $this->lessons()->pluck('id');

        $report = ProgressReport::whereIn('lesson_id', $lessonIds)
            ->where('student_id', $userId)
            ->whereNotNull('last_accessed')
            ->orderByDesc('last_accessed')
            ->first();

        if (!$report) return null;

        return $this->lessons()->find($report->lesson_id);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
