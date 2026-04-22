<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class AdminCourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with('teacher')->withCount(['lessons', 'enrollments']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $courses = $query->latest()->paginate(20);
        return view('admin.courses.index', compact('courses'));
    }

    public function preview(Course $course)
    {
        $course->load(['teacher', 'approvedBy', 'lessons.contents', 'enrollments']);
        return view('admin.courses.preview', compact('course'));
    }

    public function approve(Course $course)
    {
        $course->update([
            'status'      => 'published',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $course->lessons()->update(['status' => 'published']);

        return back()->with('success', "Course \"{$course->title}\" approved and published.");
    }

    public function reject(Course $course)
    {
        $course->update(['status' => 'rejected']);
        return back()->with('success', "Course \"{$course->title}\" rejected.");
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses')->with('success', 'Course deleted.');
    }
}
