@extends('layouts.admin')

@section('title', 'Preview: ' . $course->title)

@push('styles')
<style>
    .content-iframe { height: 480px; }
    @media (max-width: 640px) { .content-iframe { height: 300px; } }
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('admin-content')
@php
    use Illuminate\Support\Str;
    $statusMap = [
        'draft'     => ['bg' => 'bg-gray-100 text-gray-700',    'dot' => 'bg-gray-400'],
        'pending'   => ['bg' => 'bg-yellow-100 text-yellow-800', 'dot' => 'bg-yellow-400'],
        'published' => ['bg' => 'bg-emerald-100 text-emerald-800','dot'=> 'bg-emerald-500'],
        'rejected'  => ['bg' => 'bg-red-100 text-red-700',       'dot' => 'bg-red-500'],
    ];
    $s = $statusMap[$course->status] ?? $statusMap['draft'];
@endphp

<div class="space-y-5 max-w-5xl" x-data="{ zoomSrc: null, zoomAlt: '' }">

    {{-- ── Image Zoom Modal ── --}}
    <div x-show="zoomSrc" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
         @click.self="zoomSrc = null"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="relative max-w-5xl w-full">
            <button @click="zoomSrc = null"
                    class="absolute -top-10 right-0 text-white/70 hover:text-white text-sm flex items-center gap-1">
                &times; Close
            </button>
            <img :src="zoomSrc" :alt="zoomAlt"
                 class="w-full max-h-[85vh] object-contain rounded-xl shadow-2xl">
        </div>
    </div>

    {{-- ── Top nav ── --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <a href="{{ route('admin.courses') }}"
           class="text-sm text-gray-500 hover:text-gray-800 transition">
            ← Back to Course Approval
        </a>

        <div class="flex items-center gap-2">
            @if($course->status !== 'published')
                <form method="POST" action="{{ route('admin.courses.approve', $course) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-5 py-2.5 rounded-xl font-semibold transition shadow-sm shadow-emerald-100">
                        Approve & Publish
                    </button>
                </form>
            @else
                <span class="text-sm text-emerald-600 font-semibold flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span> Published
                </span>
            @endif

            @if($course->status !== 'rejected')
                <form method="POST" action="{{ route('admin.courses.reject', $course) }}"
                      onsubmit="return confirm('Reject this course? The teacher will need to resubmit.')">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 text-sm px-5 py-2.5 rounded-xl font-semibold transition">
                        Reject
                    </button>
                </form>
            @endif

            <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                  onsubmit="return confirm('Permanently delete this course? This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="text-gray-400 hover:text-red-600 hover:bg-red-50 text-sm px-3 py-2.5 rounded-xl transition border border-gray-200">
                    Delete
                </button>
            </form>
        </div>
    </div>

    {{-- ── Hero ── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="relative h-56 overflow-hidden">
            <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
            <div class="absolute bottom-0 inset-x-0 p-6">
                <div class="flex flex-wrap gap-2 mb-2">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $s['bg'] }} flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full {{ $s['dot'] }}"></span>
                        {{ ucfirst($course->status) }}
                    </span>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $course->isFree() ? 'bg-emerald-500 text-white' : 'bg-white text-gray-800' }}">
                        {{ $course->isFree() ? 'FREE' : '₹' . number_format($course->price, 2) }}
                    </span>
                    <span class="text-xs px-2.5 py-1 rounded-full bg-indigo-600 text-white font-semibold">
                        {{ $course->subject }}
                    </span>
                    <span class="text-xs px-2.5 py-1 rounded-full bg-white/20 text-white backdrop-blur-sm font-medium">
                        {{ $course->class_level }}
                    </span>
                </div>
                <h1 class="text-2xl font-bold text-white leading-snug">{{ $course->title }}</h1>
                <div class="flex items-center gap-3 mt-2 text-white/80 text-sm">
                    <img src="{{ $course->teacher->avatar_url }}" class="w-6 h-6 rounded-full object-cover border border-white/30">
                    <span class="font-medium">{{ $course->teacher->name }}</span>
                    <span class="opacity-50">·</span>
                    <span>{{ $course->teacher->subject_specialization ?? 'Teacher' }}</span>
                </div>
            </div>
        </div>

        {{-- Meta bar --}}
        <div class="flex flex-wrap gap-6 px-6 py-3 bg-gray-50 border-b border-gray-100 text-xs text-gray-500 font-medium">
            <span>{{ $course->lessons->count() }} lessons</span>
            <span>{{ $course->enrollments->count() }} enrolled</span>
            <span>Submitted {{ $course->created_at->format('d M Y') }}</span>
            @if($course->approved_at)
                <span class="text-emerald-600">Approved {{ $course->approved_at->format('d M Y') }}</span>
            @endif
            @if($course->approvedBy)
                <span>by {{ $course->approvedBy->name }}</span>
            @endif
        </div>

        {{-- Description --}}
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Course Description</h2>
            <p class="text-gray-700 text-sm leading-relaxed">{{ $course->description }}</p>
        </div>

        {{-- ── Lessons ── --}}
        <div class="px-6 py-5" x-data="{ openLesson: 0 }">
            <h2 class="font-bold text-gray-800 text-base mb-4">
                Lessons
                <span class="ml-2 text-xs font-normal text-gray-400">({{ $course->lessons->count() }} total)</span>
            </h2>

            @if($course->lessons->isEmpty())
                <div class="flex flex-col items-center py-12 text-gray-400">
                    <p class="text-sm font-medium">No lessons added to this course yet.</p>
                    <p class="text-xs mt-1">The teacher needs to add lessons before this can be approved.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($course->lessons as $i => $lesson)
                        <div class="border border-gray-200 rounded-xl overflow-hidden transition-shadow hover:shadow-sm"
                             x-data>

                            {{-- Lesson accordion header --}}
                            <button type="button"
                                    @click="$dispatch('toggle-lesson', {{ $i }}); openLesson = (openLesson === {{ $i }}) ? -1 : {{ $i }}"
                                    class="w-full flex items-center gap-4 px-5 py-4 text-left transition-colors"
                                    :class="openLesson === {{ $i }} ? 'bg-indigo-50' : 'bg-gray-50 hover:bg-gray-100'">

                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 transition-colors"
                                     :class="openLesson === {{ $i }} ? 'bg-indigo-600 text-white' : 'bg-indigo-100 text-indigo-700'">
                                    {{ $i + 1 }}
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-sm text-gray-800">{{ $lesson->title }}</p>
                                    <div class="flex flex-wrap items-center gap-3 mt-0.5">
                                        <span class="text-xs text-gray-400">{{ Str::limit($lesson->description, 70) }}</span>
                                        @if($lesson->duration_minutes)
                                            <span class="text-xs text-gray-400">{{ $lesson->duration_minutes }}m</span>
                                        @endif
                                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                            {{ $lesson->status === 'published' ? 'bg-emerald-100 text-emerald-700'
                                             : ($lesson->status === 'pending'  ? 'bg-yellow-100 text-yellow-700'
                                             : 'bg-gray-100 text-gray-600') }}">
                                            {{ ucfirst($lesson->status) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 flex-shrink-0">
                                    @php
                                        $contentCount = $lesson->contents->count()
                                            + ($lesson->file_path ? 1 : 0)
                                            + ($lesson->content   ? 1 : 0);
                                    @endphp
                                    <span class="text-xs text-gray-400">
                                        {{ $contentCount }} block{{ $contentCount !== 1 ? 's' : '' }}
                                    </span>
                                    <span class="text-gray-400 transition-transform duration-200 inline-block"
                                          :class="openLesson === {{ $i }} ? 'rotate-180' : ''">▼</span>
                                </div>
                            </button>

                            {{-- Lesson content (accordion body) --}}
                            <div x-show="openLesson === {{ $i }}"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-cloak>

                                @php $hasContent = $lesson->contents->isNotEmpty() || $lesson->file_path || $lesson->content; @endphp

                                @if(!$hasContent)
                                    <div class="px-5 py-6 text-center text-gray-400 border-t border-gray-100 bg-white">
                                        <p class="text-sm">No content blocks added to this lesson yet.</p>
                                    </div>
                                @else
                                    @foreach($lesson->contents as $j => $block)
                                        <div class="border-t border-gray-100 bg-white">
                                            <div class="flex items-center gap-3 px-5 py-3 bg-gray-50/80">
                                                <div class="flex-1">
                                                    <span class="text-sm font-semibold text-gray-800">
                                                        {{ $block->title ?: ucfirst($block->type) . ' Content' }}
                                                    </span>
                                                    <span class="ml-2 text-xs text-gray-400 bg-white border border-gray-200 px-2 py-0.5 rounded-full">
                                                        {{ $block->type }}
                                                    </span>
                                                </div>
                                                @if(in_array($block->type, ['pdf','video','image']) && $block->file_path)
                                                    <a href="{{ $block->file_url }}" target="_blank"
                                                       class="text-xs text-indigo-600 hover:underline flex-shrink-0">
                                                        ↗ Open in new tab
                                                    </a>
                                                @endif
                                            </div>

                                            <div class="px-5 py-4">
                                                @if($block->type === 'video')
                                                    @if($block->file_path && $block->file_url)
                                                        <div class="rounded-xl overflow-hidden bg-black shadow-inner">
                                                            <video controls preload="metadata"
                                                                   class="w-full" style="max-height:500px;"
                                                                   onerror="this.parentElement.innerHTML='<div class=\'p-6 text-center text-white text-sm\'>Video file could not be loaded. <a href=\'{{ $block->file_url }}\' target=\'_blank\' class=\'underline\'>Try opening directly</a>.</div>'">
                                                                <source src="{{ $block->file_url }}" type="video/mp4">
                                                                <p class="text-white p-4 text-sm">Your browser does not support HTML5 video.</p>
                                                            </video>
                                                        </div>
                                                    @else
                                                        <div class="text-sm text-gray-400 bg-gray-50 rounded-xl p-4 border border-dashed border-gray-200">
                                                            File not available — the video may not have been uploaded correctly.
                                                        </div>
                                                    @endif

                                                @elseif($block->type === 'pdf')
                                                    @if($block->file_path && $block->file_url)
                                                        <div class="rounded-xl overflow-hidden border border-gray-200 bg-gray-50">
                                                            <div class="flex items-center justify-end px-3 py-2 bg-gray-100 border-b border-gray-200">
                                                                <a href="{{ $block->file_url }}" target="_blank"
                                                                   class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition">
                                                                    ↗ Open in new tab
                                                                </a>
                                                            </div>
                                                            <iframe src="{{ $block->file_url }}"
                                                                    class="w-full content-iframe"
                                                                    title="{{ $block->title ?? 'PDF Document' }}">
                                                            </iframe>
                                                        </div>
                                                    @else
                                                        <div class="text-sm text-gray-400 bg-gray-50 rounded-xl p-4 border border-dashed border-gray-200">
                                                            File not available — the PDF may not have been uploaded correctly.
                                                        </div>
                                                    @endif

                                                @elseif($block->type === 'image')
                                                    @if($block->file_path && $block->file_url)
                                                        <div class="flex justify-center">
                                                            <img src="{{ $block->file_url }}"
                                                                 alt="{{ $block->title ?? 'Image' }}"
                                                                 class="max-w-full max-h-[300px] rounded-xl border border-gray-200 object-contain shadow-sm cursor-zoom-in hover:opacity-90 transition"
                                                                 @click="zoomSrc = '{{ $block->file_url }}'; zoomAlt = '{{ addslashes($block->title ?? 'Image') }}'"
                                                                 title="Click to zoom">
                                                        </div>
                                                        <p class="text-center text-xs text-gray-400 mt-1.5">Click image to zoom</p>
                                                    @else
                                                        <div class="text-sm text-gray-400 bg-gray-50 rounded-xl p-4 border border-dashed border-gray-200">
                                                            File not available — the image may not have been uploaded correctly.
                                                        </div>
                                                    @endif

                                                @elseif($block->type === 'text' && $block->content_text)
                                                    <div class="bg-gray-50 rounded-xl border border-gray-200 p-5
                                                                text-sm text-gray-800 leading-relaxed whitespace-pre-wrap font-mono">{{ $block->content_text }}</div>

                                                @else
                                                    <div class="text-sm text-gray-400 bg-gray-50 rounded-xl p-4 border border-dashed border-gray-200">
                                                        No previewable content for this block.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- Legacy single-file fallback --}}
                                    @if($lesson->contents->isEmpty())
                                        <div class="border-t border-gray-100 bg-white">
                                            <div class="flex items-center gap-3 px-5 py-3 bg-gray-50/80">
                                                <span class="text-sm font-semibold text-gray-800 capitalize">
                                                    {{ $lesson->file_type ?? 'Text' }} Content
                                                </span>
                                                <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Legacy</span>
                                                @if($lesson->file_path)
                                                    <a href="{{ $lesson->file_url }}" target="_blank"
                                                       class="ml-auto text-xs text-indigo-600 hover:underline flex-shrink-0">↗ Open</a>
                                                @endif
                                            </div>
                                            <div class="px-5 py-4">
                                                @if($lesson->file_type === 'video' && $lesson->file_path)
                                                    <div class="rounded-xl overflow-hidden bg-black shadow-inner">
                                                        <video controls preload="metadata" class="w-full" style="max-height:500px;"
                                                               onerror="this.parentElement.innerHTML='<div class=\'p-4 text-center text-white text-sm\'>Video could not be loaded.</div>'">
                                                            <source src="{{ $lesson->file_url }}" type="video/mp4">
                                                        </video>
                                                    </div>
                                                @elseif($lesson->file_type === 'pdf' && $lesson->file_path)
                                                    <div class="rounded-xl overflow-hidden border border-gray-200 bg-gray-50">
                                                        <div class="flex justify-end px-3 py-2 bg-gray-100 border-b border-gray-200">
                                                            <a href="{{ $lesson->file_url }}" target="_blank"
                                                               class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">↗ Open in new tab</a>
                                                        </div>
                                                        <iframe src="{{ $lesson->file_url }}" class="w-full content-iframe"></iframe>
                                                    </div>
                                                @elseif($lesson->file_type === 'image' && $lesson->file_path)
                                                    <div class="flex justify-center">
                                                        <img src="{{ $lesson->file_url }}" alt="{{ $lesson->title }}"
                                                             class="max-w-full max-h-[300px] rounded-xl border border-gray-200 object-contain cursor-zoom-in hover:opacity-90 transition"
                                                             @click="zoomSrc = '{{ $lesson->file_url }}'; zoomAlt = '{{ addslashes($lesson->title) }}'">
                                                    </div>
                                                    <p class="text-center text-xs text-gray-400 mt-1.5">Click to zoom</p>
                                                @elseif($lesson->content)
                                                    <div class="bg-gray-50 rounded-xl border border-gray-200 p-5 text-sm text-gray-800 leading-relaxed whitespace-pre-wrap">{{ $lesson->content }}</div>
                                                @else
                                                    <div class="text-sm text-gray-400 bg-gray-50 rounded-xl p-4 border border-dashed border-gray-200">
                                                        File not available.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ── Bottom action bar ── --}}
    @if($course->status === 'pending')
    <div class="sticky bottom-4 bg-white rounded-2xl shadow-lg border border-gray-200 p-4 flex items-center justify-between gap-4">
        <div>
            <p class="font-semibold text-sm text-gray-800">Pending Review</p>
            <p class="text-xs text-gray-500">This course is waiting for your decision.</p>
        </div>
        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('admin.courses.approve', $course) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-2.5 rounded-xl transition shadow-sm shadow-emerald-100">
                    Approve & Publish
                </button>
            </form>
            <form method="POST" action="{{ route('admin.courses.reject', $course) }}"
                  onsubmit="return confirm('Reject this course?')">
                @csrf @method('PATCH')
                <button type="submit"
                        class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-semibold px-6 py-2.5 rounded-xl transition">
                    Reject
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
