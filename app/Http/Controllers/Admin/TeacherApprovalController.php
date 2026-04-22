<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherApprovalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');

        $teachers = User::where('role', 'teacher')
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20);

        return view('admin.teachers.index', compact('teachers', 'status'));
    }

    public function approve(User $user)
    {
        abort_unless($user->role === 'teacher', 403);
        $user->update(['status' => 'approved']);
        return back()->with('success', "{$user->name} has been approved as a teacher.");
    }

    public function reject(User $user)
    {
        abort_unless($user->role === 'teacher', 403);
        $user->update(['status' => 'rejected']);
        return back()->with('success', "{$user->name}'s teacher application has been rejected.");
    }
}
