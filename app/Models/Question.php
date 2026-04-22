<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id', 'question_text', 'type',
        'option_a', 'option_b', 'option_c', 'option_d',
        'correct_answer', 'explanation', 'marks', 'order',
    ];

    protected $casts = [
        'marks' => 'integer',
        'order' => 'integer',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function isCorrect(string $answer): bool
    {
        if ($this->type === 'text') {
            return mb_strtolower(trim($answer)) === mb_strtolower(trim($this->correct_answer));
        }
        return strtolower($answer) === strtolower($this->correct_answer);
    }

    public function getOptionAttribute(string $key): ?string
    {
        return match(strtolower($key)) {
            'a' => $this->option_a,
            'b' => $this->option_b,
            'c' => $this->option_c,
            'd' => $this->option_d,
            default => null,
        };
    }

    public function getCorrectAnswerTextAttribute(): string
    {
        if ($this->type === 'text') {
            return $this->correct_answer;
        }
        return $this->getOptionAttribute($this->correct_answer) ?? '';
    }

    public function getOptionsForDisplay(): array
    {
        return match($this->type) {
            'true_false' => ['a' => 'True', 'b' => 'False'],
            'mcq'        => array_filter([
                'a' => $this->option_a,
                'b' => $this->option_b,
                'c' => $this->option_c,
                'd' => $this->option_d,
            ]),
            default => [],
        };
    }
}
