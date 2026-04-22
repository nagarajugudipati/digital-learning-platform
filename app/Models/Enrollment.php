<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = ['user_id', 'course_id', 'enrolled_at', 'payment_status', 'amount_paid', 'transaction_id'];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'amount_paid' => 'decimal:2',
    ];

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
