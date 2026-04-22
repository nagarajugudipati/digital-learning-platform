@extends('layouts.student')

@section('title', $lesson->title . ' — ' . $course->title)

@section('student-content')
<div class="space-y-4">

    {{-- Breadcrumb + progress --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <nav class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('student.courses') }}" class="hover:text-indigo-600 transition">Courses</a>
            <span class="text-gray-300">›</span>
            <a href="{{ route('student.courses.show', $course) }}" class="hover:text-indigo-600 transition truncate max-w-[160px]">{{ $course->title }}</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-800 truncate max-w-[160px]">{{ $lesson->title }}</span>
        </nav>
        <div class="flex items-center gap-2 text-xs text-gray-500">
            <div class="flex items-center gap-1.5">
                <div class="w-24 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                    @php $pct = $allLessons->count() > 0 ? round((($currentIdx + 1) / $allLessons->count()) * 100) : 0; @endphp
                    <div class="h-full bg-indigo-600 rounded-full" style="width:{{ $pct }}%"></div>
                </div>
                <span class="font-medium text-gray-600">{{ $currentIdx + 1 }} / {{ $allLessons->count() }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-5 items-start">

        {{-- ── Main content ── --}}
        <div class="lg:col-span-3 space-y-4">

            {{-- Lesson header card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">{{ $lesson->title }}</h1>
                        <p class="text-gray-500 text-sm mt-1">{{ $lesson->description }}</p>
                    </div>
                    @if($lesson->duration_minutes)
                        <span class="text-xs bg-gray-100 text-gray-500 px-3 py-1 rounded-full whitespace-nowrap flex-shrink-0">
                            ⏱ {{ $lesson->duration_minutes }} min
                        </span>
                    @endif
                </div>
                @if($lesson->contents->isNotEmpty())
                    <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-gray-100">
                        @foreach($lesson->contents as $c)
                            <span class="text-xs bg-indigo-50 text-indigo-600 px-2.5 py-1 rounded-full font-medium">
                                {{ $c->icon }} {{ ucfirst($c->type) }}{{ $c->title ? ' — ' . $c->title : '' }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Content blocks --}}
            @php
                $hasContent = $lesson->contents->isNotEmpty() || $lesson->file_path || $lesson->content;
            @endphp

            @if(!$hasContent)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="text-5xl mb-3">📭</div>
                    <p class="text-gray-500 text-sm">No content has been added to this lesson yet.</p>
                </div>
            @else

                {{-- Multi-content blocks (new system) --}}
                @foreach($lesson->contents as $idx => $block)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                        {{-- Block header --}}
                        <div class="flex items-center gap-3 px-5 py-3.5 border-b border-gray-100 bg-gray-50">
                            <span class="text-xl">{{ $block->icon }}</span>
                            <div>
                                <p class="font-semibold text-sm text-gray-800">
                                    {{ $block->title ?: ucfirst($block->type) . ' Content' }}
                                </p>
                                <p class="text-xs text-gray-400 capitalize">{{ $block->type }}</p>
                            </div>
                            @if(in_array($block->type, ['pdf','video','image']) && $block->file_path)
                                <a href="{{ $block->file_url }}" target="_blank"
                                   class="ml-auto text-xs text-indigo-600 hover:underline flex items-center gap-1">
                                    ↗ Open
                                </a>
                            @endif
                        </div>

                        {{-- Block content --}}
                        <div class="p-5">
                            @if($block->type === 'pdf' && $block->file_path)
                                <div class="rounded-xl overflow-hidden border border-gray-200 bg-gray-100">
                                    <iframe src="{{ $block->file_url }}" class="w-full"
                                            style="height:70vh; min-height:420px;" title="{{ $block->title }}">
                                        <div class="p-6 text-center">
                                            <a href="{{ $block->file_url }}" target="_blank"
                                               class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">
                                                Download PDF
                                            </a>
                                        </div>
                                    </iframe>
                                </div>

                            @elseif($block->type === 'video' && $block->file_path)
                                <div class="rounded-xl overflow-hidden bg-black shadow-inner">
                                    <video controls class="w-full" style="max-height:65vh;" preload="metadata">
                                        <source src="{{ $block->file_url }}">
                                        <p class="text-white text-sm p-4">Your browser does not support video playback.</p>
                                    </video>
                                </div>

                            @elseif($block->type === 'image' && $block->file_path)
                                <div class="flex justify-center">
                                    <img src="{{ $block->file_url }}"
                                         alt="{{ $block->title ?? 'Lesson image' }}"
                                         class="max-w-full max-h-[60vh] rounded-xl border border-gray-200 object-contain shadow-sm">
                                </div>

                            @elseif($block->type === 'text' && $block->content_text)
                                <div class="prose prose-sm max-w-none bg-gray-50 rounded-xl border border-gray-200 p-6
                                            text-gray-800 leading-relaxed whitespace-pre-wrap text-sm">{{ $block->content_text }}</div>

                            @else
                                <div class="text-center py-6 text-gray-400">
                                    <p class="text-sm">Content file is not available.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                {{-- Legacy single-file fallback --}}
                @if($lesson->contents->isEmpty())
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="flex items-center gap-3 px-5 py-3.5 border-b border-gray-100 bg-gray-50">
                            <span class="text-xl">{{ $lesson->file_type === 'video' ? '🎥' : ($lesson->file_type === 'image' ? '🖼️' : '📄') }}</span>
                            <p class="font-semibold text-sm text-gray-800 capitalize">{{ $lesson->file_type }} Content</p>
                        </div>
                        <div class="p-5">
                            @if($lesson->file_type === 'pdf' && $lesson->file_path)
                                <div class="rounded-xl overflow-hidden border border-gray-200 bg-gray-100">
                                    <iframe src="{{ $lesson->file_url }}" class="w-full" style="height:70vh;"></iframe>
                                </div>
                            @elseif($lesson->file_type === 'video' && $lesson->file_path)
                                <div class="rounded-xl overflow-hidden bg-black">
                                    <video controls class="w-full" style="max-height:65vh;" preload="metadata">
                                        <source src="{{ $lesson->file_url }}">
                                    </video>
                                </div>
                            @elseif($lesson->content)
                                <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 text-gray-800 text-sm leading-relaxed whitespace-pre-wrap">
                                    {{ $lesson->content }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif

            {{-- ── Mark Complete / Auto-next ── --}}
            <div id="completeSection" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    @if($isCompleted)
                        <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center text-white text-sm font-bold">✓</div>
                        <div>
                            <p class="text-sm font-semibold text-emerald-700">Lesson completed!</p>
                            <p class="text-xs text-gray-400">Your progress has been saved.</p>
                        </div>
                    @else
                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 text-sm" id="completeIcon">○</div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Mark as complete</p>
                            <p class="text-xs text-gray-400">Track your progress through this course.</p>
                        </div>
                    @endif
                </div>
                @if(!$isCompleted)
                    <button id="markCompleteBtn" type="button"
                            onclick="markComplete()"
                            class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition">
                        <span id="completeBtnText">Mark Complete</span>
                        <span id="completeBtnSpinner" class="hidden">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                        </span>
                    </button>
                @endif
            </div>

            {{-- ── Prev / Next nav ── --}}
            <div class="flex justify-between gap-4">
                @if($prev)
                    <a href="{{ route('student.courses.lesson', [$course, $prev]) }}"
                       class="flex items-center gap-3 flex-1 bg-white hover:bg-gray-50 border border-gray-200 hover:border-indigo-300 text-gray-700 px-4 py-3.5 rounded-2xl transition shadow-sm group">
                        <div class="w-8 h-8 rounded-full bg-gray-100 group-hover:bg-indigo-100 flex items-center justify-center flex-shrink-0 transition">
                            <span class="text-gray-500 group-hover:text-indigo-600 text-sm">←</span>
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs text-gray-400">Previous Lesson</div>
                            <div class="font-semibold text-sm truncate">{{ $prev->title }}</div>
                        </div>
                    </a>
                @else
                    <div class="flex-1"></div>
                @endif

                @if($next)
                    <a href="{{ route('student.courses.lesson', [$course, $next]) }}"
                       class="flex items-center justify-end gap-3 flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3.5 rounded-2xl transition shadow-sm shadow-indigo-200 group">
                        <div class="min-w-0 text-right">
                            <div class="text-xs text-indigo-200">Next Lesson</div>
                            <div class="font-semibold text-sm truncate">{{ $next->title }}</div>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-white/20 group-hover:bg-white/30 flex items-center justify-center flex-shrink-0 transition">
                            <span class="text-sm">→</span>
                        </div>
                    </a>
                @else
                    <a href="{{ route('student.courses.show', $course) }}"
                       class="flex items-center justify-center gap-2 flex-1 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-3.5 rounded-2xl transition shadow-sm shadow-emerald-200 font-bold">
                        🎉 Course Complete!
                    </a>
                @endif
            </div>
        </div>

        {{-- ── Sidebar: lesson list ── --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 sticky top-20 overflow-hidden">
                {{-- Header --}}
                <div class="px-4 py-3.5 border-b border-gray-100 bg-gray-50">
                    <h2 class="font-bold text-gray-800 text-sm">{{ $course->title }}</h2>
                    <div class="flex items-center gap-2 mt-1">
                        <div class="flex-1 h-1 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-600 rounded-full" style="width:{{ $pct }}%"></div>
                        </div>
                        <span class="text-xs text-gray-400 whitespace-nowrap">{{ $pct }}%</span>
                    </div>
                </div>

                {{-- Lesson list --}}
                <div class="divide-y divide-gray-50 overflow-y-auto" style="max-height: calc(100vh - 260px);">
                    @foreach($allLessons as $i => $l)
                        @php $done = in_array($l->id, $completedIds); @endphp
                        <a href="{{ route('student.courses.lesson', [$course, $l]) }}"
                           class="flex items-center gap-3 px-4 py-3 transition
                                  {{ $l->id === $lesson->id
                                     ? 'bg-indigo-50 border-l-4 border-indigo-600'
                                     : 'hover:bg-gray-50 border-l-4 border-transparent' }}">

                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0
                                        {{ $l->id === $lesson->id
                                           ? 'bg-indigo-600 text-white'
                                           : ($done ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-500') }}">
                                @if($done && $l->id !== $lesson->id)
                                    ✓
                                @else
                                    {{ $i + 1 }}
                                @endif
                            </div>

                            <span class="text-xs leading-snug line-clamp-2 flex-1
                                         {{ $l->id === $lesson->id ? 'font-bold text-indigo-700' : 'text-gray-600' }}">
                                {{ $l->title }}
                            </span>
                            @if($done && $l->id !== $lesson->id)
                                <span class="text-emerald-500 text-xs flex-shrink-0">✓</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
/* ─── Constants ─────────────────────────────────────────────────────────── */
const LESSON_ID    = {{ $lesson->id }};
const COMPLETE_URL = "{{ route('student.courses.lesson.complete', [$course, $lesson]) }}";
const CSRF         = "{{ csrf_token() }}";
@if($next)
const NEXT_URL = "{{ route('student.courses.lesson', [$course, $next]) }}";
@else
const NEXT_URL = null;
@endif

/* ─── Video Position Save / Restore ─────────────────────────────────────── */
(function () {
    const KEY = `nabha_vpos_${LESSON_ID}`;

    // Restore position on all video elements for this lesson
    document.querySelectorAll('video').forEach(video => {
        const saved = parseFloat(localStorage.getItem(KEY) || '0');

        video.addEventListener('loadedmetadata', () => {
            if (saved > 4 && saved < video.duration - 3) {
                video.currentTime = saved;
                showResumeToast(video, saved);
            }
        });

        // Save position every 5 seconds while playing
        let saveTimer;
        video.addEventListener('play', () => {
            saveTimer = setInterval(() => {
                if (!video.paused && video.currentTime > 2) {
                    localStorage.setItem(KEY, video.currentTime.toFixed(1));
                }
            }, 5000);
        });
        video.addEventListener('pause', () => {
            clearInterval(saveTimer);
            if (video.currentTime > 2) localStorage.setItem(KEY, video.currentTime.toFixed(1));
        });
        // Clear on completion
        video.addEventListener('ended', () => {
            clearInterval(saveTimer);
            localStorage.removeItem(KEY);
        });
    });

    function showResumeToast(video, seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        const at   = mins > 0 ? `${mins}m ${secs}s` : `${secs}s`;
        if (window.toast) window.toast('info', `Resumed from ${at}`);
    }
})();

/* ─── Mark Complete + Auto-next ─────────────────────────────────────────── */
function markComplete() {
    const btn     = document.getElementById('markCompleteBtn');
    const txtEl   = document.getElementById('completeBtnText');
    const spinner = document.getElementById('completeBtnSpinner');
    if (!btn) return;

    btn.disabled = true;
    if (txtEl) txtEl.textContent = 'Saving…';
    if (spinner) spinner.classList.remove('hidden');

    fetch(COMPLETE_URL, {
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => {
        if (!data.completed) return;

        // Update section to "completed" state
        const section = document.getElementById('completeSection');
        section.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center text-white text-sm font-bold">✓</div>
                <div>
                    <p class="text-sm font-semibold text-emerald-700">Lesson completed!</p>
                    <p class="text-xs text-gray-400">Your progress has been saved.</p>
                </div>
            </div>`;

        if (window.toast) window.toast('success', 'Lesson marked as complete!');

        // Mark sidebar item as done (best-effort)
        document.querySelectorAll('[data-lesson-id="{{ $lesson->id }}"]').forEach(el => {
            el.classList.add('text-emerald-600');
        });

        if (data.next_url) {
            let countdown = 5;
            const wrap = document.createElement('div');
            wrap.className = 'flex items-center gap-3';
            wrap.innerHTML = `
                <p class="text-xs text-gray-400">
                    Auto-advancing in <span id="cdCount">${countdown}</span>s…
                </p>
                <a href="${data.next_url}"
                   class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl font-semibold transition">
                    Next Lesson →
                </a>
                <button onclick="clearInterval(window._autoNextTimer); this.parentElement.querySelector('#cdCount').closest('p').textContent='Auto-advance cancelled';"
                        class="text-xs text-gray-400 hover:text-gray-600 transition underline">Cancel</button>`;
            section.appendChild(wrap);

            window._autoNextTimer = setInterval(() => {
                countdown--;
                const el = document.getElementById('cdCount');
                if (el) el.textContent = countdown;
                if (countdown <= 0) {
                    clearInterval(window._autoNextTimer);
                    window.location.href = data.next_url;
                }
            }, 1000);
        }
    })
    .catch(() => {
        if (btn) btn.disabled = false;
        if (txtEl) txtEl.textContent = 'Mark Complete';
        if (spinner) spinner.classList.add('hidden');
        if (window.toast) window.toast('error', 'Could not save progress. Please try again.');
    });
}
</script>
@endpush
@endsection
