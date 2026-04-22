@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="{ sidebarOpen: false }">
    <nav class="bg-gray-900 text-white shadow-lg sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-md hover:bg-gray-700 text-sm font-medium">
                        Menu
                    </button>
                    <div>
                        <span class="font-bold text-lg hidden sm:block">Nabha Digital Learning</span>
                        <span class="text-xs text-gray-400 hidden sm:block">Admin Panel</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-400 hidden md:block">Admin: {{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm bg-gray-700 hover:bg-gray-600 px-3 py-1.5 rounded-lg transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <aside class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-900 text-white shadow-xl transform transition-transform duration-300 md:translate-x-0 md:static md:z-auto top-16"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="p-4 mt-16 md:mt-0">
                <div class="flex items-center gap-3 p-3 bg-gray-800 rounded-xl mb-6">
                    <img src="{{ auth()->user()->avatar_url }}" class="w-10 h-10 rounded-full" alt="Avatar">
                    <div>
                        <p class="font-semibold text-sm">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400">Administrator</p>
                    </div>
                </div>
                <nav class="space-y-1">
                    @php
                        $links = [
                            ['route' => 'admin.dashboard',   'label' => 'Dashboard'],
                            ['route' => 'admin.users',       'label' => 'Manage Users'],
                            ['route' => 'admin.teachers',    'label' => 'Teacher Approvals'],
                            ['route' => 'admin.reports',     'label' => 'Student Reports'],
                            ['route' => 'admin.content',     'label' => 'Content Review'],
                            ['route' => 'admin.courses',     'label' => 'Course Approval'],
                            ['route' => 'admin.chatbot-qa',  'label' => 'Chatbot Training'],
                        ];
                    @endphp
                    @foreach($links as $link)
                        <a href="{{ route($link['route']) }}"
                           class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition
                                  {{ request()->routeIs($link['route']) ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </aside>

        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden" x-cloak></div>

        <main class="flex-1 min-w-0 p-4 md:p-6 lg:p-8">
            @yield('admin-content')
        </main>
    </div>
</div>
@endsection
