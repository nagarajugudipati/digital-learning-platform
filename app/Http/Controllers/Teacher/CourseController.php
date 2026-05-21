<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('teacher_id', auth()->id())
            ->withCount('lessons', 'enrollments')
            ->latest()
            ->paginate(15);

        return view('teacher.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('teacher.courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'subject'     => ['required', 'string'],
            'class_level' => ['required', 'string'],
            'thumbnail'   => ['nullable', 'image', 'max:2048'],
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        Course::create([
            'teacher_id'  => auth()->id(),
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'price'       => $validated['price'],
            'subject'     => $validated['subject'],
            'class_level' => $validated['class_level'],
            'thumbnail'   => $thumbnailPath,
            'status'      => 'draft',
        ]);

        return redirect()->route('teacher.courses')->with('success', 'Course created! Add lessons and submit for review.');
    }

    public function show(Course $course)
    {
        abort_if($course->teacher_id !== auth()->id(), 403);

        $course->load(['lessons.contents']);
        return view('teacher.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        abort_if($course->teacher_id !== auth()->id(), 403);
        return view('teacher.courses.create', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        abort_if($course->teacher_id !== auth()->id(), 403);

        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'subject'     => ['required', 'string'],
            'class_level' => ['required', 'string'],
            'thumbnail'   => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        $course->update(array_merge($validated, ['status' => 'draft']));

        return redirect()->route('teacher.courses.show', $course)->with('success', 'Course updated.');
    }

    public function destroy(Course $course)
    {
        abort_if($course->teacher_id !== auth()->id(), 403);

        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();
        return redirect()->route('teacher.courses')->with('success', 'Course deleted.');
    }

    public function submit(Course $course)
    {
        abort_if($course->teacher_id !== auth()->id(), 403);
        abort_if($course->lessons()->count() === 0, 422, 'Add at least one lesson before submitting.');

        $course->update(['status' => 'pending']);
        return back()->with('success', 'Course submitted for admin review!');
    }

    public function addLesson(Course $course)
    {
        abort_if($course->teacher_id !== auth()->id(), 403);
        return view('teacher.courses.add-lesson', compact('course'));
    }

    public function storeLesson(Request $request, Course $course)
    {
        abort_if($course->teacher_id !== auth()->id(), 403);

        $request->validate([
            'title'                        => ['required', 'string', 'max:200'],
            'description'                  => ['required', 'string'],
            'contents'                     => ['required', 'array', 'min:1'],
            'contents.*.type'              => ['required', 'in:video,pdf,image,text'],
            'contents.*.title'             => ['nullable', 'string', 'max:200'],
            'contents.*.content_text'      => ['nullable', 'string'],
            'contents.*.file'              => ['nullable', 'file', 'max:51200', 'mimes:pdf,mp4,webm,jpg,jpeg,png'],
        ]);

        $lesson = Lesson::create([
            'teacher_id'  => auth()->id(),
            'course_id'   => $course->id,
            'title'       => $request->title,
            'description' => $request->description,
            'subject'     => $course->subject ?? 'General',
            'class_level' => $course->class_level ?? 'All',
            'file_type'   => $request->contents[0]['type'] ?? 'text',
            'status'      => 'pending',
            'order'       => $course->lessons()->max('order') + 1,
        ]);

        foreach ($request->contents as $i => $block) {
            $filePath = null;
            if (isset($block['file']) && $request->hasFile("contents.{$i}.file")) {
                $filePath = $request->file("contents.{$i}.file")->store('lesson-contents', 'public');
            }

            LessonContent::create([
                'lesson_id'    => $lesson->id,
                'title'        => $block['title'] ?? null,
                'type'         => $block['type'],
                'file_path'    => $filePath,
                'content_text' => $block['content_text'] ?? null,
                'order'        => $i,
            ]);
        }

        return redirect()->route('teacher.courses.show', $course)->with('success', 'Lesson added to course!');
    }

    public function editLesson(Course $course, Lesson $lesson)
    {
        abort_if($course->teacher_id !== auth()->id(), 403);
        abort_if($lesson->course_id !== $course->id, 403);

        $lesson->load('contents');
        return view('teacher.courses.edit-lesson', compact('course', 'lesson'));
    }

    public function updateLesson(Request $request, Course $course, Lesson $lesson)
    {
        abort_if($course->teacher_id !== auth()->id(), 403);
        abort_if($lesson->course_id !== $course->id, 403);

        $request->validate([
            'title'                   => ['required', 'string', 'max:200'],
            'description'             => ['required', 'string'],
            'contents'                => ['required', 'array', 'min:1'],
            'contents.*.type'         => ['required', 'in:video,pdf,image,text'],
            'contents.*.title'        => ['nullable', 'string', 'max:200'],
            'contents.*.content_text' => ['nullable', 'string'],
            'contents.*.file'         => ['nullable', 'file', 'max:51200', 'mimes:pdf,mp4,webm,jpg,jpeg,png'],
        ]);

        $lesson->update([
            'title'       => $request->title,
            'description' => $request->description,
            'file_type'   => $request->contents[0]['type'] ?? $lesson->file_type,
        ]);

        // Delete explicitly removed blocks
        if ($request->filled('deleted_ids')) {
            foreach ($request->deleted_ids as $delId) {
                $content = LessonContent::where('id', $delId)
                    ->where('lesson_id', $lesson->id)
                    ->first();
                if ($content) {
                    if ($content->file_path) {
                        Storage::disk('public')->delete($content->file_path);
                    }
                    $content->delete();
                }
            }
        }

        foreach ($request->contents as $i => $block) {
            $id = !empty($block['id']) ? (int) $block['id'] : null;

            if ($id) {
                $content = LessonContent::where('id', $id)
                    ->where('lesson_id', $lesson->id)
                    ->first();
                if ($content) {
                    $updateData = [
                        'type'         => $block['type'],
                        'title'        => $block['title'] ?? null,
                        'content_text' => $block['content_text'] ?? null,
                        'order'        => $i,
                    ];
                    if ($request->hasFile("contents.{$i}.file")) {
                        if ($content->file_path) {
                            Storage::disk('public')->delete($content->file_path);
                        }
                        $updateData['file_path'] = $request->file("contents.{$i}.file")
                            ->store('lesson-contents', 'public');
                    }
                    $content->update($updateData);
                }
            } else {
                $filePath = null;
                if ($request->hasFile("contents.{$i}.file")) {
                    $filePath = $request->file("contents.{$i}.file")
                        ->store('lesson-contents', 'public');
                }
                LessonContent::create([
                    'lesson_id'    => $lesson->id,
                    'type'         => $block['type'],
                    'title'        => $block['title'] ?? null,
                    'file_path'    => $filePath,
                    'content_text' => $block['content_text'] ?? null,
                    'order'        => $i,
                ]);
            }
        }

        return redirect()->route('teacher.courses.show', $course)
            ->with('success', 'Lesson updated successfully!');
    }

    public function destroyLesson(Course $course, Lesson $lesson)
    {
        abort_if($course->teacher_id !== auth()->id(), 403);
        abort_if($lesson->course_id !== $course->id, 403);

        foreach ($lesson->contents as $content) {
            if ($content->file_path) {
                Storage::disk('public')->delete($content->file_path);
            }
        }

        $lesson->delete();
        return back()->with('success', 'Lesson removed from course.');
    }
}
