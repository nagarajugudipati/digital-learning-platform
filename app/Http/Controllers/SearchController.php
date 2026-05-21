<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * AJAX live search for courses.
     * Returns up to 8 matching courses as JSON.
     * Results are scoped by the authenticated user's role.
     */
    public function courses(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $user  = auth()->user();
        $query = Course::where('title', 'like', "%{$q}%");

        if ($user->isStudent()) {
            $query->published();
        } elseif ($user->isTeacher()) {
            $query->where('teacher_id', $user->id);
        }
        // admin sees all courses (no extra filter)

        $courses = $query
            ->select('id', 'title', 'subject', 'status', 'teacher_id')
            ->with('teacher:id,name')
            ->limit(8)
            ->get()
            ->map(function (Course $course) use ($user) {
                return [
                    'id'      => $course->id,
                    'title'   => $course->title,
                    'subject' => $course->subject,
                    'teacher' => $course->teacher?->name ?? '',
                    'url'     => $this->courseUrl($course, $user),
                ];
            });

        return response()->json($courses);
    }

    private function courseUrl(Course $course, $user): string
    {
        if ($user->isStudent()) {
            return route('student.courses.show', $course->id);
        }
        if ($user->isTeacher()) {
            return route('teacher.courses.show', $course->id);
        }
        // admin
        return route('admin.courses.preview', $course->id);
    }
}
