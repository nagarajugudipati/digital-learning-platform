@extends('layouts.student')

@section('title', 'Lessons - Nabha Learning')

@section('student-content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Lessons</h1>
            <p class="text-gray-500 text-sm mt-1">{{ auth()->user()->class_level }} curriculum materials</p>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" data-no-loading class="flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search lessons..."
               class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
        <select name="subject" class="px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
            <option value="">All Subjects</option>
            @foreach($subjects as $subject)
                <option value="{{ $subject }}" {{ request('subject') === $subject ? 'selected' : '' }}>{{ $subject }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-5 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition text-sm font-medium">
            Search
        </button>
        @if(request()->hasAny(['search','subject']))
            <a href="{{ route('student.lessons') }}" class="px-5 py-2.5 border border-gray-300 text-gray-600 rounded-xl hover:bg-gray-50 transition text-sm">Clear</a>
        @endif
    </form>

    <!-- Lessons Grid -->
    @if($lessons->isEmpty())
        <div class="bg-white rounded-xl p-12 text-center shadow-sm border border-gray-100">
            <p class="text-gray-500">No lessons found. Try a different filter.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($lessons as $lesson)
                @php $progress = $lesson->progressReports->first(); @endphp
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition group">
                    <!-- Card Header -->
                    <div class="h-32 bg-gradient-to-br from-primary-100 to-indigo-100 flex items-center justify-center relative">
                        <span class="text-sm font-semibold text-primary-700 uppercase tracking-wide">
                            {{ $lesson->file_type === 'video' ? 'Video' : ($lesson->file_type === 'pdf' ? 'PDF' : 'Text') }}
                        </span>
                        <div class="absolute top-2 right-2">
                            @if($progress && $progress->is_completed)
                                <span class="bg-emerald-500 text-white text-xs px-2 py-0.5 rounded-full">Completed</span>
                            @elseif($progress)
                                <span class="bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full">In Progress</span>
                            @endif
                        </div>
                        <div class="absolute top-2 left-2">
                            <span class="bg-white bg-opacity-90 text-primary-700 text-xs px-2 py-0.5 rounded-full font-medium">{{ $lesson->subject }}</span>
                        </div>
                    </div>

                    <div class="p-4">
                        <h3 class="font-semibold text-gray-800 group-hover:text-primary-600 transition line-clamp-2">{{ $lesson->title }}</h3>
                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $lesson->description }}</p>
                        <div class="flex items-center gap-3 mt-3 text-xs text-gray-400">
                            <span>{{ $lesson->teacher->name }}</span>
                            <span>•</span>
                            <span>{{ $lesson->view_count }} views</span>
                        </div>

                        @if($progress)
                            <div class="mt-3">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>Progress</span>
                                    <span>{{ $progress->completion_percentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-primary-600 h-1.5 rounded-full transition-all"
                                         style="width: {{ $progress->completion_percentage }}%"></div>
                                </div>
                            </div>
                        @endif

                        <a href="{{ route('student.lesson.show', $lesson->id) }}"
                           class="mt-4 w-full block text-center bg-primary-600 hover:bg-primary-700 text-white py-2 rounded-lg text-sm font-medium transition">
                            {{ $progress ? 'Continue →' : 'Start Learning →' }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $lessons->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
