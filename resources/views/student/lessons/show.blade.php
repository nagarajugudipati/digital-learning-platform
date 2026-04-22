@extends('layouts.student')

@section('title', $lesson->title . ' - Nabha Learning')

@section('student-content')
<div class="max-w-4xl mx-auto space-y-5">
    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-500 flex items-center gap-2">
        <a href="{{ route('student.lessons') }}" class="hover:text-primary-600">Lessons</a>
        <span>›</span>
        <span class="text-gray-800 truncate">{{ $lesson->title }}</span>
    </nav>

    <!-- Lesson Header -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-primary-100 text-primary-700 text-xs px-2 py-0.5 rounded-full font-medium">{{ $lesson->subject }}</span>
                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">{{ $lesson->class_level }}</span>
                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full uppercase">{{ $lesson->file_type }}</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $lesson->title }}</h1>
                <p class="text-gray-600 mt-2">{{ $lesson->description }}</p>
                <div class="flex items-center gap-4 mt-3 text-sm text-gray-500">
                    <span>{{ $lesson->teacher->name }}</span>
                    <span>{{ $lesson->view_count }} views</span>
                    <span>{{ $lesson->download_count }} downloads</span>
                    @if($lesson->duration_minutes)
                        <span>{{ $lesson->duration_minutes }} min</span>
                    @endif
                </div>
            </div>
            @if($progress && $progress->is_completed)
                <div class="flex-shrink-0 bg-emerald-100 text-emerald-700 rounded-xl p-3 text-center">
                    <div class="text-xs font-medium">Completed</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-wrap gap-3">
        @if($lesson->file_path)
            <a href="{{ route('student.lesson.download', $lesson->id) }}"
               class="bg-primary-600 text-white px-4 py-2.5 rounded-xl hover:bg-primary-700 transition text-sm font-medium"
               onclick="cacheForOffline('{{ $lesson->file_url }}')">
                Download for Offline
            </a>
        @endif
        @unless($progress && $progress->is_completed)
            <button id="mark-complete-btn"
                    onclick="markComplete({{ $lesson->id }})"
                    class="bg-emerald-600 text-white px-4 py-2.5 rounded-xl hover:bg-emerald-700 transition text-sm font-medium">
                Mark as Complete
            </button>
        @endunless
        @if($lesson->quizzes->count())
            <a href="{{ route('student.quizzes') }}" class="border border-primary-600 text-primary-600 px-4 py-2.5 rounded-xl hover:bg-primary-50 transition text-sm font-medium">
                Related Quizzes ({{ $lesson->quizzes->count() }})
            </a>
        @endif
    </div>

    <!-- Lesson Content -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @if($lesson->file_type === 'pdf' && $lesson->file_path)
            <div class="bg-gray-50 border-b border-gray-200 px-4 py-3 flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700">PDF Lesson</span>
                <a href="{{ $lesson->file_url }}" target="_blank" class="text-xs text-primary-600 hover:underline">Open in new tab ↗</a>
            </div>
            <div class="p-4">
                <iframe src="{{ $lesson->file_url }}" class="w-full rounded-lg" style="height: 600px;" title="{{ $lesson->title }}"></iframe>
            </div>

        @elseif($lesson->file_type === 'video' && $lesson->file_path)
            <div class="p-4">
                <video controls class="w-full rounded-lg" style="max-height: 500px;"
                       onplay="trackProgress({{ $lesson->id }})"
                       onended="markComplete({{ $lesson->id }})">
                    <source src="{{ $lesson->file_url }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>

        @elseif($lesson->content)
            <div class="p-6">
                <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                    {!! nl2br(e($lesson->content)) !!}
                </div>
            </div>

        @else
            <div class="p-8 text-center text-gray-500">
                <p>Lesson content will appear here.</p>
            </div>
        @endif
    </div>

    <!-- Related Lessons -->
    @if($relatedLessons->count())
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4">Related Lessons in {{ $lesson->subject }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($relatedLessons as $related)
                    <a href="{{ route('student.lesson.show', $related->id) }}"
                       class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl hover:border-primary-300 hover:bg-primary-50 transition group">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 group-hover:text-primary-600 truncate">{{ $related->title }}</p>
                            <p class="text-xs text-gray-500">{{ $related->view_count }} views</p>
                        </div>
                        <span class="text-primary-500 text-sm">→</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function markComplete(lessonId) {
    fetch(`/student/lessons/${lessonId}/complete`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('mark-complete-btn')?.remove();
            const banner = document.createElement('div');
            banner.className = 'fixed bottom-4 right-4 bg-emerald-600 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium';
            banner.textContent = 'Lesson marked as complete!';
            document.body.appendChild(banner);
            setTimeout(() => banner.remove(), 3000);
        }
    });
}

function cacheForOffline(url) {
    if ('caches' in window) {
        caches.open('nabha-lessons-v1').then(cache => {
            cache.add(url).then(() => {
                const banner = document.createElement('div');
                banner.className = 'fixed bottom-4 right-4 bg-blue-600 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium';
                banner.textContent = 'Lesson cached for offline access!';
                document.body.appendChild(banner);
                setTimeout(() => banner.remove(), 3000);
            });
        });
    }
}
</script>
@endpush
@endsection
