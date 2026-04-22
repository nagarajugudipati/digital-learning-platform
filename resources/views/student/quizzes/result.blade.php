@extends('layouts.student')

@section('title', 'Quiz Result - Nabha Learning')

@section('student-content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Result Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
        <div class="text-6xl mb-4">
            {{ $attempt->percentage >= 90 ? '🏆' : ($attempt->percentage >= 60 ? '🎉' : ($attempt->passed ? '✅' : '📚')) }}
        </div>
        <h1 class="text-3xl font-bold text-gray-800 mb-1">
            {{ $attempt->passed ? 'Well Done!' : 'Keep Practicing!' }}
        </h1>
        <p class="text-gray-500">{{ $attempt->quiz->title }}</p>

        <!-- Score Circle -->
        <div class="mt-6 flex justify-center">
            <div class="relative w-40 h-40">
                <svg class="w-40 h-40 -rotate-90" viewBox="0 0 160 160">
                    <circle cx="80" cy="80" r="70" fill="none" stroke="#f3f4f6" stroke-width="12"/>
                    <circle cx="80" cy="80" r="70" fill="none"
                            stroke="{{ $attempt->passed ? '#10b981' : '#ef4444' }}"
                            stroke-width="12"
                            stroke-linecap="round"
                            stroke-dasharray="{{ 440 }}"
                            stroke-dashoffset="{{ 440 - (440 * $attempt->percentage / 100) }}"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-3xl font-bold text-gray-800">{{ $attempt->percentage }}%</span>
                    <span class="text-sm text-gray-500">{{ $attempt->score }}/{{ $attempt->total_marks }}</span>
                    <span class="text-lg font-bold {{ $attempt->passed ? 'text-emerald-600' : 'text-red-500' }}">{{ $attempt->grade }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-100">
            <div>
                <div class="text-xl font-bold text-gray-800">{{ $attempt->score }}</div>
                <div class="text-xs text-gray-500">Marks Earned</div>
            </div>
            <div>
                <div class="text-xl font-bold text-gray-800">{{ $attempt->time_taken_formatted }}</div>
                <div class="text-xs text-gray-500">Time Taken</div>
            </div>
            <div>
                <div class="text-xl font-bold {{ $attempt->passed ? 'text-emerald-600' : 'text-red-500' }}">
                    {{ $attempt->passed ? 'PASS' : 'FAIL' }}
                </div>
                <div class="text-xs text-gray-500">Result (Pass ≥ {{ $attempt->quiz->passing_marks }}%)</div>
            </div>
        </div>
    </div>

    <!-- Question Review -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-5">Detailed Review</h2>
        <div class="space-y-5">
            @foreach($attempt->quiz->questions as $index => $question)
                @php
                    $answerData = $attempt->answers[$question->id] ?? null;
                    $isCorrect = $answerData['is_correct'] ?? false;
                    $givenAnswer = $answerData['given'] ?? null;
                @endphp
                <div class="border rounded-xl p-4 {{ $isCorrect ? 'border-emerald-200 bg-emerald-50' : 'border-red-200 bg-red-50' }}">
                    <div class="flex items-start gap-3">
                        <span class="{{ $isCorrect ? 'text-emerald-600' : 'text-red-500' }} text-xl flex-shrink-0">
                            {{ $isCorrect ? '✓' : '✗' }}
                        </span>
                        <div class="flex-1">
                            <p class="font-medium text-gray-800 text-sm">Q{{ $index + 1 }}. {{ $question->question_text }}</p>
                            <div class="mt-2 text-sm space-y-1">
                                @if($givenAnswer && !$isCorrect)
                                    <p class="text-red-600">Your answer: <strong>{{ strtoupper($givenAnswer) }}) {{ $question->getOptionAttribute($givenAnswer) }}</strong></p>
                                @elseif(!$givenAnswer)
                                    <p class="text-gray-500 italic">Not answered</p>
                                @endif
                                <p class="text-emerald-700">Correct: <strong>{{ strtoupper($question->correct_answer) }}) {{ $question->correct_answer_text }}</strong></p>
                                @if($question->explanation)
                                    <p class="text-gray-600 text-xs mt-2 bg-white bg-opacity-60 p-2 rounded-lg">💡 {{ $question->explanation }}</p>
                                @endif
                            </div>
                        </div>
                        <span class="text-xs font-bold {{ $isCorrect ? 'text-emerald-600' : 'text-gray-400' }}">
                            +{{ $isCorrect ? $question->marks : 0 }}/{{ $question->marks }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Actions -->
    <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ route('student.quizzes') }}" class="flex-1 text-center bg-primary-600 text-white py-3 rounded-xl font-semibold hover:bg-primary-700 transition">
            ← Back to Quizzes
        </a>
        @if($attempt->quiz->canAttempt(auth()->id()))
            <a href="{{ route('student.quiz.start', $attempt->quiz_id) }}" class="flex-1 text-center border border-primary-600 text-primary-600 py-3 rounded-xl font-semibold hover:bg-primary-50 transition">
                🔄 Retake Quiz
            </a>
        @endif
        <a href="{{ route('student.chatbot') }}" class="flex-1 text-center bg-purple-600 text-white py-3 rounded-xl font-semibold hover:bg-purple-700 transition">
            🤖 Ask AI Chatbot
        </a>
    </div>
</div>
@endsection
