<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Str;

class EnrollmentService
{
    public function isEnrolled(User $user, Course $course): bool
    {
        return $course->enrollments()->where('user_id', $user->id)->exists();
    }

    public function enrollFree(User $user, Course $course): Enrollment
    {
        return Enrollment::create([
            'user_id'        => $user->id,
            'course_id'      => $course->id,
            'enrolled_at'    => now(),
            'payment_status' => 'free',
            'amount_paid'    => 0,
        ]);
    }

    /**
     * Record a paid enrollment. Card validation is the controller's responsibility.
     */
    public function enrollPaid(User $user, Course $course): Enrollment
    {
        return Enrollment::create([
            'user_id'        => $user->id,
            'course_id'      => $course->id,
            'enrolled_at'    => now(),
            'payment_status' => 'paid',
            'amount_paid'    => $course->price,
            'transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
        ]);
    }
}
