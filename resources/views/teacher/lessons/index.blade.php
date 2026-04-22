@extends('layouts.teacher')

@section('title', 'My Lessons - Nabha Learning')

@section('teacher-content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">My Lessons</h1>
            <p class="text-gray-500 text-sm mt-1">Manage your uploaded learning materials</p>
        </div>
        <a href="{{ route('teacher.lessons.create') }}"
           class="bg-emerald-600 text-white px-4 py-2.5 rounded-xl hover:bg-emerald-700 transition text-sm font-medium">
            Upload Lesson
        </a>
    </div>

    @if($lessons->isEmpty())
        <div class="bg-white rounded-xl p-12 text-center shadow-sm border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">No Lessons Yet</h3>
            <p class="text-gray-500 text-sm mb-5">Start by uploading your first lesson for students!</p>
            <a href="{{ route('teacher.lessons.create') }}" class="bg-emerald-600 text-white px-6 py-3 rounded-xl hover:bg-emerald-700 transition font-medium">
                Upload First Lesson
            </a>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Lesson</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Subject</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Class</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Views</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($lessons as $lesson)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-4">
                                    <div>
                                        <p class="font-medium text-sm text-gray-800">{{ Str::limit($lesson->title, 40) }}</p>
                                        <p class="text-xs text-gray-500">{{ $lesson->created_at->format('d M Y') }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600">{{ $lesson->subject }}</td>
                                <td class="px-4 py-4 text-sm text-gray-600">{{ $lesson->class_level }}</td>
                                <td class="px-4 py-4">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                            'approved' => 'bg-blue-100 text-blue-700',
                                            'published' => 'bg-emerald-100 text-emerald-700',
                                            'rejected' => 'bg-red-100 text-red-700',
                                        ];
                                    @endphp
                                    <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $statusClasses[$lesson->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($lesson->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600">{{ $lesson->view_count }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('teacher.lessons.edit', $lesson->id) }}"
                                           class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg transition">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('teacher.lessons.destroy', $lesson->id) }}"
                                              onsubmit="return confirm('Delete this lesson?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1.5 rounded-lg transition">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div>{{ $lessons->links() }}</div>
    @endif
</div>
@endsection
