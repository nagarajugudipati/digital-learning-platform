<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function index(Request $request)
    {
        $query = Lesson::with('teacher')->withCount(['progressReports', 'quizzes']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $lessons = $query->latest()->paginate(20);
        return view('admin.content.index', compact('lessons'));
    }

    public function approve(Lesson $lesson)
    {
        $lesson->update([
            'status' => 'published',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', "Lesson \"{$lesson->title}\" has been approved and published.");
    }

    public function reject(Request $request, Lesson $lesson)
    {
        $lesson->update(['status' => 'rejected']);
        return back()->with('success', "Lesson \"{$lesson->title}\" has been rejected.");
    }

    public function preview(Lesson $lesson)
    {
        return view('admin.content.preview', compact('lesson'));
    }

    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return redirect()->route('admin.content')->with('success', 'Lesson deleted.');
    }
}
