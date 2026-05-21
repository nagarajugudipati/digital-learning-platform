<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Query;
use Illuminate\Http\Request;

class QueryController extends Controller
{
    public function index()
    {
        $queries = Query::where(fn($q) =>
                $q->where('teacher_id', auth()->id())
                  ->orWhereNull('teacher_id')
            )
            ->with(['student:id,name,avatar', 'repliedBy:id,name'])
            ->latest()
            ->paginate(20);

        return view('teacher.queries.index', compact('queries'));
    }

    public function reply(Request $request, Query $query)
    {
        abort_if(
            $query->teacher_id && $query->teacher_id !== auth()->id(),
            403
        );

        $request->validate([
            'reply' => ['required', 'string', 'max:3000'],
        ]);

        $query->update([
            'reply'       => $request->reply,
            'replied_by'  => auth()->id(),
            'status'      => 'replied',
            'replied_at'  => now(),
        ]);

        return back()->with('success', 'Reply sent!');
    }
}
