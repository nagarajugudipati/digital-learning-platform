<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotQA extends Model
{
    protected $table = 'chatbot_qa';

    protected $fillable = ['question', 'answer', 'keywords', 'created_by'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Keywords as a trimmed array, empty strings removed. */
    public function getKeywordsArrayAttribute(): array
    {
        if (!$this->keywords) return [];
        return array_filter(array_map('trim', explode(',', strtolower($this->keywords))));
    }

    /**
     * Score how well this Q&A matches a normalized input string.
     * Returns 0.0–1.0.
     */
    public function matchScore(string $normalized): float
    {
        // Exact substring match on question → high confidence
        if (str_contains(strtolower($this->question), $normalized)) return 0.95;

        // Keyword match
        $words = array_filter(explode(' ', $normalized), fn ($w) => strlen($w) > 2);
        $kwds  = $this->keywords_array;

        if (empty($kwds) || empty($words)) return 0.0;

        $hits = 0;
        foreach ($kwds as $kw) {
            foreach ($words as $word) {
                if (str_contains($kw, $word) || str_contains($word, $kw)) {
                    $hits++;
                    break;
                }
            }
        }

        return $hits > 0 ? min($hits / count($kwds), 1.0) : 0.0;
    }
}
