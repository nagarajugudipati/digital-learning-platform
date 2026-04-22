<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'message', 'response', 'intent',
        'subject', 'confidence', 'was_helpful', 'session_id',
    ];

    protected $casts = [
        'confidence' => 'decimal:4',
        'was_helpful' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
