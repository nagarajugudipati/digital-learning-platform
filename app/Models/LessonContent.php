<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LessonContent extends Model
{
    protected $fillable = ['lesson_id', 'title', 'type', 'file_path', 'content_text', 'order'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function getFileUrlAttribute(): ?string
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            return asset('storage/' . $this->file_path);
        }
        return null;
    }

    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'video' => '🎥',
            'pdf'   => '📄',
            'image' => '🖼️',
            default => '📝',
        };
    }
}
