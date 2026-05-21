<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Query extends Model
{
    protected $fillable = [
        'student_id', 'teacher_id', 'subject', 'message',
        'reply', 'replied_by', 'status', 'replied_at',
    ];

    protected $casts = ['replied_at' => 'datetime'];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function repliedBy()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
