@extends('layouts.teacher')

@section('title', 'My Quizzes - Nabha Learning')

@section('teacher-content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">My Quizzes</h1>
            <p class="text-gray-500 text-sm mt-1">Create and manage assessments for your students</p>
        </div>
        <a href="{{ route('teacher.quizzes.create') }}"
           class="bg-emerald-600 text-white px-4 py-2.5 rounded-xl hover:bg-emerald-700 transition text-sm font-medium">
            Create Quiz
        </a>
    </div>

    @if($quizzes->isEmpty())
        <div class="bg-white rounded-xl p-12 text-center shadow-sm border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">No Quizzes Yet</h3>
            <p class="text-gray-500 text-sm mb-5">Create your first quiz to test student understanding!</p>
            <a href="{{ route('teacher.quizzes.create') }}" class="bg-emerald-600 text-white px-6 py-3 rounded-xl hover:bg-emerald-700 transition font-medium">
                Create First Quiz
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($quizzes as $quiz)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="h-1.5 {{ $quiz->status === 'active' ? 'bg-emerald-500' : ($quiz->status === 'draft' ? 'bg-yellow-400' : 'bg-gray-400') }}"></div>
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">{{ $quiz->subject }}</span>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full
                                {{ $quiz->status === 'active' ? 'bg-emerald-100 text-emerald-700' : ($quiz->status === 'draft' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                                {{ ucfirst($quiz->status) }}
                            </span>
                        </div>

                        <h3 class="font-semibold text-gray-800 mb-1">{{ $quiz->title }}</h3>
                        <p class="text-xs text-gray-500">{{ $quiz->class_level }}</p>

                        <div class="grid grid-cols-3 gap-2 mt-3 text-center">
                            <div class="bg-gray-50 rounded-lg p-2">
                                <div class="text-sm font-bold text-gray-800">{{ $quiz->questions_count }}</div>
                                <div class="text-xs text-gray-500">Questions</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-2">
                                <div class="text-sm font-bold text-gray-800">{{ $quiz->attempts_count }}</div>
                                <div class="text-xs text-gray-500">Attempts</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-2">
                                <div class="text-sm font-bold text-gray-800">{{ $quiz->time_limit }}m</div>
                                <div class="text-xs text-gray-500">Time</div>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-4">
                            <form method="POST" action="{{ route('teacher.quizzes.toggle', $quiz->id) }}" class="flex-1">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="w-full text-xs py-2 rounded-lg font-medium transition
                                               {{ $quiz->status === 'active' ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }}">
                                    {{ $quiz->status === 'active' ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('teacher.quizzes.destroy', $quiz->id) }}"
                                  onsubmit="return confirm('Delete this quiz?')" class="flex-1">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full text-xs bg-red-100 hover:bg-red-200 text-red-700 py-2 rounded-lg font-medium transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div>{{ $quizzes->links() }}</div>
    @endif
</div>
@endsection
