<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::where('teacher_id', auth()->id())
            ->withCount(['questions', 'attempts'])
            ->with('lesson')
            ->latest()->paginate(15);

        return view('teacher.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        $lessons = Lesson::where('teacher_id', auth()->id())
            ->where('status', 'published')->pluck('title', 'id');
        return view('teacher.quizzes.create', compact('lessons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'        => ['required', 'string', 'max:200'],
            'description'  => ['nullable', 'string'],
            'subject'      => ['required', 'string', 'max:255'],
            'class_level'  => ['required', 'string'],
            'lesson_id'    => ['nullable', 'exists:lessons,id'],
            'time_limit'   => ['required', 'integer', 'min:5', 'max:180'],
            'passing_marks'=> ['required', 'integer', 'min:0', 'max:100'],
            'max_attempts' => ['required', 'integer', 'min:1', 'max:10'],
            'questions'    => ['required', 'array', 'min:1'],
            'questions.*.question_text'   => ['required', 'string'],
            'questions.*.type'            => ['required', 'in:mcq,true_false,text'],
            'questions.*.marks'           => ['required', 'integer', 'min:1'],
            'questions.*.correct_answer'  => ['required', 'string'],
            'questions.*.explanation'     => ['nullable', 'string'],
        ]);

        $quiz = Quiz::create([
            'teacher_id'    => auth()->id(),
            'title'         => $request->title,
            'description'   => $request->description,
            'subject'       => $request->subject,
            'class_level'   => $request->class_level,
            'lesson_id'     => $request->lesson_id,
            'time_limit'    => $request->time_limit,
            'passing_marks' => $request->passing_marks,
            'max_attempts'  => $request->max_attempts,
            'status'        => 'draft',
        ]);

        $totalMarks = 0;
        foreach ($request->questions as $index => $qData) {
            $type = $qData['type'];

            $optionA = $optionB = $optionC = $optionD = null;
            $correctAnswer = $qData['correct_answer'];

            if ($type === 'mcq') {
                $optionA = $qData['option_a'] ?? null;
                $optionB = $qData['option_b'] ?? null;
                $optionC = $qData['option_c'] ?? null;
                $optionD = $qData['option_d'] ?? null;
            } elseif ($type === 'true_false') {
                $optionA = 'True';
                $optionB = 'False';
            }
            // text type: no options needed

            Question::create([
                'quiz_id'       => $quiz->id,
                'question_text' => $qData['question_text'],
                'type'          => $type,
                'option_a'      => $optionA,
                'option_b'      => $optionB,
                'option_c'      => $optionC,
                'option_d'      => $optionD,
                'correct_answer'=> $correctAnswer,
                'explanation'   => $qData['explanation'] ?? null,
                'marks'         => $qData['marks'],
                'order'         => $index + 1,
            ]);
            $totalMarks += $qData['marks'];
        }

        $quiz->update(['total_marks' => $totalMarks]);
        return redirect()->route('teacher.quizzes')->with('success', 'Quiz created! Set it to Active when ready.');
    }

    public function toggleStatus(Quiz $quiz)
    {
        if ($quiz->teacher_id !== auth()->id()) abort(403);
        $newStatus = $quiz->status === 'active' ? 'closed' : 'active';
        $quiz->update(['status' => $newStatus]);
        return back()->with('success', "Quiz is now {$newStatus}.");
    }

    public function destroy(Quiz $quiz)
    {
        if ($quiz->teacher_id !== auth()->id()) abort(403);
        $quiz->delete();
        return redirect()->route('teacher.quizzes')->with('success', 'Quiz deleted.');
    }
}
