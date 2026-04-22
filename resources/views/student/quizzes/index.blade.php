@extends('layouts.student')

@section('title', 'Quizzes - Nabha Learning')

@section('student-content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">✏️ Quizzes</h1>
        <p class="text-gray-500 text-sm mt-1">Test your knowledge and track your progress</p>
    </div>

    <!-- Filter -->
    <form method="GET" data-no-loading class="flex gap-3">
        <select name="subject" class="px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
            <option value="">All Subjects</option>
            @foreach($subjects as $subject)
                <option value="{{ $subject }}" {{ request('subject') === $subject ? 'selected' : '' }}>{{ $subject }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-5 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition text-sm font-medium">Filter</button>
        @if(request('subject'))
            <a href="{{ route('student.quizzes') }}" class="px-5 py-2.5 border border-gray-300 text-gray-600 rounded-xl hover:bg-gray-50 transition text-sm">Clear</a>
        @endif
    </form>

    <!-- Quiz Grid -->
    @if($quizzes->isEmpty())
        <div class="bg-white rounded-xl p-12 text-center shadow-sm border border-gray-100">
            <div class="text-5xl mb-4">📋</div>
            <p class="text-gray-500">No quizzes available yet. Check back soon!</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($quizzes as $quiz)
                @php
                    $myAttempts = $quiz->attempts;
                    $bestAttempt = $myAttempts->where('status','completed')->sortByDesc('percentage')->first();
                    $canAttempt = $myAttempts->where('status','completed')->count() < $quiz->max_attempts;
                    $inProgress = $myAttempts->where('status','started')->first();
                @endphp
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                    <div class="h-2 {{ $bestAttempt && $bestAttempt->passed ? 'bg-emerald-500' : ($bestAttempt ? 'bg-red-400' : 'bg-primary-600') }}"></div>
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-3">
                            <span class="bg-primary-100 text-primary-700 text-xs px-2 py-0.5 rounded-full font-medium">{{ $quiz->subject }}</span>
                            @if($bestAttempt)
                                <span class="text-xs font-bold {{ $bestAttempt->passed ? 'text-emerald-600' : 'text-red-500' }}">
                                    Best: {{ $bestAttempt->percentage }}%
                                </span>
                            @endif
                        </div>

                        <h3 class="font-semibold text-gray-800 mb-2">{{ $quiz->title }}</h3>

                        <div class="grid grid-cols-3 gap-2 mb-4 text-center">
                            <div class="bg-gray-50 rounded-lg p-2">
                                <div class="text-sm font-bold text-gray-800">{{ $quiz->questions_count ?? '?' }}</div>
                                <div class="text-xs text-gray-500">Questions</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-2">
                                <div class="text-sm font-bold text-gray-800">{{ $quiz->time_limit }}m</div>
                                <div class="text-xs text-gray-500">Time Limit</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-2">
                                <div class="text-sm font-bold text-gray-800">{{ $myAttempts->where('status','completed')->count() }}/{{ $quiz->max_attempts }}</div>
                                <div class="text-xs text-gray-500">Attempts</div>
                            </div>
                        </div>

                        @if($inProgress)
                            <a href="{{ route('student.quiz.start', $quiz->id) }}"
                               class="block w-full text-center bg-yellow-500 hover:bg-yellow-600 text-white py-2.5 rounded-xl text-sm font-semibold transition">
                                ▶ Resume Quiz
                            </a>
                        @elseif($canAttempt)
                            <a href="{{ route('student.quiz.start', $quiz->id) }}"
                               class="block w-full text-center bg-primary-600 hover:bg-primary-700 text-white py-2.5 rounded-xl text-sm font-semibold transition">
                                {{ $bestAttempt ? '🔄 Retake Quiz' : '▶ Start Quiz' }}
                            </a>
                        @else
                            <div class="w-full text-center bg-gray-100 text-gray-500 py-2.5 rounded-xl text-sm">
                                Max attempts reached
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $quizzes->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
