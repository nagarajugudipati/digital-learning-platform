@extends('layouts.admin')

@section('title', 'Student Analytics — Nabha Learning')

@section('admin-content')
<div class="space-y-6">

    {{-- ── Header ── --}}
    <div class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-2xl p-6 text-white">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}"
               class="p-2 rounded-lg bg-gray-700/60 hover:bg-gray-700 transition text-gray-300 hover:text-white flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold">Student Analytics</h1>
                <p class="text-gray-400 mt-0.5 text-sm">Enrollments &amp; assigned teachers per student</p>
            </div>
        </div>
    </div>

    {{-- ── Summary Stats ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-blue-600">{{ number_format($totalStudents) }}</div>
            <div class="text-xs text-gray-500 mt-1 font-medium">Total Students</div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-indigo-600">{{ number_format($totalEnrollments) }}</div>
            <div class="text-xs text-gray-500 mt-1 font-medium">Total Enrollments</div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-emerald-600">{{ number_format($activeStudents) }}</div>
            <div class="text-xs text-gray-500 mt-1 font-medium">Active Students</div>
        </div>
    </div>

    {{-- ── Filters ── --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('admin.students-analytics') }}" data-no-loading>
            <div class="flex flex-col sm:flex-row gap-3">

                {{-- Search --}}
                <div class="flex-1">
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Search students by name or email…"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition">
                </div>

                {{-- Student dropdown drill-down --}}
                <div class="sm:w-72">
                    <select name="student_id" onchange="this.form.submit()"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white transition">
                        <option value="">— Select Student to Drill Down —</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}" {{ request('student_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->name }} ({{ $s->enrollments_count }} enrolment{{ $s->enrollments_count !== 1 ? 's' : '' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">
                    Search
                </button>

                @if($search || request('student_id'))
                    <a href="{{ route('admin.students-analytics') }}"
                       class="px-5 py-2.5 border border-gray-200 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 transition text-center whitespace-nowrap">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ── Students Table ── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">
                All Students
                @if($search)
                    <span class="ml-2 text-sm text-gray-400 font-normal">— "{{ $search }}"</span>
                @endif
            </h3>
            <span class="text-xs text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full font-medium">
                {{ $students->total() }} student{{ $students->total() !== 1 ? 's' : '' }}
            </span>
        </div>

        @if($students->isEmpty())
            <div class="px-5 py-14 text-center text-gray-400">
                <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
                <p class="text-sm">No students found{{ $search ? ' for "'.$search.'"' : '' }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Student</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Email</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Enrolled</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Assigned Teachers</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Drill Down</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($students as $student)
                            @php $isSelected = request('student_id') == $student->id; @endphp
                            <tr class="transition-colors {{ $isSelected ? 'bg-blue-50/80' : 'hover:bg-gray-50/70' }}">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $student->avatar_url }}" alt=""
                                             class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                                        <div>
                                            <span class="font-medium text-gray-800 block">{{ $student->name }}</span>
                                            @if($student->class_level)
                                                <span class="text-xs text-gray-400">Class {{ $student->class_level }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-gray-500 hidden sm:table-cell">{{ $student->email }}</td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="font-bold text-indigo-600 text-base">{{ $student->enrollments_count }}</span>
                                </td>
                                <td class="px-5 py-3.5 hidden md:table-cell">
                                    @if($student->unique_teachers->isEmpty())
                                        <span class="text-gray-400 text-xs">—</span>
                                    @else
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($student->unique_teachers->take(3) as $teacher)
                                                <span class="text-xs bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-md font-medium">
                                                    {{ $teacher->name }}
                                                </span>
                                            @endforeach
                                            @if($student->unique_teachers->count() > 3)
                                                <span class="text-xs text-gray-400 py-0.5 self-center">
                                                    +{{ $student->unique_teachers->count() - 3 }} more
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @if($isSelected)
                                        <span class="text-xs font-semibold text-blue-500 bg-blue-100 px-2.5 py-1 rounded-full">
                                            Selected ↓
                                        </span>
                                    @else
                                        <a href="{{ route('admin.students-analytics', array_filter(['student_id' => $student->id, 'search' => $search])) }}"
                                           class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                                            View Details →
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($students->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $students->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- ── Student Drill-Down ── --}}
    @if($selectedStudent)
    <div id="student-detail" class="space-y-4 scroll-mt-6">

        {{-- Student Info Card --}}
        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex flex-col sm:flex-row items-start gap-5">
                <img src="{{ $selectedStudent->avatar_url }}" alt=""
                     class="w-16 h-16 rounded-2xl border-2 border-white/30 flex-shrink-0 object-cover">
                <div class="flex-1 min-w-0">
                    <h2 class="text-xl font-bold">{{ $selectedStudent->name }}</h2>
                    <p class="text-blue-100 text-sm mt-0.5">{{ $selectedStudent->email }}</p>
                    <div class="flex flex-wrap gap-4 mt-2 text-xs text-blue-100">
                        @if($selectedStudent->class_level)
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                Class {{ $selectedStudent->class_level }}
                            </span>
                        @endif
                        @if($selectedStudent->school)
                            <span>{{ $selectedStudent->school }}</span>
                        @endif
                        @if($selectedStudent->phone)
                            <span>{{ $selectedStudent->phone }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex gap-4 text-center">
                    <div class="bg-white/10 rounded-xl px-4 py-3">
                        <div class="text-2xl font-bold">{{ $studentEnrollments->count() }}</div>
                        <div class="text-xs text-blue-100 mt-0.5">Courses</div>
                    </div>
                    <div class="bg-white/10 rounded-xl px-4 py-3">
                        <div class="text-2xl font-bold">{{ $studentTeachers->count() }}</div>
                        <div class="text-xs text-blue-100 mt-0.5">Teachers</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Teachers Grid --}}
        @if($studentTeachers->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">
                    Teachers assigned to <span class="text-blue-600">{{ $selectedStudent->name }}</span>
                </h3>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($studentTeachers as $teacher)
                    <div class="flex items-center gap-3 p-3.5 bg-gray-50 rounded-xl border border-gray-100 hover:border-emerald-200 hover:bg-emerald-50/30 transition">
                        <img src="{{ $teacher->avatar_url }}" alt=""
                             class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800 text-sm truncate">{{ $teacher->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $teacher->email }}</p>
                            @if($teacher->subject_specialization)
                                <p class="text-xs text-emerald-600 font-medium mt-0.5">{{ $teacher->subject_specialization }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Enrolled Courses Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">
                    Courses enrolled by <span class="text-blue-600">{{ $selectedStudent->name }}</span>
                </h3>
                <span class="text-xs text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full font-medium">
                    {{ $studentEnrollments->count() }} course{{ $studentEnrollments->count() !== 1 ? 's' : '' }}
                </span>
            </div>

            @if($studentEnrollments->isEmpty())
                <p class="px-5 py-10 text-center text-sm text-gray-400">No enrollments yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">#</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Course</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Teacher</th>
                                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Payment</th>
                                <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Enrolled On</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($studentEnrollments as $i => $enrollment)
                                <tr class="hover:bg-gray-50/70 transition-colors">
                                    <td class="px-5 py-3.5 text-gray-400 text-xs font-medium">{{ $i + 1 }}</td>
                                    <td class="px-5 py-3.5 font-medium text-gray-800">
                                        {{ $enrollment->course?->title ?? 'Course Deleted' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-gray-600 hidden sm:table-cell">
                                        @if($enrollment->course?->teacher)
                                            <div class="flex items-center gap-2">
                                                <img src="{{ $enrollment->course->teacher->avatar_url }}" alt=""
                                                     class="w-5 h-5 rounded-full object-cover">
                                                <span>{{ $enrollment->course->teacher->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        @php $ps = $enrollment->payment_status ?? 'free'; @endphp
                                        <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                                            {{ $ps === 'paid'    ? 'bg-emerald-100 text-emerald-700'
                                             : ($ps === 'free'   ? 'bg-gray-100 text-gray-600'
                                             : 'bg-yellow-100 text-yellow-700') }}">
                                            {{ ucfirst($ps) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right text-gray-500 hidden md:table-cell">
                                        {{ ($enrollment->enrolled_at ?? $enrollment->created_at)->format('M d, Y') }}
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
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('student-detail');
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    </script>
    @endpush
    @endif

</div>
@endsection
