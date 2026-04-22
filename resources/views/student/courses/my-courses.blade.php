@extends('layouts.student')

@section('title', 'My Courses - Nabha Learning')

@section('student-content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">My Courses</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $enrollments->count() }} course{{ $enrollments->count() !== 1 ? 's' : '' }} enrolled</p>
        </div>
        <a href="{{ route('student.courses') }}"
           class="text-sm bg-indigo-600 text-white px-4 py-2.5 rounded-xl hover:bg-indigo-700 transition font-medium">
            Browse More
        </a>
    </div>

    @if($enrollments->isEmpty())
        <div class="bg-white rounded-2xl p-16 text-center shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gray-700 mb-2">No courses yet</h3>
            <p class="text-gray-400 text-sm mb-6">Enroll in a course to start your learning journey.</p>
            <a href="{{ route('student.courses') }}"
               class="bg-indigo-600 text-white px-6 py-3 rounded-xl hover:bg-indigo-700 transition font-semibold">
                Explore Courses
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($enrollments as $enrollment)
                @php $course = $enrollment->course; @endphp
                @if($course)
                    <div class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">

                        {{-- Thumbnail --}}
                        <a href="{{ route('student.courses.show', $course) }}" class="block relative h-40 overflow-hidden">
                            <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>

                            <div class="absolute top-3 right-3">
                                @if($enrollment->payment_status === 'paid')
                                    <span class="bg-amber-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">Paid</span>
                                @else
                                    <span class="bg-emerald-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">Free</span>
                                @endif
                            </div>

                            <div class="absolute bottom-3 left-3">
                                <span class="bg-white/90 text-gray-700 text-xs font-semibold px-2.5 py-1 rounded-full">{{ $course->subject }}</span>
                            </div>
                        </a>

                        <div class="p-4">
                            <a href="{{ route('student.courses.show', $course) }}">
                                <h3 class="font-bold text-gray-800 text-sm line-clamp-2 group-hover:text-indigo-700 transition">
                                    {{ $course->title }}
                                </h3>
                            </a>

                            <div class="flex items-center gap-2 mt-2">
                                <img src="{{ $course->teacher->avatar_url }}" class="w-4 h-4 rounded-full object-cover" alt="">
                                <span class="text-xs text-gray-400">{{ $course->teacher->name }}</span>
                            </div>

                            <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
                                <span>{{ $course->lessons_count }} lessons</span>
                                <span>•</span>
                                <span>{{ $course->class_level }}</span>
                            </div>

                            <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                                <div>
                                    <p class="text-xs text-gray-400">Enrolled</p>
                                    <p class="text-xs font-medium text-gray-600">{{ $enrollment->enrolled_at->format('d M Y') }}</p>
                                </div>

                                @if($course->lessons_count > 0)
                                    <a href="{{ route('student.courses.lesson', [$course, $course->lessons->first()]) }}"
                                       class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1.5 rounded-lg font-semibold transition">
                                        Continue →
                                    </a>
                                @else
                                    <a href="{{ route('student.courses.show', $course) }}"
                                       class="text-xs bg-gray-100 text-gray-600 px-4 py-1.5 rounded-lg font-medium hover:bg-gray-200 transition">
                                        View
                                    </a>
                                @endif
                            </div>

                            @if($enrollment->transaction_id)
                                <p class="text-xs text-gray-300 mt-2 font-mono">TXN: {{ $enrollment->transaction_id }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
@endsection
