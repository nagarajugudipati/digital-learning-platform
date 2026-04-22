@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="{ sidebarOpen: false }">
    <nav class="bg-emerald-700 text-white shadow-lg sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-md hover:bg-emerald-600 text-sm font-medium">
                        Menu
                    </button>
                    <div>
                        <span class="font-bold text-lg hidden sm:block">Nabha Digital Learning</span>
                        <span class="text-xs text-emerald-200 hidden sm:block">Teacher Portal</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-emerald-200 hidden md:block">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm bg-emerald-600 hover:bg-emerald-500 px-3 py-1.5 rounded-lg transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <aside class="fixed inset-y-0 left-0 z-30 w-64 bg-white shadow-xl transform transition-transform duration-300 md:translate-x-0 md:static md:shadow-none md:z-auto top-16"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="p-4 mt-16 md:mt-0">
                <div class="flex items-center gap-3 p-3 bg-emerald-50 rounded-xl mb-6">
                    <img src="{{ auth()->user()->avatar_url }}" class="w-10 h-10 rounded-full" alt="Avatar">
                    <div>
                        <p class="font-semibold text-sm text-gray-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->subject_specialization ?? 'Teacher' }}</p>
                    </div>
                </div>
                <nav class="space-y-1">
                    @php
                        $links = [
                            ['route' => 'teacher.dashboard',        'label' => 'Dashboard'],
                            ['route' => 'teacher.lessons',          'label' => 'My Lessons'],
                            ['route' => 'teacher.lessons.create',   'label' => 'Upload Lesson'],
                            ['route' => 'teacher.quizzes',          'label' => 'My Quizzes'],
                            ['route' => 'teacher.quizzes.create',   'label' => 'Create Quiz'],
                            ['route' => 'teacher.analytics',        'label' => 'Analytics'],
                            ['route' => 'teacher.student.progress', 'label' => 'Student Progress'],
                            ['route' => 'teacher.reports',          'label' => 'Reports'],
                            ['route' => 'teacher.courses',          'label' => 'My Courses'],
                            ['route' => 'teacher.courses.create',   'label' => 'Create Course'],
                            ['route' => 'teacher.chatbot-qa',       'label' => 'Chatbot Training'],
                            ['route' => 'teacher.profile',          'label' => 'My Profile'],
                        ];
                    @endphp
                    @foreach($links as $link)
                        <a href="{{ route($link['route']) }}"
                           class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition
                                  {{ request()->routeIs($link['route']) ? 'bg-emerald-600 text-white' : 'text-gray-600 hover:bg-emerald-50 hover:text-emerald-700' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </aside>

        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden" x-cloak></div>

        <main class="flex-1 min-w-0 p-4 md:p-6 lg:p-8">
            @yield('teacher-content')
        </main>
    </div>
</div>
@endsection
