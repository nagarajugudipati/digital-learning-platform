<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Query;
use App\Models\User;
use Illuminate\Http\Request;

class QueryController extends Controller
{
    public function index()
    {
        $queries  = Query::where('student_id', auth()->id())
            ->with(['teacher:id,name', 'repliedBy:id,name'])
            ->latest()
            ->paginate(15);

        $teachers = User::where('role', 'teacher')
            ->where(fn($q) => $q->whereNull('status')->orWhere('status', 'approved'))
            ->orderBy('name')
            ->get(['id', 'name', 'subject_specialization']);

        return view('student.queries.index', compact('queries', 'teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => ['nullable', 'exists:users,id'],
            'subject'    => ['required', 'string', 'max:200'],
            'message'    => ['required', 'string', 'max:2000'],
        ]);

        Query::create([
            'student_id' => auth()->id(),
            'teacher_id' => $request->teacher_id ?: null,
            'subject'    => $request->subject,
            'message'    => $request->message,
            'status'     => 'pending',
        ]);

        return back()->with('success', 'Your query has been submitted!');
    }
}
