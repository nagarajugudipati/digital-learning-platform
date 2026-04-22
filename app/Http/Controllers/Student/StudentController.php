<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\ProgressReport;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function dashboard()
    {
        $student = auth()->user();

        $lessonsCompleted = ProgressReport::where('student_id', $student->id)
            ->where('is_completed', true)->count();

        $quizAttempts = QuizAttempt::where('student_id', $student->id)
            ->where('status', 'completed')->get();

        $avgScore = $quizAttempts->avg('percentage') ?? 0;

        $recentLessons = ProgressReport::with('lesson')
            ->where('student_id', $student->id)
            ->orderBy('last_accessed', 'desc')
            ->take(5)->get();

        $totalLessons = Lesson::published()
            ->forClass($student->class_level)->count();

        $totalQuizzes = Quiz::active()
            ->where('class_level', $student->class_level)->count();

        $subjectScores = $this->getSubjectScores($student->id);
        $progressByWeek = $this->getWeeklyProgress($student->id);

        return view('student.dashboard', compact(
            'student', 'lessonsCompleted', 'quizAttempts',
            'avgScore', 'recentLessons', 'totalLessons',
            'totalQuizzes', 'subjectScores', 'progressByWeek'
        ));
    }

    public function lessons(Request $request)
    {
        $student = auth()->user();
        $query = Lesson::published()->with(['teacher', 'progressReports' => function ($q) use ($student) {
            $q->where('student_id', $student->id);
        }]);

        if ($request->subject) {
            $query->where('subject', $request->subject);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $query->where('class_level', $student->class_level);
        $lessons = $query->latest()->paginate(12);

        $subjects = Lesson::published()->forClass($student->class_level)
            ->distinct()->pluck('subject');

        return view('student.lessons.index', compact('lessons', 'subjects'));
    }

    public function showLesson(Lesson $lesson)
    {
        if (!$lesson->isPublished()) {
            abort(404);
        }

        $lesson->incrementViews();
        $progress = ProgressReport::trackView(auth()->id(), $lesson->id);
        $relatedLessons = Lesson::published()
            ->where('subject', $lesson->subject)
            ->where('id', '!=', $lesson->id)
            ->take(4)->get();

        return view('student.lessons.show', compact('lesson', 'progress', 'relatedLessons'));
    }

    public function completeLesson(Lesson $lesson)
    {
        $progress = ProgressReport::firstOrCreate(
            ['student_id' => auth()->id(), 'lesson_id' => $lesson->id]
        );
        $progress->markCompleted();

        return response()->json(['success' => true, 'message' => 'Lesson marked as complete!']);
    }

    public function downloadLesson(Lesson $lesson)
    {
        if (!$lesson->isPublished() || !$lesson->file_path) {
            abort(404);
        }

        $progress = ProgressReport::firstOrCreate(
            ['student_id' => auth()->id(), 'lesson_id' => $lesson->id]
        );
        $progress->update(['is_downloaded' => true]);
        $lesson->increment('download_count');

        return Storage::disk('public')->download($lesson->file_path, $lesson->title . '.' . pathinfo($lesson->file_path, PATHINFO_EXTENSION));
    }

    private function getSubjectScores(int $studentId): array
    {
        $subjects = ['Mathematics', 'Science', 'English', 'Hindi', 'Social Studies'];
        $scores = [];
        foreach ($subjects as $subject) {
            $avg = QuizAttempt::join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
                ->where('quiz_attempts.student_id', $studentId)
                ->where('quiz_attempts.status', 'completed')
                ->where('quizzes.subject', $subject)
                ->avg('quiz_attempts.percentage');
            $scores[$subject] = round($avg ?? 0, 1);
        }
        return $scores;
    }

    private function getWeeklyProgress(int $studentId): array
    {
        $weeks = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = ProgressReport::where('student_id', $studentId)
                ->whereDate('last_accessed', $date->toDateString())
                ->count();
            $weeks[$date->format('D')] = $count;
        }
        return $weeks;
    }
}
