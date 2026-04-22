@extends('layouts.teacher')

@section('title', 'Teacher Dashboard - Nabha Learning')

@section('teacher-content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-6 text-white">
        <h1 class="text-2xl font-bold">Welcome, {{ auth()->user()->name }}!</h1>
        <p class="text-emerald-200 mt-1">{{ auth()->user()->subject_specialization ?? 'Teacher' }} | {{ auth()->user()->school }}</p>
        <p class="text-sm mt-3 text-emerald-100">Thank you for empowering students in Nabha through digital education!</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-3xl font-bold text-gray-800">{{ $lessonsCount }}</div>
            <div class="text-sm text-gray-500 mt-1">Lessons Created</div>
            <a href="{{ route('teacher.lessons') }}" class="text-xs text-emerald-600 hover:underline mt-1 block">Manage →</a>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-3xl font-bold text-gray-800">{{ $quizzesCount }}</div>
            <div class="text-sm text-gray-500 mt-1">Quizzes Created</div>
            <a href="{{ route('teacher.quizzes') }}" class="text-xs text-emerald-600 hover:underline mt-1 block">Manage →</a>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-3xl font-bold text-gray-800">{{ $studentsReached }}</div>
            <div class="text-sm text-gray-500 mt-1">Students Reached</div>
            <a href="{{ route('teacher.analytics') }}" class="text-xs text-emerald-600 hover:underline mt-1 block">Analytics →</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('teacher.lessons.create') }}" class="flex items-center gap-3 p-3 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition group">
                    <div>
                        <p class="font-medium text-sm text-emerald-800">Upload New Lesson</p>
                        <p class="text-xs text-gray-500">Share PDF or video with students</p>
                    </div>
                    <span class="ml-auto text-emerald-500 group-hover:translate-x-1 transition">→</span>
                </a>
                <a href="{{ route('teacher.quizzes.create') }}" class="flex items-center gap-3 p-3 bg-blue-50 hover:bg-blue-100 rounded-xl transition group">
                    <div>
                        <p class="font-medium text-sm text-blue-800">Create New Quiz</p>
                        <p class="text-xs text-gray-500">Test student understanding</p>
                    </div>
                    <span class="ml-auto text-blue-500 group-hover:translate-x-1 transition">→</span>
                </a>
                <a href="{{ route('teacher.analytics') }}" class="flex items-center gap-3 p-3 bg-purple-50 hover:bg-purple-100 rounded-xl transition group">
                    <div>
                        <p class="font-medium text-sm text-purple-800">View Analytics</p>
                        <p class="text-xs text-gray-500">Track student performance</p>
                    </div>
                    <span class="ml-auto text-purple-500 group-hover:translate-x-1 transition">→</span>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4">Tips for Teachers</h3>
            <div class="space-y-3 text-sm text-gray-600">
                <div class="flex items-start gap-2">
                    <span class="text-emerald-500 mt-0.5 flex-shrink-0">✓</span>
                    <p>Upload lessons as PDF for easy offline download by students.</p>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-emerald-500 mt-0.5 flex-shrink-0">✓</span>
                    <p>Add at least 5 questions to your quizzes for better assessment.</p>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-emerald-500 mt-0.5 flex-shrink-0">✓</span>
                    <p>Lessons need Admin approval before students can see them.</p>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-emerald-500 mt-0.5 flex-shrink-0">✓</span>
                    <p>Add explanations to quiz answers to help students learn from mistakes.</p>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-emerald-500 mt-0.5 flex-shrink-0">✓</span>
                    <p>Check analytics regularly to identify students who need extra help.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
