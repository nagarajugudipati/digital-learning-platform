@extends('layouts.student')

@section('title', 'Courses - Nabha Learning')

@section('student-content')
<div class="space-y-6">

    {{-- Page header --}}
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-6 text-white">
        <h1 class="text-2xl font-bold">Explore Courses</h1>
        <p class="text-indigo-200 text-sm mt-1">Learn from expert teachers at your own pace</p>
    </div>

    {{-- Search + Filters --}}
    <form method="GET" action="{{ route('student.courses') }}" data-no-loading
          class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search courses, subjects..."
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <select name="subject"
                    class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                <option value="">All Subjects</option>
                @foreach($subjects as $s)
                    <option value="{{ $s }}" {{ request('subject') === $s ? 'selected':'' }}>{{ $s }}</option>
                @endforeach
            </select>

            <select name="class_level"
                    class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                <option value="">All Classes</option>
                @foreach($classLevels as $cl)
                    <option value="{{ $cl }}" {{ request('class_level') === $cl ? 'selected':'' }}>{{ $cl }}</option>
                @endforeach
            </select>

            <button type="submit"
                    class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-indigo-700 transition">
                Search
            </button>
            @if(request()->hasAny(['search','subject','class_level']))
                <a href="{{ route('student.courses') }}"
                   class="flex items-center px-4 py-2.5 border border-gray-300 rounded-xl text-sm text-gray-500 hover:bg-gray-50 transition">
                    Clear
                </a>
            @endif
        </div>
    </form>

    {{-- Results count --}}
    @if($courses->total() > 0)
        <p class="text-sm text-gray-500">Showing {{ $courses->firstItem() }}–{{ $courses->lastItem() }} of {{ $courses->total() }} courses</p>
    @endif

    @if($courses->isEmpty())
        <div class="bg-white rounded-2xl p-16 text-center shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gray-700 mb-2">No courses found</h3>
            <p class="text-gray-400 text-sm mb-5">Try adjusting your search or browse all courses.</p>
            <a href="{{ route('student.courses') }}"
               class="bg-indigo-600 text-white px-6 py-3 rounded-xl hover:bg-indigo-700 transition font-semibold">
                Browse All
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($courses as $course)
                @php $isEnrolled = in_array($course->id, $enrolled); @endphp
                <div class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden
                            hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">

                    {{-- Thumbnail --}}
                    <a href="{{ route('student.courses.show', $course) }}" class="block relative h-44 overflow-hidden">
                        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>

                        {{-- Badges --}}
                        <div class="absolute top-3 left-3 flex gap-1.5">
                            @if($course->isFree())
                                <span class="bg-emerald-500 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow">FREE</span>
                            @endif
                            @if($isEnrolled)
                                <span class="bg-indigo-600 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow">Enrolled</span>
                            @endif
                        </div>

                        {{-- Subject tag --}}
                        <div class="absolute bottom-3 left-3">
                            <span class="bg-white/90 backdrop-blur-sm text-gray-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                {{ $course->subject }}
                            </span>
                        </div>
                    </a>

                    {{-- Card body --}}
                    <div class="p-4">
                        <a href="{{ route('student.courses.show', $course) }}">
                            <h3 class="font-bold text-gray-800 text-sm line-clamp-2 leading-snug group-hover:text-indigo-700 transition">
                                {{ $course->title }}
                            </h3>
                        </a>
                        <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ $course->description }}</p>

                        <div class="flex items-center gap-2 mt-3">
                            <img src="{{ $course->teacher->avatar_url }}" alt="{{ $course->teacher->name }}"
                                 class="w-5 h-5 rounded-full object-cover">
                            <span class="text-xs text-gray-500">{{ $course->teacher->name }}</span>
                        </div>

                        <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
                            <span>{{ $course->lessons_count }} lessons</span>
                            <span>•</span>
                            <span>{{ $course->enrollments_count }} enrolled</span>
                            <span>•</span>
                            <span>{{ $course->class_level }}</span>
                        </div>

                        <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                            <span class="font-extrabold {{ $course->isFree() ? 'text-emerald-600' : 'text-gray-800' }} text-base">
                                {{ $course->isFree() ? 'Free' : '₹' . number_format($course->price, 2) }}
                            </span>

                            @if($isEnrolled)
                                <a href="{{ route('student.courses.show', $course) }}"
                                   class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 py-1.5 rounded-lg font-semibold transition">
                                    Continue →
                                </a>
                            @else
                                <a href="{{ route('student.courses.show', $course) }}"
                                   class="text-xs bg-gray-100 hover:bg-indigo-50 hover:text-indigo-700 text-gray-600 px-3.5 py-1.5 rounded-lg font-semibold transition">
                                    View Details
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-2">{{ $courses->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
