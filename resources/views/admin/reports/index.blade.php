@extends('layouts.admin')

@section('title', 'Student Reports - Admin')

@section('admin-content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Student Reports</h1>
        <p class="text-gray-500 text-sm mt-1">Platform-wide performance overview &mdash; {{ number_format($totalCount) }} student{{ $totalCount !== 1 ? 's' : '' }} found</p>
    </div>

    {{-- ── Filters ── --}}
    <form method="GET" data-no-loading
          class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">

        {{-- Search bar (full-width row) --}}
        <div class="mb-4">
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm select-none">&#128269;</span>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Search by student name or email…"
                       class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm
                              focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
            </div>
        </div>

        {{-- Second row: dropdowns + dates --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Course</label>
                <select name="course_id"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white transition">
                    <option value="">All Courses</option>
                    @foreach($courses as $id => $title)
                        <option value="{{ $id }}" {{ $courseId == $id ? 'selected' : '' }}>{{ $title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Student</label>
                <select name="student_id"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white transition">
                    <option value="">All Students</option>
                    @foreach($allStudents as $id => $name)
                        <option value="{{ $id }}" {{ $studentId == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">From</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                       class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm
                              focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">To</label>
                <input type="date" name="date_to" value="{{ $dateTo }}"
                       class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm
                              focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 mt-4">
            <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition">
                Apply Filters
            </button>
            @if($search || $courseId || $studentId || $dateFrom || $dateTo)
                <a href="{{ route('admin.reports') }}"
                   class="px-5 py-2.5 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-xl text-sm font-medium transition">
                    Clear All
                </a>
                <span class="text-xs text-gray-400 ml-1">Filters active</span>
            @endif
        </div>
    </form>

    {{-- ── Summary Cards ── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $summaryCards = [
                ['label' => 'Students (this page)', 'value' => $report->count(),                                        'color' => 'text-gray-800'],
                ['label' => 'Total Enrollments',    'value' => $report->sum('enrolled_courses'),                       'color' => 'text-indigo-600'],
                ['label' => 'Lessons Completed',    'value' => $report->sum('completed_lessons'),                      'color' => 'text-emerald-600'],
                ['label' => 'Avg Quiz Score',       'value' => ($report->count() ? round($report->avg('avg_score'),1) : 0) . '%', 'color' => 'text-amber-600'],
            ];
        @endphp
        @foreach($summaryCards as $card)
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition">
                <div class="text-2xl font-bold {{ $card['color'] }}">{{ $card['value'] }}</div>
                <div class="text-xs text-gray-500 mt-1.5 font-medium">{{ $card['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- ── Report Table ── --}}
    @if($report->isEmpty())
        <div class="bg-white rounded-2xl py-20 text-center shadow-sm border border-gray-100">
            <p class="text-gray-400 text-sm">No students match the current filters.</p>
            @if($search || $courseId || $studentId || $dateFrom || $dateTo)
                <a href="{{ route('admin.reports') }}" class="mt-4 inline-block text-indigo-600 text-sm hover:underline">
                    Clear filters
                </a>
            @endif
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- Mobile: cards --}}
            <div class="md:hidden divide-y divide-gray-100">
                @foreach($report as $row)
                    <div class="p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">{{ $row['name'] }}</p>
                                <p class="text-xs text-gray-400">{{ $row['email'] }}</p>
                            </div>
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ $row['class_level'] }}</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center text-xs pt-1">
                            <div class="bg-gray-50 rounded-lg p-2">
                                <div class="font-bold text-gray-700">{{ $row['enrolled_courses'] }}</div>
                                <div class="text-gray-400 mt-0.5">Enrolled</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-2">
                                <div class="font-bold text-gray-700">{{ $row['completed_lessons'] }}</div>
                                <div class="text-gray-400 mt-0.5">Lessons</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-2">
                                <div class="font-bold {{ $row['avg_score'] >= 60 ? 'text-emerald-600' : ($row['avg_score'] > 0 ? 'text-orange-500' : 'text-gray-400') }}">
                                    {{ $row['avg_score'] > 0 ? $row['avg_score'] . '%' : 'N/A' }}
                                </div>
                                <div class="text-gray-400 mt-0.5">Avg Score</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 pt-1">
                            <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                                <div class="bg-indigo-500 h-1.5 rounded-full transition-all"
                                     style="width: {{ $row['progress_pct'] }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500 w-10 text-right">{{ $row['progress_pct'] }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Desktop: table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-left">
                            <th class="px-5 py-4 font-semibold text-gray-500 uppercase text-xs tracking-wide">#</th>
                            <th class="px-5 py-4 font-semibold text-gray-500 uppercase text-xs tracking-wide">Student</th>
                            <th class="px-5 py-4 font-semibold text-gray-500 uppercase text-xs tracking-wide">Class</th>
                            <th class="px-5 py-4 font-semibold text-gray-500 uppercase text-xs tracking-wide text-center">Courses</th>
                            <th class="px-5 py-4 font-semibold text-gray-500 uppercase text-xs tracking-wide text-center">Lessons</th>
                            <th class="px-5 py-4 font-semibold text-gray-500 uppercase text-xs tracking-wide text-center">Quizzes</th>
                            <th class="px-5 py-4 font-semibold text-gray-500 uppercase text-xs tracking-wide text-center">Avg Score</th>
                            <th class="px-5 py-4 font-semibold text-gray-500 uppercase text-xs tracking-wide">Progress</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($report as $i => $row)
                            <tr class="hover:bg-indigo-50/30 transition-colors group">
                                <td class="px-5 py-4 text-gray-400 tabular-nums">
                                    {{ ($students->currentPage() - 1) * $students->perPage() + $i + 1 }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-medium text-gray-800 group-hover:text-indigo-700 transition-colors">{{ $row['name'] }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $row['email'] }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">
                                        {{ $row['class_level'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center font-semibold text-gray-700">{{ $row['enrolled_courses'] }}</td>
                                <td class="px-5 py-4 text-center font-semibold text-gray-700">{{ $row['completed_lessons'] }}</td>
                                <td class="px-5 py-4 text-center font-semibold text-gray-700">{{ $row['quizzes_taken'] }}</td>
                                <td class="px-5 py-4 text-center">
                                    @if($row['avg_score'] > 0)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                                            {{ $row['avg_score'] >= 60 ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-orange-700' }}">
                                            {{ $row['avg_score'] }}%
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">N/A</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2 min-w-[4rem]">
                                            <div class="bg-indigo-500 h-2 rounded-full transition-all"
                                                 style="width: {{ $row['progress_pct'] }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-600 w-9 text-right tabular-nums">
                                            {{ $row['progress_pct'] }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($students->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-sm text-gray-500">
                        Showing {{ $students->firstItem() }}–{{ $students->lastItem() }} of {{ number_format($students->total()) }} students
                    </p>
                    <div class="text-sm">{{ $students->links() }}</div>
                </div>
            @else
                <div class="px-5 py-3 border-t border-gray-100">
                    <p class="text-sm text-gray-500">{{ $students->total() }} student{{ $students->total() !== 1 ? 's' : '' }} shown</p>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
