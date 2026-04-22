@extends('layouts.student')

@section('title', 'Student Dashboard - Nabha Learning')

@section('student-content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-primary-600 to-indigo-600 rounded-2xl p-6 text-white">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold">Welcome, {{ auth()->user()->name }}!</h1>
                <p class="text-primary-200 mt-1">{{ auth()->user()->class_level }} | {{ auth()->user()->school }}</p>
                <p class="text-sm mt-3 text-primary-100">Keep learning — every lesson brings you closer to your goals!</p>
            </div>
            @if(auth()->user()->streak_count > 0)
                <div class="flex-shrink-0 bg-white bg-opacity-20 rounded-xl px-4 py-3 text-center">
                    <div class="text-2xl font-bold">🔥 {{ auth()->user()->streak_count }}</div>
                    <div class="text-xs text-primary-100 mt-0.5">Day Streak</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-gray-800">{{ $lessonsCompleted }}</div>
            <div class="text-xs text-gray-500 mt-1">Lessons Completed</div>
            <div class="text-xs text-primary-600 mt-1">of {{ $totalLessons }} available</div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-gray-800">{{ $quizAttempts->count() }}</div>
            <div class="text-xs text-gray-500 mt-1">Quizzes Attempted</div>
            <div class="text-xs text-emerald-600 mt-1">{{ $quizAttempts->where('passed', true)->count() }} passed</div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-gray-800">{{ round($avgScore, 1) }}%</div>
            <div class="text-xs text-gray-500 mt-1">Average Score</div>
            <div class="text-xs {{ $avgScore >= 60 ? 'text-emerald-600' : 'text-orange-500' }} mt-1">
                {{ $avgScore >= 60 ? 'Good Performance' : 'Keep Practicing' }}
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-gray-800">{{ $totalQuizzes }}</div>
            <div class="text-xs text-gray-500 mt-1">Quizzes Available</div>
            <div class="text-xs text-primary-600 mt-1">Take a quiz now!</div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4">Weekly Activity</h3>
            <canvas id="weeklyChart" height="200"></canvas>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4">Subject Performance</h3>
            <canvas id="subjectChart" height="200"></canvas>
        </div>
    </div>

    <!-- Recent Lessons -->
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800">Recently Accessed Lessons</h3>
            <a href="{{ route('student.lessons') }}" class="text-sm text-primary-600 hover:underline">View All →</a>
        </div>
        @if($recentLessons->isEmpty())
            <div class="text-center py-8">
                <p class="text-gray-500 text-sm">No lessons accessed yet.</p>
                <a href="{{ route('student.lessons') }}" class="mt-3 inline-block bg-primary-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-primary-700 transition">
                    Browse Lessons
                </a>
            </div>
        @else
            <div class="space-y-3">
                @foreach($recentLessons as $report)
                    @if($report->lesson)
                    <a href="{{ route('student.lesson.show', $report->lesson_id) }}"
                       class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-lg transition group">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm text-gray-800 truncate group-hover:text-primary-600">{{ $report->lesson->title }}</p>
                            <p class="text-xs text-gray-500">{{ $report->lesson->subject }} | {{ $report->last_accessed?->diffForHumans() }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            @if($report->is_completed)
                                <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">Done</span>
                            @else
                                <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">In Progress</span>
                            @endif
                        </div>
                    </a>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    <!-- Quick Action Links -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('student.lessons') }}" class="bg-primary-600 text-white rounded-xl p-4 text-center hover:bg-primary-700 transition">
            <div class="text-sm font-semibold">View Lessons</div>
        </a>
        <a href="{{ route('student.quizzes') }}" class="bg-emerald-600 text-white rounded-xl p-4 text-center hover:bg-emerald-700 transition">
            <div class="text-sm font-semibold">Take a Quiz</div>
        </a>
        <a href="{{ route('student.chatbot') }}" class="bg-purple-600 text-white rounded-xl p-4 text-center hover:bg-purple-700 transition">
            <div class="text-sm font-semibold">Ask AI Bot</div>
        </a>
        <div class="bg-orange-500 text-white rounded-xl p-4 text-center">
            <div class="text-sm font-semibold">Works Offline</div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const weeklyData = @json($progressByWeek);
const subjectData = @json($subjectScores);

new Chart(document.getElementById('weeklyChart'), {
    type: 'bar',
    data: {
        labels: Object.keys(weeklyData),
        datasets: [{
            label: 'Lessons Accessed',
            data: Object.values(weeklyData),
            backgroundColor: 'rgba(79, 70, 229, 0.8)',
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f3f4f6' } },
            x: { grid: { display: false } }
        }
    }
});

new Chart(document.getElementById('subjectChart'), {
    type: 'radar',
    data: {
        labels: Object.keys(subjectData),
        datasets: [{
            label: 'Score %',
            data: Object.values(subjectData),
            backgroundColor: 'rgba(79, 70, 229, 0.2)',
            borderColor: 'rgba(79, 70, 229, 1)',
            borderWidth: 2,
            pointBackgroundColor: 'rgba(79, 70, 229, 1)',
        }]
    },
    options: {
        responsive: true,
        scales: { r: { beginAtZero: true, max: 100, ticks: { stepSize: 20 } } },
        plugins: { legend: { display: false } }
    }
});
</script>
@endpush
@endsection
