<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Query;
use Illuminate\Http\Request;

class QueryController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', '');

        $queries = Query::with(['student:id,name,avatar', 'teacher:id,name', 'repliedBy:id,name'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20);

        $pending = Query::where('status', 'pending')->count();
        $replied = Query::where('status', 'replied')->count();

        return view('admin.queries.index', compact('queries', 'status', 'pending', 'replied'));
    }

    public function reply(Request $request, Query $query)
    {
        $request->validate([
            'reply' => ['required', 'string', 'max:3000'],
        ]);

        $query->update([
            'reply'      => $request->reply,
            'replied_by' => auth()->id(),
            'status'     => 'replied',
            'replied_at' => now(),
        ]);

        return back()->with('success', 'Reply sent!');
    }
}
