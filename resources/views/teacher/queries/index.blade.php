@extends('layouts.teacher')

@section('title', 'Student Queries - Nabha Learning')

@section('teacher-content')
<div class="space-y-5">

    <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ __('messages.queries.student_queries') }}</h1>
        <p class="text-gray-500 text-sm mt-1">{{ __('messages.queries.subtitle_teacher') }}</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Queries --}}
    <div class="space-y-4">
        @forelse($queries as $query)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5" x-data="{ replyOpen: false }">

                {{-- Header --}}
                <div class="flex items-start gap-4">
                    <img src="{{ $query->student->avatar_url }}" alt="{{ $query->student->name }}"
                         class="w-10 h-10 rounded-full object-cover flex-shrink-0">

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-semibold text-gray-800 text-sm">{{ $query->student->name }}</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-xs text-gray-400">{{ $query->created_at->format('d M Y, h:i A') }}</span>
                            @if(!$query->teacher_id)
                                <span class="text-xs bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full font-medium">Open to all teachers</span>
                            @endif
                            <span class="ml-auto text-xs font-semibold px-2.5 py-1 rounded-full
                                {{ $query->isPending() ? 'bg-yellow-100 text-yellow-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $query->isPending() ? __('messages.queries.pending') : __('messages.queries.replied') }}
                            </span>
                        </div>
                        <h3 class="font-semibold text-gray-700 mt-1">{{ $query->subject }}</h3>
                    </div>
                </div>

                {{-- Message --}}
                <div class="mt-3 bg-gray-50 rounded-xl p-4 text-sm text-gray-700 leading-relaxed">
                    {{ $query->message }}
                </div>

                {{-- Existing reply --}}
                @if($query->reply)
                    <div class="mt-3 border-l-4 border-emerald-400 bg-emerald-50 rounded-r-xl pl-4 pr-4 py-3 space-y-1">
                        <p class="text-xs font-semibold text-emerald-600">
                            Your reply · {{ $query->replied_at?->format('d M Y, h:i A') }}
                        </p>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $query->reply }}</p>
                    </div>
                @endif

                {{-- Reply form --}}
                <div class="mt-3 flex items-center gap-3">
                    <button @click="replyOpen = !replyOpen"
                            class="text-xs {{ $query->reply ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' : 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200' }} px-3 py-1.5 rounded-lg font-medium transition">
                        {{ $query->reply ? 'Edit Reply' : __('messages.queries.reply') }}
                    </button>
                </div>

                <div x-show="replyOpen" x-transition class="mt-3">
                    <form method="POST" action="{{ route('teacher.queries.reply', $query) }}" class="space-y-3">
                        @csrf @method('PUT')
                        <textarea name="reply" rows="4" required
                                  placeholder="{{ __('messages.queries.reply_placeholder') }}"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none">{{ $query->reply }}</textarea>
                        <div class="flex gap-2">
                            <button type="submit"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-xl text-sm font-semibold transition">
                                {{ __('messages.queries.send_reply') }}
                            </button>
                            <button type="button" @click="replyOpen = false"
                                    class="px-5 py-2 border border-gray-300 text-gray-600 rounded-xl text-sm hover:bg-gray-50 transition">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 shadow-sm">
                <p class="text-gray-400 text-sm">{{ __('messages.queries.no_queries') }}</p>
            </div>
        @endforelse

        <div>{{ $queries->links() }}</div>
    </div>
</div>
@endsection
