<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotLog;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        // period = number of months to show in trend charts (3 / 6 / 12)
        $period = in_array((int) $request->period, [3, 6, 12])
            ? (int) $request->period
            : 6;

        // Platform-wide counts — cached for 5 minutes, invalidated on demand
        $stats = Cache::remember('admin.dashboard.stats', 300, function () {
            return [
                'total_students'    => User::where('role', 'student')->count(),
                'total_teachers'    => User::where('role', 'teacher')->count(),
                'total_lessons'     => Lesson::count(),
                'pending_lessons'   => Lesson::where('status', 'pending')->count(),
                'total_quizzes'     => Quiz::count(),
                'total_attempts'    => QuizAttempt::where('status', 'completed')->count(),
                'chatbot_queries'   => ChatbotLog::count(),
                'active_users'      => User::where('is_active', true)->count(),
                'total_courses'     => Course::count(),
                'published_courses' => Course::where('status', 'published')->count(),
                'pending_courses'   => Course::where('status', 'pending')->count(),
                'total_enrollments' => Enrollment::count(),
                'total_revenue'     => (float) Enrollment::where('payment_status', 'paid')->sum('amount_paid'),
            ];
        });

        $recentUsers   = User::latest()->take(5)->get();
        $recentLessons = Lesson::with('teacher')->latest()->take(5)->get();
        $recentCourses = Course::with('teacher')->withCount('enrollments')->latest()->take(5)->get();

        // Trend data — NOT cached so it's always fresh for the chosen period
        $monthlyRegistrations = [];
        $monthlyRevenue       = [];
        $roleDistribution     = [
            'Students' => $stats['total_students'],
            'Teachers' => $stats['total_teachers'],
            'Admins'   => User::where('role', 'admin')->count(),
        ];

        for ($i = $period - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $label = $month->format('M y');

            $monthlyRegistrations[$label] = User::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $monthlyRevenue[$label] = (float) Enrollment::where('payment_status', 'paid')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount_paid');
        }

        return view('admin.dashboard', compact(
            'stats', 'recentUsers', 'recentLessons', 'recentCourses',
            'monthlyRegistrations', 'monthlyRevenue', 'roleDistribution', 'period'
        ));
    }
}
