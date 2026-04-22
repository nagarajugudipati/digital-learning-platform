@extends('layouts.teacher')

@section('title', 'Analytics - Nabha Learning')

@section('teacher-content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Analytics Dashboard</h1>
        <p class="text-gray-500 text-sm mt-1">Track student engagement and performance</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-gray-800">{{ number_format($totalViews) }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Lesson Views</div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-gray-800">{{ number_format($totalDownloads) }}</div>
            <div class="text-xs text-gray-500 mt-1">Downloads</div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-gray-800">{{ number_format($totalAttempts) }}</div>
            <div class="text-xs text-gray-500 mt-1">Quiz Attempts</div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-gray-800">{{ round($avgQuizScore, 1) }}%</div>
            <div class="text-xs text-gray-500 mt-1">Avg. Quiz Score</div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4">Weekly Quiz Attempts</h3>
            <canvas id="weeklyChart" height="220"></canvas>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4">Quiz Performance</h3>
            @if($quizPerformance->isEmpty())
                <div class="flex items-center justify-center h-40 text-gray-400 text-sm">No quiz data yet</div>
            @else
                <canvas id="quizChart" height="220"></canvas>
            @endif
        </div>
    </div>

    <!-- Lesson Engagement Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Lesson Engagement</h3>
        </div>
        @if($lessonEngagement->isEmpty())
            <div class="p-8 text-center text-gray-400 text-sm">No lesson data yet. Upload lessons to see engagement.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Lesson</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Subject</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Views</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Downloads</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Students</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($lessonEngagement as $lesson)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ Str::limit($lesson->title, 35) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $lesson->subject }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $lesson->view_count }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $lesson->download_count }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $lesson->student_views }}</td>
                                <td class="px-4 py-3">
                                    <span class="text-xs px-2 py-0.5 rounded-full
                                        {{ $lesson->status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ ucfirst($lesson->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
const weeklyData = @json($weeklyAttempts);
const quizData = @json($quizPerformance->values());

new Chart(document.getElementById('weeklyChart'), {
    type: 'line',
    data: {
        labels: Object.keys(weeklyData),
        datasets: [{
            label: 'Quiz Attempts',
            data: Object.values(weeklyData),
            borderColor: 'rgb(16, 185, 129)',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            fill: true,
            tension: 0.4,
            borderWidth: 2,
            pointBackgroundColor: 'rgb(16, 185, 129)',
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

@if($quizPerformance->isNotEmpty())
const quizCanvas = document.getElementById('quizChart');
if (quizCanvas) {
    new Chart(quizCanvas, {
        type: 'bar',
        data: {
            labels: quizData.map(q => q.title.substring(0, 20) + '...'),
            datasets: [
                {
                    label: 'Avg Score %',
                    data: quizData.map(q => q.avg_score),
                    backgroundColor: 'rgba(79, 70, 229, 0.8)',
                    borderRadius: 4,
                },
                {
                    label: 'Pass Rate %',
                    data: quizData.map(q => q.pass_rate),
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, max: 100, grid: { color: '#f3f4f6' } },
                x: { grid: { display: false } }
            }
        }
    });
}
@endif
</script>
@endpush
@endsection
