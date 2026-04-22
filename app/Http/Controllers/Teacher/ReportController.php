<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\ProgressReport;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $teacher   = auth()->user();
        $courseId  = $request->input('course_id');
        $studentId = $request->input('student_id');
        $dateFrom  = $request->input('date_from');
        $dateTo    = $request->input('date_to');
        $search    = $request->input('search');

        $myLessonIds = Lesson::where('teacher_id', $teacher->id)->pluck('id');
        $myQuizIds   = Quiz::where('teacher_id', $teacher->id)->pluck('id');
        $myCourseIds = Course::where('teacher_id', $teacher->id)->pluck('id');

        // Union of: enrolled in teacher's courses OR attempted teacher's quizzes
        $enrolledIds  = Enrollment::whereIn('course_id', $myCourseIds)->pluck('user_id');
        $quizIds      = QuizAttempt::whereIn('quiz_id', $myQuizIds)->pluck('student_id');
        $eligibleIds  = $enrolledIds->merge($quizIds)->unique()->values();

        // Apply course filter
        if ($courseId) {
            $byCourse    = Enrollment::where('course_id', $courseId)->pluck('user_id');
            $eligibleIds = $eligibleIds->intersect($byCourse)->values();
        }

        // Apply date filter on enrollment date
        if ($dateFrom || $dateTo) {
            $byDate = Enrollment::whereIn('course_id', $myCourseIds)
                ->when($dateFrom, fn ($q) => $q->whereDate('enrolled_at', '>=', $dateFrom))
                ->when($dateTo,   fn ($q) => $q->whereDate('enrolled_at', '<=', $dateTo))
                ->pluck('user_id');
            $eligibleIds = $eligibleIds->intersect($byDate)->values();
        }

        // DB-level pagination + search
        $studentQuery = User::where('role', 'student')
            ->whereIn('id', $eligibleIds)
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            }))
            ->when($studentId, fn ($q) => $q->where('id', $studentId))
            ->orderBy('name');

        $students = $studentQuery->paginate(20)->withQueryString();
        $pageIds  = $students->pluck('id');

        $totalLessons = $myLessonIds->count();

        $completedMap = ProgressReport::whereIn('lesson_id', $myLessonIds)
            ->whereIn('student_id', $pageIds)
            ->where('is_completed', true)
            ->select('student_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('student_id')
            ->pluck('cnt', 'student_id');

        $quizStats = QuizAttempt::whereIn('quiz_id', $myQuizIds)
            ->whereIn('student_id', $pageIds)
            ->where('status', 'completed')
            ->select('student_id',
                DB::raw('COUNT(*) as quiz_count'),
                DB::raw('AVG(percentage) as avg_score')
            )
            ->groupBy('student_id')
            ->get()->keyBy('student_id');

        $report = $students->getCollection()->map(function ($student) use (
            $completedMap, $quizStats, $totalLessons
        ) {
            $completed = $completedMap[$student->id] ?? 0;
            $qStat     = $quizStats[$student->id] ?? null;
            $progress  = $totalLessons > 0 ? round(($completed / $totalLessons) * 100, 1) : 0;

            return [
                'id'                => $student->id,
                'name'              => $student->name,
                'email'             => $student->email,
                'class_level'       => $student->class_level ?? 'N/A',
                'completed_lessons' => $completed,
                'total_lessons'     => $totalLessons,
                'quizzes_taken'     => $qStat ? $qStat->quiz_count : 0,
                'avg_score'         => $qStat ? round($qStat->avg_score, 1) : 0,
                'progress_pct'      => $progress,
            ];
        });

        $courses              = Course::where('teacher_id', $teacher->id)->orderBy('title')->pluck('title', 'id');
        $allStudentsDropdown  = User::where('role', 'student')
            ->whereIn('id', $eligibleIds->toArray())
            ->orderBy('name')->pluck('name', 'id');

        return view('teacher.reports.index', compact(
            'report', 'students', 'courses', 'allStudentsDropdown',
            'courseId', 'studentId', 'dateFrom', 'dateTo', 'search'
        ));
    }
}
