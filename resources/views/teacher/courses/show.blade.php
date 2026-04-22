@extends('layouts.teacher')

@section('title', $course->title . ' - Manage')

@section('teacher-content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <a href="{{ route('teacher.courses') }}" class="text-sm text-gray-500 hover:text-gray-700">← All Courses</a>
            <h1 class="text-2xl font-bold text-gray-800 mt-1">{{ $course->title }}</h1>
            <div class="flex items-center gap-2 mt-1 text-sm text-gray-500">
                <span>{{ $course->subject }}</span> • <span>{{ $course->class_level }}</span>
                @php $sc = ['draft'=>'bg-gray-100 text-gray-600','pending'=>'bg-yellow-100 text-yellow-700','published'=>'bg-emerald-100 text-emerald-700','rejected'=>'bg-red-100 text-red-700']; @endphp
                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $sc[$course->status] ?? '' }}">{{ ucfirst($course->status) }}</span>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('teacher.courses.edit', $course) }}"
               class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition">
                Edit Course
            </a>
            @if(in_array($course->status, ['draft', 'rejected']))
                <form method="POST" action="{{ route('teacher.courses.submit', $course) }}">
                    @csrf
                    <button type="submit"
                            class="text-sm bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition"
                            onclick="return confirm('Submit for admin review?')">
                        Submit for Review
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Lessons list --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-gray-700">Course Lessons ({{ $course->lessons->count() }})</h2>
                <a href="{{ route('teacher.courses.add-lesson', $course) }}"
                   class="text-sm bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                    Add Lesson
                </a>
            </div>

            @if($course->lessons->isEmpty())
                <div class="bg-white rounded-xl p-8 text-center border border-dashed border-gray-300">
                    <p class="text-gray-500 text-sm">No lessons yet. Add your first lesson.</p>
                    <a href="{{ route('teacher.courses.add-lesson', $course) }}"
                       class="mt-3 inline-block text-sm bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                        Add First Lesson
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($course->lessons as $i => $lesson)
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm flex-shrink-0">
                                    {{ $i + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-800 text-sm">{{ $lesson->title }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $lesson->description }}</p>
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        @foreach($lesson->contents as $content)
                                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                                {{ ucfirst($content->type) }}
                                            </span>
                                        @endforeach
                                        @if($lesson->contents->isEmpty())
                                            <span class="text-xs bg-yellow-100 text-yellow-600 px-2 py-0.5 rounded-full">No content blocks</span>
                                        @endif
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('teacher.courses.destroy-lesson', [$course, $lesson]) }}"
                                      onsubmit="return confirm('Remove this lesson?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 px-2 py-1 font-medium">Remove</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Course info sidebar --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                     class="w-full h-40 object-cover">
                <div class="p-4 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Price</span>
                        <span class="font-semibold text-emerald-700">{{ $course->price > 0 ? '₹' . number_format($course->price, 2) : 'Free' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Lessons</span>
                        <span class="font-semibold text-gray-800">{{ $course->lessons->count() }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Enrolled</span>
                        <span class="font-semibold text-gray-800">{{ $course->enrollments->count() }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Status</span>
                        <span class="font-semibold capitalize text-gray-800">{{ $course->status }}</span>
                    </div>
                </div>
            </div>

            @if($course->status === 'rejected')
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
                    <p class="font-medium">Course Rejected</p>
                    <p class="mt-1 text-xs">Edit the course and resubmit for review.</p>
                </div>
            @elseif($course->status === 'pending')
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-700">
                    <p class="font-medium">Under Review</p>
                    <p class="mt-1 text-xs">Admin is reviewing your course.</p>
                </div>
            @elseif($course->status === 'published')
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-sm text-emerald-700">
                    <p class="font-medium">Live & Published</p>
                    <p class="mt-1 text-xs">Students can now enroll in this course.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
