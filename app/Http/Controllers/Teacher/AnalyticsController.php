<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\ProgressReport;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $teacher   = auth()->user();
        $myLessons = Lesson::where('teacher_id', $teacher->id)->pluck('id');
        $myQuizzes = Quiz::where('teacher_id', $teacher->id)->pluck('id');

        $totalViews     = ProgressReport::whereIn('lesson_id', $myLessons)->sum('views');
        $totalDownloads = Lesson::where('teacher_id', $teacher->id)->sum('download_count');
        $totalAttempts  = QuizAttempt::whereIn('quiz_id', $myQuizzes)->where('status', 'completed')->count();
        $avgQuizScore   = QuizAttempt::whereIn('quiz_id', $myQuizzes)->where('status', 'completed')->avg('percentage') ?? 0;

        $quizPerformance = Quiz::where('teacher_id', $teacher->id)
            ->withCount(['attempts as completed_attempts' => fn ($q) => $q->where('status', 'completed')])
            ->with(['attempts' => fn ($q) => $q->where('status', 'completed')])
            ->get()
            ->map(fn ($quiz) => [
                'title'     => $quiz->title,
                'attempts'  => $quiz->completed_attempts,
                'avg_score' => round($quiz->attempts->avg('percentage') ?? 0, 1),
                'pass_rate' => $quiz->completed_attempts > 0
                    ? round(($quiz->attempts->where('passed', true)->count() / $quiz->completed_attempts) * 100, 1)
                    : 0,
            ]);

        $lessonEngagement = Lesson::where('teacher_id', $teacher->id)
            ->withCount(['progressReports as student_views'])
            ->orderBy('view_count', 'desc')
            ->take(10)->get();

        $weeklyAttempts = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $weeklyAttempts[$date->format('D')] = QuizAttempt::whereIn('quiz_id', $myQuizzes)
                ->whereDate('created_at', $date->toDateString())->count();
        }

        return view('teacher.analytics.index', compact(
            'totalViews', 'totalDownloads', 'totalAttempts', 'avgQuizScore',
            'quizPerformance', 'lessonEngagement', 'weeklyAttempts'
        ));
    }

    public function studentProgress()
    {
        $teacher      = auth()->user();
        $myLessonIds  = Lesson::where('teacher_id', $teacher->id)->pluck('id');
        $myQuizIds    = Quiz::where('teacher_id', $teacher->id)->pluck('id');
        $totalLessons = $myLessonIds->count();

        $studentIds = ProgressReport::whereIn('lesson_id', $myLessonIds)
            ->distinct()->pluck('student_id');

        // Bulk-load progress to avoid N+1
        $completionMap = ProgressReport::whereIn('lesson_id', $myLessonIds)
            ->whereIn('student_id', $studentIds)
            ->where('is_completed', true)
            ->select('student_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('student_id')
            ->pluck('cnt', 'student_id');

        // Bulk-load quiz stats
        $quizStats = QuizAttempt::whereIn('quiz_id', $myQuizIds)
            ->whereIn('student_id', $studentIds)
            ->where('status', 'completed')
            ->select(
                'student_id',
                DB::raw('COUNT(*) as cnt'),
                DB::raw('AVG(percentage) as avg_pct')
            )
            ->groupBy('student_id')
            ->get()
            ->keyBy('student_id');

        $students = User::where('role', 'student')
            ->whereIn('id', $studentIds)
            ->orderBy('name')
            ->get()
            ->map(function ($student) use ($completionMap, $quizStats, $totalLessons) {
                $completed   = $completionMap[$student->id] ?? 0;
                $qStats      = $quizStats[$student->id] ?? null;
                $avgScore    = $qStats ? round($qStats->avg_pct, 1) : 0;
                $progressPct = $totalLessons > 0 ? round(($completed / $totalLessons) * 100, 1) : 0;

                return [
                    'id'               => $student->id,
                    'name'             => $student->name,
                    'email'            => $student->email,
                    'class_level'      => $student->class_level ?? 'N/A',
                    'completed_lessons' => $completed,
                    'total_lessons'    => $totalLessons,
                    'quizzes_taken'    => $qStats ? $qStats->cnt : 0,
                    'avg_score'        => $avgScore,
                    'progress_pct'     => $progressPct,
                ];
            });

        $chartLabels   = $students->pluck('name')->values();
        $chartScores   = $students->pluck('avg_score')->values();
        $chartProgress = $students->pluck('progress_pct')->values();

        return view('teacher.student_progress', compact(
            'students', 'chartLabels', 'chartScores', 'chartProgress', 'totalLessons'
        ));
    }

    public function exportCsv()
    {
        $teacher      = auth()->user();
        $myLessonIds  = Lesson::where('teacher_id', $teacher->id)->pluck('id');
        $myQuizIds    = Quiz::where('teacher_id', $teacher->id)->pluck('id');
        $totalLessons = $myLessonIds->count();

        $studentIds = ProgressReport::whereIn('lesson_id', $myLessonIds)
            ->distinct()->pluck('student_id');

        $completionMap = ProgressReport::whereIn('lesson_id', $myLessonIds)
            ->whereIn('student_id', $studentIds)
            ->where('is_completed', true)
            ->select('student_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('student_id')
            ->pluck('cnt', 'student_id');

        $quizStats = QuizAttempt::whereIn('quiz_id', $myQuizIds)
            ->whereIn('student_id', $studentIds)
            ->where('status', 'completed')
            ->select('student_id', DB::raw('COUNT(*) as cnt'), DB::raw('AVG(percentage) as avg_pct'))
            ->groupBy('student_id')
            ->get()
            ->keyBy('student_id');

        $students = User::where('role', 'student')
            ->whereIn('id', $studentIds)
            ->orderBy('name')
            ->get();

        $filename = 'student-progress-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($students, $completionMap, $quizStats, $totalLessons) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['#', 'Name', 'Email', 'Class', 'Lessons Completed', 'Total Lessons', 'Progress %', 'Quizzes Taken', 'Avg Score %']);
            foreach ($students as $i => $student) {
                $completed = $completionMap[$student->id] ?? 0;
                $qStats    = $quizStats[$student->id] ?? null;
                fputcsv($handle, [
                    $i + 1,
                    $student->name,
                    $student->email,
                    $student->class_level ?? 'N/A',
                    $completed,
                    $totalLessons,
                    $totalLessons > 0 ? round(($completed / $totalLessons) * 100, 1) : 0,
                    $qStats ? $qStats->cnt : 0,
                    $qStats ? round($qStats->avg_pct, 1) : 0,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
