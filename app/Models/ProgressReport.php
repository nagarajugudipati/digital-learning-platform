<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'lesson_id', 'completion_percentage',
        'is_completed', 'is_downloaded', 'time_spent',
        'views', 'last_accessed', 'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'is_downloaded' => 'boolean',
        'completion_percentage' => 'decimal:2',
        'last_accessed' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public static function trackView(int $studentId, int $lessonId): self
    {
        $report = self::firstOrCreate(
            ['student_id' => $studentId, 'lesson_id' => $lessonId],
            ['completion_percentage' => 0, 'views' => 0]
        );
        $report->increment('views');
        $report->update(['last_accessed' => now()]);
        return $report;
    }

    public function markCompleted(): void
    {
        $this->update([
            'is_completed' => true,
            'completion_percentage' => 100,
            'completed_at' => now(),
        ]);
    }
}
