@extends('layouts.admin')

@section('title', 'Preview: ' . $lesson->title)

@section('admin-content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.content') }}"
           class="text-sm text-gray-500 hover:text-gray-800 flex items-center gap-1">
            ← Back to Content Review
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Title bar --}}
        <div class="p-6 border-b border-gray-100">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <h1 class="text-xl font-bold text-gray-800">{{ $lesson->title }}</h1>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            {{ $lesson->status === 'published' ? 'bg-emerald-100 text-emerald-700'
                             : ($lesson->status === 'pending' ? 'bg-yellow-100 text-yellow-700'
                             : 'bg-red-100 text-red-700') }}">
                            {{ ucfirst($lesson->status) }}
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-3 text-sm text-gray-500">
                        <span>{{ $lesson->teacher->name }}</span>
                        <span>•</span>
                        <span>{{ $lesson->subject }}</span>
                        <span>•</span>
                        <span>{{ $lesson->class_level }}</span>
                        <span>•</span>
                        <span>{{ $lesson->created_at->format('d M Y') }}</span>
                        @if($lesson->duration_minutes)
                            <span>•</span>
                            <span>{{ $lesson->duration_minutes }} min</span>
                        @endif
                    </div>
                </div>

                <div class="flex gap-2 flex-shrink-0">
                    @if($lesson->status !== 'published')
                        <form method="POST" action="{{ route('admin.content.approve', $lesson->id) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="text-sm bg-emerald-100 hover:bg-emerald-200 text-emerald-700 px-4 py-2 rounded-lg font-medium transition">
                                Approve
                            </button>
                        </form>
                    @endif
                    @if($lesson->status !== 'rejected')
                        <form method="POST" action="{{ route('admin.content.reject', $lesson->id) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="text-sm bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-4 py-2 rounded-lg font-medium transition">
                                Reject
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Description --}}
        @if($lesson->description)
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Description</h2>
                <p class="text-gray-700 text-sm leading-relaxed">{{ $lesson->description }}</p>
            </div>
        @endif

        {{-- Content preview --}}
        <div class="p-6">
            @if($lesson->file_type === 'pdf' && $lesson->file_path)
                <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">PDF Preview</h2>
                <div class="rounded-xl overflow-hidden border border-gray-200 bg-gray-100">
                    <iframe src="{{ $lesson->file_url }}"
                            class="w-full"
                            style="height: 75vh; min-height: 500px;"
                            title="{{ $lesson->title }}">
                        <p class="p-4 text-gray-600">
                            Your browser does not support PDF embedding.
                            <a href="{{ $lesson->file_url }}" target="_blank"
                               class="text-indigo-600 underline">Download PDF</a>
                        </p>
                    </iframe>
                </div>
                <div class="mt-3 flex justify-end">
                    <a href="{{ $lesson->file_url }}" target="_blank"
                       class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
                        ↗ Open in new tab
                    </a>
                </div>

            @elseif($lesson->file_type === 'video' && $lesson->file_path)
                <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Video Preview</h2>
                <div class="rounded-xl overflow-hidden bg-black flex items-center justify-center"
                     style="max-height: 70vh;">
                    <video controls
                           class="w-full max-h-full"
                           style="max-height: 70vh;"
                           preload="metadata">
                        <source src="{{ $lesson->file_url }}">
                        Your browser does not support video playback.
                    </video>
                </div>

            @elseif($lesson->content)
                <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Lesson Content</h2>
                <div class="prose prose-sm max-w-none bg-gray-50 rounded-xl p-5 border border-gray-200
                            text-gray-800 leading-relaxed whitespace-pre-wrap text-sm">{{ $lesson->content }}</div>

            @else
                <div class="text-center py-16 text-gray-400">
                    <p class="text-sm">No previewable content available for this lesson.</p>
                    <p class="text-xs mt-1">The teacher may not have uploaded a file or added text content yet.</p>
                </div>
            @endif
        </div>

        {{-- Stats footer --}}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-wrap gap-6 text-sm text-gray-500">
            <span>{{ $lesson->view_count }} views</span>
            <span>{{ $lesson->download_count }} downloads</span>
            <span>{{ $lesson->quizzes->count() }} quizzes linked</span>
            @if($lesson->approved_at)
                <span>Approved {{ $lesson->approved_at->format('d M Y') }}</span>
            @endif
        </div>
    </div>
</div>
@endsection
