@extends('layouts.admin')

@section('title', 'Admin Dashboard — Nabha Learning')

@section('admin-content')
<div class="space-y-6">

    {{-- ── Header ── --}}
    <div class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-2xl p-6 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">Admin Dashboard</h1>
                <p class="text-gray-400 mt-1 text-sm">Nabha Digital Learning — System Overview</p>
            </div>

            {{-- Period selector --}}
            <form method="GET" action="{{ route('admin.dashboard') }}" data-no-loading class="flex items-center gap-1 bg-gray-700/60 rounded-xl p-1">
                @foreach([3 => '3M', 6 => '6M', 12 => '1Y'] as $val => $label)
                    <button type="submit" name="period" value="{{ $val }}"
                            class="px-4 py-1.5 rounded-lg text-sm font-semibold transition
                                   {{ $period === $val
                                      ? 'bg-indigo-600 text-white shadow'
                                      : 'text-gray-300 hover:bg-gray-600 hover:text-white' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </form>
        </div>

        {{-- Alert badges --}}
        <div class="flex flex-wrap gap-2 mt-4">
            @if($stats['pending_lessons'] > 0)
                <a href="{{ route('admin.content') }}"
                   class="inline-flex items-center gap-1.5 bg-yellow-500 hover:bg-yellow-400 text-white text-xs font-semibold px-3 py-1.5 rounded-full transition">
                    {{ $stats['pending_lessons'] }} lesson{{ $stats['pending_lessons'] !== 1 ? 's' : '' }} pending review
                </a>
            @endif
            @if($stats['pending_courses'] > 0)
                <a href="{{ route('admin.courses') }}"
                   class="inline-flex items-center gap-1.5 bg-orange-500 hover:bg-orange-400 text-white text-xs font-semibold px-3 py-1.5 rounded-full transition">
                    {{ $stats['pending_courses'] }} course{{ $stats['pending_courses'] !== 1 ? 's' : '' }} pending review
                </a>
            @endif
            @if($stats['pending_lessons'] === 0 && $stats['pending_courses'] === 0)
                <span class="inline-flex items-center gap-1.5 bg-emerald-600 text-white text-xs font-semibold px-3 py-1.5 rounded-full">
                    All content is up to date
                </span>
            @endif
        </div>
    </div>

    {{-- ── Stats Grid ── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $statCards = [
                ['value' => $stats['total_students'],    'label' => 'Students',         'color' => 'text-blue-600',   'bg' => 'bg-blue-50'],
                ['value' => $stats['total_teachers'],    'label' => 'Teachers',         'color' => 'text-emerald-600','bg' => 'bg-emerald-50'],
                ['value' => $stats['total_courses'],     'label' => 'Total Courses',    'color' => 'text-indigo-600', 'bg' => 'bg-indigo-50'],
                ['value' => $stats['published_courses'], 'label' => 'Published',        'color' => 'text-teal-600',   'bg' => 'bg-teal-50'],
                ['value' => $stats['total_lessons'],     'label' => 'Lessons',          'color' => 'text-purple-600', 'bg' => 'bg-purple-50'],
                ['value' => $stats['pending_lessons'],   'label' => 'Pending Review',   'color' => 'text-yellow-600', 'bg' => 'bg-yellow-50'],
                ['value' => $stats['total_enrollments'], 'label' => 'Enrollments',      'color' => 'text-pink-600',   'bg' => 'bg-pink-50'],
                ['value' => '₹' . number_format($stats['total_revenue'], 0),
                                                         'label' => 'Total Revenue',    'color' => 'text-green-600',  'bg' => 'bg-green-50', 'raw' => true],
            ];
        @endphp
        @foreach($statCards as $card)
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
                <div class="text-2xl font-bold {{ $card['color'] }}">
                    {{ isset($card['raw']) ? $card['value'] : number_format($card['value']) }}
                </div>
                <div class="text-xs text-gray-500 mt-1 font-medium">{{ $card['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- ── Charts Row ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Monthly Registrations --}}
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 lg:col-span-1">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">New Registrations</h3>
                <span class="text-xs text-gray-400">Last {{ $period }} months</span>
            </div>
            <div class="relative" style="height:200px">
                <canvas id="regChart"></canvas>
            </div>
        </div>

        {{-- Monthly Revenue --}}
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 lg:col-span-1">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">Revenue (₹)</h3>
                <span class="text-xs text-gray-400">Last {{ $period }} months</span>
            </div>
            <div class="relative" style="height:200px">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- User Role Distribution --}}
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 lg:col-span-1">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">User Distribution</h3>
                <span class="text-xs text-gray-400">All time</span>
            </div>
            <div class="relative" style="height:200px">
                <canvas id="roleChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Tables Row ── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        {{-- Recent Users --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Recent Users</h3>
                <a href="{{ route('admin.users') }}" class="text-xs text-indigo-600 hover:underline font-medium">View All →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($recentUsers as $user)
                    <div class="px-5 py-3 flex items-center gap-3 hover:bg-gray-50 transition-colors">
                        <img src="{{ $user->avatar_url }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0" alt="">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $user->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $user->email }}</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium whitespace-nowrap
                            {{ $user->role === 'admin'   ? 'bg-gray-100 text-gray-700'
                             : ($user->role === 'teacher' ? 'bg-emerald-100 text-emerald-700'
                             : 'bg-blue-100 text-blue-700') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Recent Courses --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Recent Courses</h3>
                <a href="{{ route('admin.courses') }}" class="text-xs text-indigo-600 hover:underline font-medium">Manage →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($recentCourses as $course)
                    <div class="px-5 py-3 flex items-center gap-3 hover:bg-gray-50 transition-colors">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $course->title }}</p>
                            <p class="text-xs text-gray-400">{{ $course->teacher->name }} · {{ $course->enrollments_count }} enrolled</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium whitespace-nowrap
                            {{ $course->status === 'published' ? 'bg-emerald-100 text-emerald-700'
                             : ($course->status === 'pending'   ? 'bg-yellow-100 text-yellow-700'
                             : ($course->status === 'draft'     ? 'bg-gray-100   text-gray-600'
                             : 'bg-red-100 text-red-700')) }}">
                            {{ ucfirst($course->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Recent Lessons --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Recent Lessons</h3>
                <a href="{{ route('admin.content') }}" class="text-xs text-indigo-600 hover:underline font-medium">Review All →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($recentLessons as $lesson)
                    <div class="px-5 py-3 flex items-center gap-3 hover:bg-gray-50 transition-colors">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $lesson->title }}</p>
                            <p class="text-xs text-gray-400">by {{ $lesson->teacher->name }}</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium whitespace-nowrap
                            {{ $lesson->status === 'published' ? 'bg-emerald-100 text-emerald-700'
                             : ($lesson->status === 'pending'  ? 'bg-yellow-100 text-yellow-700'
                             : 'bg-red-100 text-red-700') }}">
                            {{ ucfirst($lesson->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const regData     = @json($monthlyRegistrations);
const revenueData = @json($monthlyRevenue);
const roleData    = @json($roleDistribution);

/* ── Shared chart defaults ── */
Chart.defaults.font.family = 'ui-sans-serif, system-ui, sans-serif';
Chart.defaults.font.size   = 11;
Chart.defaults.color       = '#6b7280';

const gridColor  = '#f3f4f6';
const baseOpts   = (yLabel) => ({
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: 'index', intersect: false },
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: '#1f2937',
            titleColor: '#f9fafb',
            bodyColor: '#d1d5db',
            padding: 10,
            cornerRadius: 8,
            displayColors: false,
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: { color: gridColor },
            ticks: { precision: 0 },
            title: yLabel ? { display: false } : undefined,
        },
        x: { grid: { display: false } },
    },
});

/* ── Registrations bar chart ── */
new Chart(document.getElementById('regChart'), {
    type: 'bar',
    data: {
        labels: Object.keys(regData),
        datasets: [{
            label: 'Registrations',
            data: Object.values(regData),
            backgroundColor: 'rgba(99, 102, 241, 0.8)',
            hoverBackgroundColor: 'rgba(99, 102, 241, 1)',
            borderRadius: 6,
            borderSkipped: false,
        }],
    },
    options: baseOpts(),
});

/* ── Revenue area chart ── */
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: Object.keys(revenueData),
        datasets: [{
            label: 'Revenue (₹)',
            data: Object.values(revenueData),
            borderColor: 'rgba(16, 185, 129, 1)',
            backgroundColor: (ctx) => {
                const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, ctx.chart.height);
                g.addColorStop(0,   'rgba(16,185,129,.25)');
                g.addColorStop(1,   'rgba(16,185,129,.02)');
                return g;
            },
            fill: true,
            tension: 0.45,
            pointBackgroundColor: 'rgba(16, 185, 129, 1)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
        }],
    },
    options: baseOpts(),
});

/* ── Role donut ── */
new Chart(document.getElementById('roleChart'), {
    type: 'doughnut',
    data: {
        labels: Object.keys(roleData),
        datasets: [{
            data: Object.values(roleData),
            backgroundColor: [
                'rgba(59,  130, 246, .85)',
                'rgba(16,  185, 129, .85)',
                'rgba(107, 114, 128, .85)',
            ],
            hoverBackgroundColor: [
                'rgba(59,  130, 246, 1)',
                'rgba(16,  185, 129, 1)',
                'rgba(107, 114, 128, 1)',
            ],
            borderWidth: 3,
            borderColor: '#fff',
            hoverBorderWidth: 3,
        }],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '68%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: { boxWidth: 10, padding: 14, usePointStyle: true, pointStyleWidth: 8 },
            },
            tooltip: {
                backgroundColor: '#1f2937',
                titleColor: '#f9fafb',
                bodyColor: '#d1d5db',
                padding: 10,
                cornerRadius: 8,
            },
        },
    },
});
</script>
@endpush
@endsection
