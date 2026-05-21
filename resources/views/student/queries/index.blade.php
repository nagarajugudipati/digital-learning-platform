@extends('layouts.student')

@section('title', 'My Queries - Nabha Learning')

@section('student-content')
<div class="space-y-6">

    {{-- Page header --}}
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-6 text-white">
        <h1 class="text-2xl font-bold">{{ __('messages.queries.my_queries') }}</h1>
        <p class="text-indigo-200 text-sm mt-1">{{ __('messages.queries.subtitle_student') }}</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Submit Query Form --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6" x-data="{ open: false }">
        <button @click="open = !open"
                class="flex items-center justify-between w-full text-left">
            <h2 class="font-bold text-gray-800 text-lg">{{ __('messages.queries.ask_question') }}</h2>
            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-show="open" x-transition class="mt-5">
            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('student.queries.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('messages.queries.teacher_optional') }}
                    </label>
                    <select name="teacher_id"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                        <option value="">{{ __('messages.queries.any_teacher') }}</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}" {{ old('teacher_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->name }}{{ $t->subject_specialization ? ' — ' . $t->subject_specialization : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('messages.queries.subject') }} <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="subject" value="{{ old('subject') }}" required
                           placeholder="e.g., Quadratic equations in Chapter 3"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('messages.queries.message') }} <span class="text-red-400">*</span>
                    </label>
                    <textarea name="message" rows="4" required
                              placeholder="Describe your question in detail..."
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('message') }}</textarea>
                </div>

                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition">
                    {{ __('messages.queries.submit') }}
                </button>
            </form>
        </div>
    </div>

    {{-- Query list --}}
    <div class="space-y-4">
        <h2 class="font-semibold text-gray-700">{{ __('messages.queries.previous') }} ({{ $queries->total() }})</h2>

        @forelse($queries as $query)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 space-y-4">
                {{-- Query header --}}
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-800 truncate">{{ $query->subject }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $query->created_at->format('d M Y, h:i A') }}
                            @if($query->teacher)
                                · To: <span class="font-medium text-indigo-600">{{ $query->teacher->name }}</span>
                            @else
                                · To: <span class="text-gray-500">Any teacher</span>
                            @endif
                        </p>
                    </div>
                    <span class="flex-shrink-0 text-xs font-semibold px-2.5 py-1 rounded-full
                        {{ $query->isPending() ? 'bg-yellow-100 text-yellow-700' : 'bg-emerald-100 text-emerald-700' }}">
                        {{ $query->isPending() ? __('messages.queries.pending') : __('messages.queries.replied') }}
                    </span>
                </div>

                {{-- Message --}}
                <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-700 leading-relaxed">
                    {{ $query->message }}
                </div>

                {{-- Reply --}}
                @if($query->reply)
                    <div class="border-l-4 border-indigo-400 bg-indigo-50 rounded-r-xl pl-4 pr-4 py-3 space-y-1">
                        <p class="text-xs font-semibold text-indigo-600">
                            {{ __('messages.queries.reply_from') }} {{ $query->repliedBy?->name ?? 'Teacher' }}
                            @if($query->replied_at)
                                · {{ $query->replied_at->format('d M Y, h:i A') }}
                            @endif
                        </p>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $query->reply }}</p>
                    </div>
                @endif
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
