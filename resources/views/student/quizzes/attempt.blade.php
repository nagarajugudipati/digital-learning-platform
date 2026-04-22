@extends('layouts.app')

@section('title', 'Quiz: ' . $quiz->title)

@section('content')
<div class="min-h-screen bg-gray-50 py-6 px-4" x-data="quizApp()" x-init="startTimer()">
    <!-- Quiz Header -->
    <div class="max-w-3xl mx-auto">
        <div class="bg-primary-700 text-white rounded-2xl p-5 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold">{{ $quiz->title }}</h1>
                    <p class="text-primary-200 text-sm mt-1">{{ $quiz->subject }} | {{ $quiz->class_level }}</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold font-mono"
                         :class="timeLeft <= 60 ? 'text-red-300 animate-pulse' : 'text-yellow-300'"
                         x-text="formatTime(timeLeft)">
                        {{ sprintf('%02d:%02d', $quiz->time_limit, 0) }}
                    </div>
                    <div class="text-xs text-primary-300 mt-1">Time Remaining</div>
                </div>
            </div>
            <!-- Progress bar -->
            <div class="mt-4 bg-primary-900 rounded-full h-2">
                <div class="bg-yellow-400 h-2 rounded-full transition-all"
                     :style="`width: ${(currentQ + 1) / totalQ * 100}%`"></div>
            </div>
            <div class="flex justify-between text-xs text-primary-300 mt-1">
                <span>Question <span x-text="currentQ + 1"></span> of {{ $questions->count() }}</span>
                <span>{{ $quiz->total_marks }} total marks</span>
            </div>
        </div>

        <!-- Quiz Form -->
        <form id="quiz-form" method="POST" action="{{ route('student.quiz.submit', $quiz->id) }}">
            @csrf

            @foreach($questions as $index => $question)
                <div class="question-card bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-4"
                     data-question-id="{{ $question->id }}"
                     data-question-type="{{ $question->type ?? 'mcq' }}"
                     x-show="currentQ === {{ $index }}"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0">

                    <div class="flex items-start gap-3 mb-5">
                        <span class="flex-shrink-0 w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center text-sm font-bold">{{ $index + 1 }}</span>
                        <div class="flex-1">
                            <p class="text-gray-800 font-medium leading-relaxed">{{ $question->question_text }}</p>
                            @php $qType = $question->type ?? 'mcq'; @endphp
                            <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full
                                {{ $qType === 'mcq' ? 'bg-blue-100 text-blue-700' : ($qType === 'true_false' ? 'bg-purple-100 text-purple-700' : 'bg-orange-100 text-orange-700') }}">
                                {{ $qType === 'mcq' ? 'Multiple Choice' : ($qType === 'true_false' ? 'True / False' : 'Text Answer') }}
                            </span>
                        </div>
                    </div>

                    @if($qType === 'mcq')
                        <div class="space-y-3">
                            @foreach($question->getOptionsForDisplay() as $key => $option)
                                <label class="flex items-center gap-3 p-3.5 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50 group">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $key }}"
                                           class="quiz-radio w-4 h-4 text-primary-600 cursor-pointer"
                                           onchange="trackAnswer({{ $question->id }})">
                                    <span class="flex-shrink-0 w-6 h-6 rounded-full border-2 border-gray-300 group-has-[:checked]:border-primary-600 bg-white flex items-center justify-center text-xs font-bold text-gray-500 group-has-[:checked]:text-primary-600">
                                        {{ strtoupper($key) }}
                                    </span>
                                    <span class="text-gray-700 text-sm">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>

                    @elseif($qType === 'true_false')
                        <div class="space-y-3">
                            @foreach(['a' => 'True', 'b' => 'False'] as $key => $label)
                                <label class="flex items-center gap-3 p-3.5 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50 group">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $key }}"
                                           class="quiz-radio w-4 h-4 text-primary-600 cursor-pointer"
                                           onchange="trackAnswer({{ $question->id }})">
                                    <span class="flex-shrink-0 w-6 h-6 rounded-full border-2 border-gray-300 group-has-[:checked]:border-primary-600 bg-white flex items-center justify-center text-xs font-bold text-gray-500 group-has-[:checked]:text-primary-600">
                                        {{ strtoupper($key) }}
                                    </span>
                                    <span class="text-gray-700 text-sm font-medium">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>

                    @else
                        {{-- Text Answer --}}
                        <div>
                            <label class="block text-sm text-gray-600 mb-2">Your Answer:</label>
                            <input type="text"
                                   name="answers[{{ $question->id }}]"
                                   class="quiz-text w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 text-sm"
                                   placeholder="Type your answer here..."
                                   oninput="trackAnswer({{ $question->id }})">
                            <p class="text-xs text-gray-400 mt-1">Answer is checked case-insensitively.</p>
                        </div>
                    @endif
                </div>
            @endforeach

            <!-- Navigation -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center justify-between gap-3">
                <button type="button" @click="prevQ()" x-show="currentQ > 0"
                        class="px-5 py-2.5 border border-gray-300 text-gray-600 rounded-xl hover:bg-gray-50 transition text-sm font-medium">
                    ← Previous
                </button>
                <div class="flex-1 text-center">
                    <span class="text-sm text-gray-500">
                        <span x-text="answeredCount"></span> of {{ $questions->count() }} answered
                    </span>
                </div>
                <button type="button" @click="nextQ()" x-show="currentQ < totalQ - 1"
                        class="px-5 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition text-sm font-medium">
                    Next →
                </button>
                <button type="button" x-show="currentQ === totalQ - 1"
                        @click="confirmSubmit()"
                        class="px-5 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition text-sm font-semibold">
                    Submit Quiz
                </button>
            </div>

            <!-- Question Navigator -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mt-4">
                <p class="text-xs text-gray-500 mb-3">Jump to question:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($questions as $i => $q)
                        <button type="button" @click="goTo({{ $i }})"
                                :class="currentQ === {{ $i }} ? 'bg-primary-600 text-white' : (isAnswered({{ $q->id }}) ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600')"
                                class="w-9 h-9 rounded-lg text-sm font-semibold transition hover:opacity-80">
                            {{ $i + 1 }}
                        </button>
                    @endforeach
                </div>
                <div class="flex items-center gap-4 mt-3 text-xs text-gray-500">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-primary-600 rounded inline-block"></span> Current</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-emerald-100 border border-emerald-300 rounded inline-block"></span> Answered</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-gray-100 border border-gray-300 rounded inline-block"></span> Unanswered</span>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Track answered questions by question ID
