<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    public function index()
    {
        $lessons = Lesson::where('teacher_id', auth()->id())
            ->withCount(['progressReports', 'quizzes'])
            ->latest()->paginate(15);

        return view('teacher.lessons.index', compact('lessons'));
    }

    public function create()
    {
        return view('teacher.lessons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'subject' => ['required', 'string'],
            'class_level' => ['required', 'string'],
            'file_type' => ['required', 'in:pdf,video,text,image'],
            'content' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:51200', 'mimes:pdf,mp4,webm,jpg,jpeg,png'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('lessons', 'public');
        }

        Lesson::create([
            'teacher_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'subject' => $validated['subject'],
            'class_level' => $validated['class_level'],
            'file_type' => $validated['file_type'],
            'content' => $validated['content'] ?? null,
            'file_path' => $filePath,
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('teacher.lessons')
            ->with('success', 'Lesson submitted for approval! Admin will review it shortly.');
    }

    public function edit(Lesson $lesson)
    {
        $this->authorize('update', $lesson);
        return view('teacher.lessons.create', compact('lesson'));
    }

    public function update(Request $request, Lesson $lesson)
    {
        $this->authorize('update', $lesson);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'subject' => ['required', 'string'],
            'class_level' => ['required', 'string'],
            'content' => ['nullable', 'string'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($request->hasFile('file')) {
            if ($lesson->file_path) {
                Storage::disk('public')->delete($lesson->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('lessons', 'public');
        }

        $lesson->update(array_merge($validated, ['status' => 'pending']));
        return redirect()->route('teacher.lessons')->with('success', 'Lesson updated and resubmitted for approval.');
    }

    public function destroy(Lesson $lesson)
    {
        $this->authorize('delete', $lesson);
        if ($lesson->file_path) {
            Storage::disk('public')->delete($lesson->file_path);
        }
        $lesson->delete();
        return redirect()->route('teacher.lessons')->with('success', 'Lesson deleted.');
    }
}
