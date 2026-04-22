<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\ProgressReport;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $courseId  = $request->input('course_id');
        $studentId = $request->input('student_id');
        $dateFrom  = $request->input('date_from');
        $dateTo    = $request->input('date_to');
        $search    = $request->input('search');

        // Resolve student IDs from course / date filters first
        $filteredIds = null;

        if ($courseId) {
            $q = Enrollment::where('course_id', $courseId);
            if ($dateFrom) $q->whereDate('enrolled_at', '>=', $dateFrom);
            if ($dateTo)   $q->whereDate('enrolled_at', '<=', $dateTo);
            $filteredIds = $q->pluck('user_id')->unique()->values();
        } elseif ($dateFrom || $dateTo) {
            $q = Enrollment::query();
            if ($dateFrom) $q->whereDate('enrolled_at', '>=', $dateFrom);
            if ($dateTo)   $q->whereDate('enrolled_at', '<=', $dateTo);
            $filteredIds = $q->pluck('user_id')->unique()->values();
        }

        // Build student query (DB-level pagination + search)
        $studentQuery = User::where('role', 'student')
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            }))
            ->when($studentId, fn ($q) => $q->where('id', $studentId))
            ->when($filteredIds !== null, fn ($q) => $q->whereIn('id', $filteredIds))
            ->orderBy('name');

        $students = $studentQuery->paginate(20)->withQueryString();
        $pageIds  = $students->pluck('id');

        // Stats only for current page
        $totalLessons = Lesson::where('status', 'published')->count();

        $enrollmentMap = Enrollment::whereIn('user_id', $pageIds)
            ->select('user_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('user_id')
            ->pluck('cnt', 'user_id');

        $completedMap = ProgressReport::whereIn('student_id', $pageIds)
            ->where('is_completed', true)
            ->select('student_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('student_id')
            ->pluck('cnt', 'student_id');

        $quizAttemptQuery = QuizAttempt::whereIn('student_id', $pageIds)
            ->where('status', 'completed');
        if ($dateFrom) $quizAttemptQuery->whereDate('completed_at', '>=', $dateFrom);
        if ($dateTo)   $quizAttemptQuery->whereDate('completed_at', '<=', $dateTo);

        $quizStats = $quizAttemptQuery
            ->select('student_id',
                DB::raw('COUNT(*) as quiz_count'),
                DB::raw('AVG(percentage) as avg_score')
            )
            ->groupBy('student_id')
            ->get()->keyBy('student_id');

        // Map page into display rows
        $report = $students->getCollection()->map(function ($student) use (
            $enrollmentMap, $completedMap, $quizStats, $totalLessons
        ) {
            $completed = $completedMap[$student->id] ?? 0;
            $qStat     = $quizStats[$student->id] ?? null;
            $progress  = $totalLessons > 0 ? round(($completed / $totalLessons) * 100, 1) : 0;

            return [
                'id'               => $student->id,
                'name'             => $student->name,
                'email'            => $student->email,
                'class_level'      => $student->class_level ?? 'N/A',
                'enrolled_courses' => $enrollmentMap[$student->id] ?? 0,
                'completed_lessons'=> $completed,
                'quizzes_taken'    => $qStat ? $qStat->quiz_count : 0,
                'avg_score'        => $qStat ? round($qStat->avg_score, 1) : 0,
                'progress_pct'     => $progress,
            ];
        });

        // Summary counts (across all filtered students, not just current page)
        $totalCount = $studentQuery->count();

        $courses     = Course::orderBy('title')->pluck('title', 'id');
        $allStudents = User::where('role', 'student')->orderBy('name')->pluck('name', 'id');

        return view('admin.reports.index', compact(
            'report', 'students', 'courses', 'allStudents',
            'courseId', 'studentId', 'dateFrom', 'dateTo', 'search', 'totalCount'
        ));
    }
}
