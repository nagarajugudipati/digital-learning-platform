<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'quiz_id', 'score', 'total_marks', 'percentage',
        'answers', 'status', 'passed', 'time_taken', 'attempt_number',
        'started_at', 'completed_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'passed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'percentage' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function getTimeTakenFormattedAttribute(): string
    {
        if (!$this->time_taken) return 'N/A';
        $minutes = floor($this->time_taken / 60);
        $seconds = $this->time_taken % 60;
        return "{$minutes}m {$seconds}s";
    }

    public function getGradeAttribute(): string
    {
        return match(true) {
            $this->percentage >= 90 => 'A+',
            $this->percentage >= 80 => 'A',
            $this->percentage >= 70 => 'B',
            $this->percentage >= 60 => 'C',
            $this->percentage >= 40 => 'D',
            default => 'F',
        };
    }

    public function calculateAndSave(array $submittedAnswers, Quiz $quiz): void
    {
        $questions = $quiz->questions;
        $score = 0;
        $totalMarks = $quiz->total_marks ?: $questions->sum('marks');
        $answers = [];

        foreach ($questions as $question) {
            $given = $submittedAnswers[$question->id] ?? null;
            $correct = $question->isCorrect($given ?? '');
            $answers[$question->id] = [
                'given' => $given,
                'correct' => $question->correct_answer,
                'is_correct' => $correct,
                'marks_earned' => $correct ? $question->marks : 0,
            ];
            if ($correct) {
                $score += $question->marks;
            }
        }

        $percentage = $totalMarks > 0 ? round(($score / $totalMarks) * 100, 2) : 0;

        $this->update([
            'score' => $score,
            'total_marks' => $totalMarks,
            'percentage' => $percentage,
            'answers' => $answers,
            'status' => 'completed',
            'passed' => $percentage >= $quiz->passing_marks,
            'completed_at' => now(),
            'time_taken' => now()->diffInSeconds($this->started_at),
        ]);
    }
}
