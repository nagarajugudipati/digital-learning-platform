@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="{ sidebarOpen: false }">
    <!-- Top Navbar -->
    <nav class="bg-primary-700 text-white shadow-lg sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-md hover:bg-primary-600 text-sm font-medium">
                        Menu
                    </button>
                    <div>
                        <span class="font-bold text-lg hidden sm:block">Nabha Digital Learning</span>
                        <span class="text-xs text-primary-200 hidden sm:block">Student Portal</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-primary-200 hidden md:block">{{ auth()->user()->name }} | {{ auth()->user()->class_level }}</span>
                    @php $cartCount = auth()->user()->cartItems()->count(); @endphp
                    <a href="{{ route('student.cart') }}"
                       class="relative px-3 py-1.5 rounded-lg hover:bg-primary-600 transition text-sm font-medium" title="Cart">
                        Cart
                        @if($cartCount > 0)
                            <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-amber-400 text-gray-900 text-[10px] font-bold rounded-full flex items-center justify-center px-0.5">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm bg-primary-600 hover:bg-primary-500 px-3 py-1.5 rounded-lg transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-30 w-64 bg-white shadow-xl transform transition-transform duration-300 md:translate-x-0 md:static md:shadow-none md:z-auto top-16"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="p-4 mt-16 md:mt-0">
                <div class="flex items-center gap-3 p-3 bg-primary-50 rounded-xl mb-6">
                    <img src="{{ auth()->user()->avatar_url }}" class="w-10 h-10 rounded-full" alt="Avatar">
                    <div>
                        <p class="font-semibold text-sm text-gray-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->class_level }}</p>
                    </div>
                </div>
                <nav class="space-y-1">
                    @php
                        $links = [
                            ['route' => 'student.dashboard',  'label' => 'Dashboard'],
                            ['route' => 'student.courses',    'label' => 'Courses'],
                            ['route' => 'student.my-courses', 'label' => 'My Courses'],
                            ['route' => 'student.lessons',    'label' => 'Lessons'],
                            ['route' => 'student.quizzes',    'label' => 'Quizzes'],
                            ['route' => 'student.chatbot',    'label' => 'AI Chatbot'],
                            ['route' => 'student.profile',    'label' => 'My Profile'],
                        ];
                    @endphp
                    @foreach($links as $link)
                        <a href="{{ route($link['route']) }}"
                           class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition
                                  {{ request()->routeIs($link['route']) ? 'bg-primary-600 text-white' : 'text-gray-600 hover:bg-primary-50 hover:text-primary-700' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach

                    {{-- Cart link with live count --}}
                    <a href="{{ route('student.cart') }}"
                       class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition
                              {{ request()->routeIs('student.cart') ? 'bg-primary-600 text-white' : 'text-gray-600 hover:bg-primary-50 hover:text-primary-700' }}">
                        <span class="flex-1">Cart</span>
                        @if($cartCount > 0)
                            <span class="bg-amber-400 text-gray-900 text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[20px] text-center">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden" x-cloak></div>

        <!-- Main Content -->
        <main class="flex-1 min-w-0 p-4 md:p-6 lg:p-8">
            @yield('student-content')
        </main>
    </div>
</div>
@endsection