const answeredSet = new Set();

function trackAnswer(questionId) {
    answeredSet.add(questionId);
    // Notify Alpine to recompute
    window.dispatchEvent(new CustomEvent('answer-updated'));
}

function isQuestionAnswered(questionId) {
    // Check radio button
    const radio = document.querySelector(`input[name="answers[${questionId}]"][type="radio"]:checked`);
    if (radio) return true;
    // Check text input
    const text = document.querySelector(`input[name="answers[${questionId}]"][type="text"]`);
    if (text && text.value.trim() !== '') return true;
    return false;
}

function quizApp() {
    return {
        currentQ: 0,
        totalQ: {{ $questions->count() }},
        timeLeft: {{ $quiz->time_limit * 60 }},
        answeredCount: 0,

        startTimer() {
            this.updateAnsweredCount();
            window.addEventListener('answer-updated', () => this.updateAnsweredCount());

            const interval = setInterval(() => {
                this.timeLeft--;
                if (this.timeLeft <= 0) {
                    clearInterval(interval);
                    document.getElementById('quiz-form').submit();
                }
            }, 1000);
        },

        updateAnsweredCount() {
            const cards = document.querySelectorAll('.question-card');
            let count = 0;
            cards.forEach(card => {
                const qId = card.dataset.questionId;
                if (isQuestionAnswered(qId)) count++;
            });
            this.answeredCount = count;
        },

        formatTime(seconds) {
            const m = Math.floor(seconds / 60).toString().padStart(2, '0');
            const s = (seconds % 60).toString().padStart(2, '0');
            return `${m}:${s}`;
        },

        nextQ() { if (this.currentQ < this.totalQ - 1) this.currentQ++; },
        prevQ() { if (this.currentQ > 0) this.currentQ--; },
        goTo(i) { this.currentQ = i; },

        isAnswered(questionId) {
            return isQuestionAnswered(questionId);
        },

        confirmSubmit() {
            this.updateAnsweredCount();
            const unanswered = this.totalQ - this.answeredCount;

            if (unanswered > 0) {
                if (!confirm(`You have ${unanswered} unanswered question(s). Submit anyway?`)) return;
            }

            document.getElementById('quiz-form').submit();
        }
    }
}
</script>
@endpush
@endsection
