<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;

class AdminAnalyticsController extends Controller
{
    // ─── Teacher Analytics ────────────────────────────────────────────────────

    public function teachersAnalytics(Request $request)
    {
        $search = trim($request->input('search', ''));

        $teacherQuery = User::where('role', 'teacher')
            ->with(['courses' => fn($q) => $q->withCount('enrollments')])
            ->withCount('courses');

        if ($search !== '') {
            $teacherQuery->where(fn($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            );
        }

        // One query for the list + one to eager-load courses with enrollment counts
        $teachers = $teacherQuery->orderBy('name')->get()
            ->each(fn($t) => $t->total_students = $t->courses->sum('enrollments_count'));

        // Platform-wide summary stats (full dataset, not search-filtered)
        $totalTeachers  = User::where('role', 'teacher')->count();
        $totalTCourses  = Course::count();
        $totalTStudents = Enrollment::count();

        // Drill-down: selected teacher detail
        $selectedTeacher = null;
        $teacherCourses  = collect();
        $teacherStudents = collect();

        if ($request->filled('teacher_id')) {
            $selectedTeacher = User::where('role', 'teacher')
                ->with(['courses' => fn($q) => $q->withCount('enrollments')])
                ->findOrFail($request->integer('teacher_id'));

            $selectedTeacher->total_students = $selectedTeacher->courses->sum('enrollments_count');
            $teacherCourses = $selectedTeacher->courses;
            $courseIds      = $teacherCourses->pluck('id');

            // Unique students enrolled in any of this teacher's courses
            $studentIds = Enrollment::whereIn('course_id', $courseIds)
                ->pluck('user_id')
                ->unique();

            // Load those students with only the relevant enrollments (no N+1)
            $teacherStudents = User::whereIn('id', $studentIds)
                ->with(['enrollments' => fn($q) => $q
                    ->whereIn('course_id', $courseIds)
                    ->with('course:id,title')
                ])
                ->orderBy('name')
                ->get();
        }

        return view('admin.analytics.teachers', compact(
            'teachers', 'search',
            'totalTeachers', 'totalTCourses', 'totalTStudents',
            'selectedTeacher', 'teacherCourses', 'teacherStudents'
        ));
    }

    // ─── Student Analytics ────────────────────────────────────────────────────

    public function studentsAnalytics(Request $request)
    {
        $search = trim($request->input('search', ''));

        $studentQuery = User::where('role', 'student')
            ->withCount('enrollments')
            ->with(['enrollments' => fn($q) => $q->with([
                'course' => fn($q2) => $q2
                    ->select('id', 'title', 'teacher_id')
                    ->with('teacher:id,name'),
            ])]);

        if ($search !== '') {
            $studentQuery->where(fn($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            );
        }

        $students = $studentQuery->orderBy('name')->paginate(20)->withQueryString();

        // Attach unique teachers collection to each paginated student (no extra queries)
        $students->getCollection()->each(function ($student) {
            $student->unique_teachers = $student->enrollments
                ->pluck('course.teacher')
                ->filter()
                ->unique('id')
                ->values();
        });

        // Platform-wide summary stats
        $totalStudents    = User::where('role', 'student')->count();
        $totalEnrollments = Enrollment::count();
        $activeStudents   = User::where('role', 'student')->where('is_active', true)->count();

        // Drill-down: selected student detail
        $selectedStudent    = null;
        $studentEnrollments = collect();
        $studentTeachers    = collect();

        if ($request->filled('student_id')) {
            $selectedStudent = User::where('role', 'student')
                ->findOrFail($request->integer('student_id'));

            $studentEnrollments = Enrollment::where('user_id', $selectedStudent->id)
                ->with(['course' => fn($q) => $q
                    ->with('teacher:id,name,email,subject_specialization')
                ])
                ->orderByDesc('enrolled_at')
                ->get();

            $studentTeachers = $studentEnrollments
                ->pluck('course.teacher')
                ->filter()
                ->unique('id')
                ->values();
        }

        return view('admin.analytics.students', compact(
            'students', 'search',
            'totalStudents', 'totalEnrollments', 'activeStudents',
            'selectedStudent', 'studentEnrollments', 'studentTeachers'
        ));
    }
}
