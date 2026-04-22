@extends('layouts.teacher')

@section('title', 'My Courses - Nabha Learning')

@section('teacher-content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">My Courses</h1>
            <p class="text-gray-500 text-sm mt-1">Create and manage your courses</p>
        </div>
        <a href="{{ route('teacher.courses.create') }}"
           class="bg-emerald-600 text-white px-4 py-2.5 rounded-xl hover:bg-emerald-700 transition text-sm font-medium">
            New Course
        </a>
    </div>

    @if($courses->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">No Courses Yet</h3>
            <p class="text-gray-500 text-sm mb-5">Create your first course and start teaching!</p>
            <a href="{{ route('teacher.courses.create') }}"
               class="bg-emerald-600 text-white px-6 py-3 rounded-xl hover:bg-emerald-700 transition font-medium">
                Create First Course
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($courses as $course)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                    <div class="h-36 bg-gradient-to-br from-indigo-500 to-purple-600 relative overflow-hidden">
                        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                             class="w-full h-full object-cover opacity-80">
                        <div class="absolute top-3 right-3">
                            @php
                                $sc = ['draft'=>'bg-gray-500','pending'=>'bg-yellow-500','published'=>'bg-emerald-500','rejected'=>'bg-red-500'];
                            @endphp
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full text-white {{ $sc[$course->status] ?? 'bg-gray-500' }}">
                                {{ ucfirst($course->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col">
                        <h3 class="font-bold text-gray-800 line-clamp-2 leading-snug">{{ $course->title }}</h3>
                        <p class="text-xs text-gray-500 mt-1 line-clamp-2 flex-1">{{ $course->description }}</p>

                        <div class="flex items-center justify-between mt-3 text-xs text-gray-500">
                            <span>{{ $course->lessons_count }} lessons</span>
                            <span>{{ $course->enrollments_count }} enrolled</span>
                            <span class="font-semibold text-emerald-700">
                                {{ $course->price > 0 ? '₹' . number_format($course->price, 2) : 'Free' }}
                            </span>
                        </div>

                        <div class="mt-3 flex gap-2">
                            <a href="{{ route('teacher.courses.show', $course) }}"
                               class="flex-1 text-center text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 py-2 rounded-lg font-medium transition">
                                Manage
                            </a>
                            <a href="{{ route('teacher.courses.edit', $course) }}"
                               class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg transition">
                                Edit
                            </a>
                            @if($course->status === 'draft' || $course->status === 'rejected')
                                <form method="POST" action="{{ route('teacher.courses.submit', $course) }}">
                                    @csrf
                                    <button type="submit"
                                            class="text-xs bg-emerald-100 hover:bg-emerald-200 text-emerald-700 px-3 py-2 rounded-lg transition"
                                            onclick="return confirm('Submit for admin review?')">
                                        Submit
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div>{{ $courses->links() }}</div>
    @endif
</div>
@endsection
