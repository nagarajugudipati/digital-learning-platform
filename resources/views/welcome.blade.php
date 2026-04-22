@extends('layouts.app')

@section('title', 'Nabha Digital Learning Platform')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-primary-900 via-primary-800 to-indigo-900 text-white">
    <!-- Navbar -->
    <nav class="px-6 py-4 flex items-center justify-between max-w-7xl mx-auto">
        <div class="flex items-center gap-3">
            <span class="text-3xl">🎓</span>
            <div>
                <span class="font-bold text-xl">Nabha Digital Learning</span>
                <p class="text-xs text-primary-300">Empowering Rural Education</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('login') }}" class="text-sm px-4 py-2 border border-primary-400 rounded-lg hover:bg-primary-700 transition">Login</a>
            <a href="{{ route('register') }}" class="text-sm px-4 py-2 bg-white text-primary-700 font-semibold rounded-lg hover:bg-primary-50 transition">Register Free</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="max-w-7xl mx-auto px-6 py-16 text-center">
        <div class="inline-flex items-center gap-2 bg-primary-700 bg-opacity-50 px-4 py-2 rounded-full text-sm mb-6">
            <span>🇮🇳</span>
            <span>Government Senior Secondary School, Nabha, Punjab</span>
        </div>
        <h1 class="text-4xl md:text-6xl font-bold leading-tight mb-6">
            Digital Education for<br>
            <span class="text-yellow-400">Every Student</span> in Nabha
        </h1>
        <p class="text-lg md:text-xl text-primary-200 max-w-2xl mx-auto mb-10">
            Access lessons, take quizzes, and get AI-powered help — even without internet.
            Quality education designed for rural students in Classes 6-10.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}" class="px-8 py-4 bg-yellow-400 text-gray-900 font-bold rounded-xl hover:bg-yellow-300 transition text-lg">
                Start Learning Free →
            </a>
            <a href="{{ route('login') }}" class="px-8 py-4 border-2 border-white rounded-xl hover:bg-white hover:text-primary-900 transition text-lg font-semibold">
                I Have an Account
            </a>
        </div>
    </section>

    <!-- Features Grid -->
    <section class="max-w-7xl mx-auto px-6 py-16">
        <h2 class="text-3xl font-bold text-center mb-12">Everything You Need to Excel</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $features = [
                    ['icon'=>'📚','title'=>'Rich Lesson Library','desc'=>'PDF and video lessons for all subjects — Maths, Science, English, Hindi & Social Studies for Classes 6-10'],
                    ['icon'=>'✏️','title'=>'Interactive Quizzes','desc'=>'Test your knowledge with timed multiple-choice quizzes. Get instant results and detailed explanations'],
                    ['icon'=>'🤖','title'=>'AI Study Assistant','desc'=>'Get instant answers to your questions using our smart chatbot. Available 24/7, covers entire syllabus'],
                    ['icon'=>'📶','title'=>'Works Offline','desc'=>'Download lessons to study without internet. All content works offline using PWA technology'],
                    ['icon'=>'📊','title'=>'Progress Tracking','desc'=>'Visual charts showing your performance across subjects. Track improvement over time'],
                    ['icon'=>'👨‍🏫','title'=>'Expert Teachers','desc'=>'Content created and verified by qualified teachers from local schools in Nabha district'],
                ];
            @endphp
            @foreach($features as $feature)
                <div class="bg-white bg-opacity-10 backdrop-blur p-6 rounded-2xl hover:bg-opacity-20 transition">
                    <div class="text-4xl mb-4">{{ $feature['icon'] }}</div>
                    <h3 class="font-bold text-xl mb-2">{{ $feature['title'] }}</h3>
                    <p class="text-primary-200 text-sm leading-relaxed">{{ $feature['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Stats -->
    <section class="max-w-7xl mx-auto px-6 py-10">
        <div class="bg-white bg-opacity-10 rounded-2xl p-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div><div class="text-4xl font-bold text-yellow-400">500+</div><div class="text-primary-200 text-sm mt-1">Students Enrolled</div></div>
                <div><div class="text-4xl font-bold text-yellow-400">50+</div><div class="text-primary-200 text-sm mt-1">Lessons Available</div></div>
                <div><div class="text-4xl font-bold text-yellow-400">5</div><div class="text-primary-200 text-sm mt-1">Subjects Covered</div></div>
                <div><div class="text-4xl font-bold text-yellow-400">100%</div><div class="text-primary-200 text-sm mt-1">Free for Students</div></div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-primary-700 mt-10 py-8 text-center text-primary-300 text-sm">
        <p>© 2024 Nabha Digital Learning Platform | Government Senior Secondary School, Nabha, Punjab</p>
        <p class="mt-1">Built with ❤️ to empower rural education in India 🇮🇳</p>
    </footer>
</div>
@endsection
