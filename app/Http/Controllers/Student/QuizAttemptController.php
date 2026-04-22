<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class QuizAttemptController extends Controller
{
    public function index(Request $request)
    {
        $student = auth()->user();
        $query = Quiz::active()->with(['teacher', 'attempts' => function ($q) use ($student) {
            $q->where('student_id', $student->id)->orderBy('created_at', 'desc');
        }])->where('class_level', $student->class_level);

        if ($request->subject) {
            $query->where('subject', $request->subject);
        }

        $quizzes = $query->latest()->paginate(12);
        $subjects = Quiz::active()->where('class_level', $student->class_level)
            ->distinct()->pluck('subject');

        return view('student.quizzes.index', compact('quizzes', 'subjects'));
    }

    public function startQuiz(Quiz $quiz)
    {
        $student = auth()->user();

        if (!$quiz->canAttempt($student->id)) {
            return redirect()->route('student.quizzes')
                ->with('error', "You have reached the maximum attempts ({$quiz->max_attempts}) for this quiz.");
        }

        $inProgress = QuizAttempt::where('student_id', $student->id)
            ->where('quiz_id', $quiz->id)
            ->where('status', 'started')
            ->first();

        if (!$inProgress) {
            $attemptNumber = QuizAttempt::where('student_id', $student->id)
                ->where('quiz_id', $quiz->id)->count() + 1;

            $inProgress = QuizAttempt::create([
                'student_id' => $student->id,
                'quiz_id' => $quiz->id,
                'status' => 'started',
                'attempt_number' => $attemptNumber,
                'started_at' => now(),
            ]);
        }

        $questions = $quiz->questions;
        return view('student.quizzes.attempt', compact('quiz', 'questions', 'inProgress'));
    }

    public function submitQuiz(Request $request, Quiz $quiz)
    {
        $attempt = QuizAttempt::where('student_id', auth()->id())
            ->where('quiz_id', $quiz->id)
            ->where('status', 'started')
            ->firstOrFail();

        $submittedAnswers = $request->input('answers', []);
        $attempt->calculateAndSave($submittedAnswers, $quiz);

        return redirect()->route('student.quiz.result', $attempt->id)
            ->with('success', 'Quiz submitted successfully!');
    }

    public function result(QuizAttempt $attempt)
    {
        if ($attempt->student_id !== auth()->id()) {
            abort(403);
        }

        $attempt->load(['quiz.questions']);
        return view('student.quizzes.result', compact('attempt'));
    }
}
