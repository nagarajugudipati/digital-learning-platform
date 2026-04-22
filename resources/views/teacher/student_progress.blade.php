@extends('layouts.teacher')

@section('title', 'Student Progress - Nabha Learning')

@section('teacher-content')
<div class="space-y-6">

    {{-- Page header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Student Progress</h1>
            <p class="text-gray-500 text-sm mt-1">Performance of students who accessed your lessons</p>
        </div>
        @if(!$students->isEmpty())
            <a href="{{ route('teacher.student.progress.export') }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition">
                Export CSV
            </a>
        @endif
    </div>

    @if($students->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-gray-100">
            <p class="text-gray-500">No students have accessed your lessons yet.</p>
            <p class="text-sm text-gray-400 mt-1">Students who view or complete your lessons will appear here.</p>
        </div>
    @else

        {{-- Summary cards --}}
        @php
            $totalStudents   = $students->count();
            $avgClassScore   = $students->avg('avg_score');
            $avgProgress     = $students->avg('progress_pct');
            $fullyCompleted  = $students->where('progress_pct', 100)->count();
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
                <div class="text-2xl font-bold text-emerald-600">{{ $totalStudents }}</div>
                <div class="text-xs text-gray-500 mt-1">Students Reached</div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
                <div class="text-2xl font-bold text-blue-600">{{ round($avgClassScore, 1) }}%</div>
                <div class="text-xs text-gray-500 mt-1">Avg Quiz Score</div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
                <div class="text-2xl font-bold text-purple-600">{{ round($avgProgress, 1) }}%</div>
                <div class="text-xs text-gray-500 mt-1">Avg Lesson Progress</div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
                <div class="text-2xl font-bold text-orange-500">{{ $fullyCompleted }}</div>
                <div class="text-xs text-gray-500 mt-1">Fully Completed</div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <h2 class="font-semibold text-gray-700 mb-4">Quiz Score by Student</h2>
                <div class="relative" style="height: 260px;">
                    <canvas id="scoreChart"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <h2 class="font-semibold text-gray-700 mb-4">Lesson Completion Progress</h2>
                <div class="relative" style="height: 260px;">
                    <canvas id="progressChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Student Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800">Student Details</h2>
                <span class="text-xs text-gray-400">{{ $totalLessons }} lesson(s) total</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">#</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">Student</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">Class</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-600">Lessons Done</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-600">Quizzes</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-600">Avg Score</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">Progress</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($students as $i => $student)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3 text-gray-400">{{ $i + 1 }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">
                                            {{ strtoupper(substr($student['name'], 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-gray-800">{{ $student['name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ $student['class_level'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-semibold text-gray-800">{{ $student['completed_lessons'] }}</span>
                                    <span class="text-gray-400">/ {{ $student['total_lessons'] }}</span>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-700">{{ $student['quizzes_taken'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    @php $score = $student['avg_score']; @endphp
                                    <span class="font-semibold
                                        {{ $score >= 75 ? 'text-emerald-600' : ($score >= 50 ? 'text-yellow-600' : 'text-red-500') }}">
                                        {{ $score > 0 ? $score . '%' : '—' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 min-w-[140px]">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden">
                                            <div class="h-2 rounded-full transition-all
                                                {{ $student['progress_pct'] >= 75 ? 'bg-emerald-500'
                                                 : ($student['progress_pct'] >= 40 ? 'bg-yellow-400'
                                                 : 'bg-red-400') }}"
                                                 style="width: {{ $student['progress_pct'] }}%">
                                            </div>
                                        </div>
                                        <span class="text-xs text-gray-500 w-9 text-right">{{ $student['progress_pct'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @endif
</div>

@push('scripts')
<script>
    const labels   = @json($chartLabels);
    const scores   = @json($chartScores);
    const progress = @json($chartProgress);

    const scoreCtx = document.getElementById('scoreChart');
    if (scoreCtx) {
        new Chart(scoreCtx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Avg Quiz Score (%)',
                    data: scores,
                    backgroundColor: 'rgba(99, 102, 241, 0.7)',
                    borderColor: 'rgba(99, 102, 241, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, max: 100, grid: { color: '#f3f4f6' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    const progCtx = document.getElementById('progressChart');
    if (progCtx) {
        new Chart(progCtx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Lesson Completion (%)',
                    data: progress,
                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, max: 100, grid: { color: '#f3f4f6' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
</script>
@endpush
@endsection
