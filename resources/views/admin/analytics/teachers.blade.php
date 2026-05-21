@extends('layouts.admin')

@section('title', 'Teacher Analytics — Nabha Learning')

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
                <h1 class="text-2xl font-bold">Teacher Analytics</h1>
                <p class="text-gray-400 mt-0.5 text-sm">Courses created &amp; student reach per teacher</p>
            </div>
        </div>
    </div>

    {{-- ── Summary Stats ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-emerald-600">{{ number_format($totalTeachers) }}</div>
            <div class="text-xs text-gray-500 mt-1 font-medium">Total Teachers</div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-indigo-600">{{ number_format($totalTCourses) }}</div>
            <div class="text-xs text-gray-500 mt-1 font-medium">Total Courses Created</div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-blue-600">{{ number_format($totalTStudents) }}</div>
            <div class="text-xs text-gray-500 mt-1 font-medium">Total Enrollments</div>
        </div>
    </div>

    {{-- ── Filters ── --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('admin.teachers-analytics') }}" data-no-loading>
            <div class="flex flex-col sm:flex-row gap-3">

                {{-- Search --}}
                <div class="flex-1">
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Search teachers by name or email…"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition">
                </div>

                {{-- Teacher dropdown drill-down --}}
                <div class="sm:w-64">
                    <select name="teacher_id" onchange="this.form.submit()"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white transition">
                        <option value="">— Select Teacher to Drill Down —</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}" {{ request('teacher_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->name }} ({{ $t->courses_count }} course{{ $t->courses_count !== 1 ? 's' : '' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">
                    Search
                </button>

                @if($search || request('teacher_id'))
                    <a href="{{ route('admin.teachers-analytics') }}"
                       class="px-5 py-2.5 border border-gray-200 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 transition text-center whitespace-nowrap">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ── Teachers Table ── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">
                All Teachers
                @if($search)
                    <span class="ml-2 text-sm text-gray-400 font-normal">— "{{ $search }}"</span>
                @endif
            </h3>
            <span class="text-xs text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full font-medium">
                {{ $teachers->count() }} teacher{{ $teachers->count() !== 1 ? 's' : '' }}
            </span>
        </div>

        @if($teachers->isEmpty())
            <div class="px-5 py-14 text-center text-gray-400">
                <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="text-sm">No teachers found{{ $search ? ' for "'.$search.'"' : '' }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Teacher</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Email</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Courses</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Students</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Status</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Drill Down</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($teachers as $teacher)
                            @php $isSelected = request('teacher_id') == $teacher->id; @endphp
                            <tr class="transition-colors {{ $isSelected ? 'bg-indigo-50/80' : 'hover:bg-gray-50/70' }}">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $teacher->avatar_url }}" alt=""
                                             class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                                        <div>
                                            <span class="font-medium text-gray-800 block">{{ $teacher->name }}</span>
                                            @if($teacher->subject_specialization)
                                                <span class="text-xs text-gray-400">{{ $teacher->subject_specialization }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-gray-500 hidden sm:table-cell">{{ $teacher->email }}</td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="font-bold text-indigo-600 text-base">{{ $teacher->courses_count }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="font-bold text-blue-600 text-base">{{ $teacher->total_students }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-center hidden md:table-cell">
                                    @php $status = $teacher->status ?? 'approved'; @endphp
                                    <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                                        {{ $status === 'approved' ? 'bg-emerald-100 text-emerald-700'
                                         : ($status === 'pending'  ? 'bg-yellow-100 text-yellow-700'
                                         : 'bg-red-100 text-red-700') }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @if($isSelected)
                                        <span class="text-xs font-semibold text-indigo-500 bg-indigo-100 px-2.5 py-1 rounded-full">
                                            Selected ↓
                                        </span>
                                    @else
                                        <a href="{{ route('admin.teachers-analytics', array_filter(['teacher_id' => $teacher->id, 'search' => $search])) }}"
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
        @endif
    </div>

    {{-- ── Teacher Drill-Down ── --}}
    @if($selectedTeacher)
    <div id="teacher-detail" class="space-y-4 scroll-mt-6">

        {{-- Teacher Info Card --}}
        <div class="bg-gradient-to-r from-indigo-600 to-violet-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex flex-col sm:flex-row items-start gap-5">
                <img src="{{ $selectedTeacher->avatar_url }}" alt=""
                     class="w-16 h-16 rounded-2xl border-2 border-white/30 flex-shrink-0 object-cover">
                <div class="flex-1 min-w-0">
                    <h2 class="text-xl font-bold">{{ $selectedTeacher->name }}</h2>
                    <p class="text-indigo-200 text-sm mt-0.5">{{ $selectedTeacher->email }}</p>
                    @if($selectedTeacher->subject_specialization)
                        <p class="text-indigo-200 text-xs mt-1 font-medium">
                            Subject Specialization: {{ $selectedTeacher->subject_specialization }}
                        </p>
                    @endif
                    @if($selectedTeacher->school)
                        <p class="text-indigo-200 text-xs mt-0.5">School: {{ $selectedTeacher->school }}</p>
                    @endif
                </div>
                <div class="flex gap-6 text-center">
                    <div class="bg-white/10 rounded-xl px-4 py-3">
                        <div class="text-2xl font-bold">{{ $selectedTeacher->courses_count }}</div>
                        <div class="text-xs text-indigo-200 mt-0.5">Courses</div>
                    </div>
                    <div class="bg-white/10 rounded-xl px-4 py-3">
                        <div class="text-2xl font-bold">{{ $selectedTeacher->total_students }}</div>
                        <div class="text-xs text-indigo-200 mt-0.5">Students</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Courses Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">
                    Courses by <span class="text-indigo-600">{{ $selectedTeacher->name }}</span>
                </h3>
                <span class="text-xs text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full font-medium">
                    {{ $teacherCourses->count() }} course{{ $teacherCourses->count() !== 1 ? 's' : '' }}
                </span>
            </div>

            @if($teacherCourses->isEmpty())
                <p class="px-5 py-10 text-center text-sm text-gray-400">No courses created yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Course Title</th>
                                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Enrolled Students</th>
                                <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Price</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($teacherCourses as $course)
                                <tr class="hover:bg-gray-50/70 transition-colors">
                                    <td class="px-5 py-3.5 font-medium text-gray-800">{{ $course->title }}</td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                                            {{ $course->status === 'published' ? 'bg-emerald-100 text-emerald-700'
                                             : ($course->status === 'pending'   ? 'bg-yellow-100 text-yellow-700'
                                             : ($course->status === 'draft'     ? 'bg-gray-100 text-gray-600'
                                             : 'bg-red-100 text-red-700')) }}">
                                            {{ ucfirst($course->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="font-bold text-blue-600">{{ $course->enrollments_count }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-medium text-gray-700">
                                        {{ (float)$course->price === 0.0 ? 'Free' : '₹'.number_format($course->price) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-indigo-50/50 border-t border-indigo-100">
                                <td class="px-5 py-3 font-semibold text-gray-700 text-sm">Total</td>
                                <td></td>
                                <td class="px-5 py-3 text-center font-bold text-blue-700">
                                    {{ $teacherCourses->sum('enrollments_count') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>

        {{-- Students Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">
                    Students under <span class="text-indigo-600">{{ $selectedTeacher->name }}</span>
                </h3>
                <span class="text-xs text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full font-medium">
                    {{ $teacherStudents->count() }} student{{ $teacherStudents->count() !== 1 ? 's' : '' }}
                </span>
            </div>

            @if($teacherStudents->isEmpty())
                <div class="px-5 py-10 text-center text-sm text-gray-400">
                    No students enrolled in this teacher's courses yet.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">#</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Student</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Email</th>
                                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Courses</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Enrolled In</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($teacherStudents as $i => $student)
                                <tr class="hover:bg-gray-50/70 transition-colors">
                                    <td class="px-5 py-3.5 text-gray-400 text-xs font-medium">{{ $i + 1 }}</td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $student->avatar_url }}" alt=""
                                                 class="w-7 h-7 rounded-full object-cover flex-shrink-0">
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
                                        <span class="font-bold text-indigo-600">{{ $student->enrollments->count() }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 hidden lg:table-cell">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($student->enrollments as $enrollment)
                                                @if($enrollment->course)
                                                    <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-medium">
                                                        {{ \Illuminate\Support\Str::limit($enrollment->course->title, 28) }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
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
            const el = document.getElementById('teacher-detail');
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    </script>
    @endpush
    @endif

</div>
@endsection
