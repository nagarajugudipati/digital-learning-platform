@extends('layouts.app')

@section('title', 'Login - Nabha Digital Learning')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-primary-900 via-primary-800 to-indigo-900 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8 text-white">
            <a href="/" class="inline-flex items-center gap-2 mb-4">
                <span class="text-4xl">🎓</span>
                <span class="font-bold text-2xl">Nabha Digital Learning</span>
            </a>
            <p class="text-primary-300">Welcome back! Sign in to continue learning.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Sign In</h2>

            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                           placeholder="your@email.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="login-password" required
                               class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                               placeholder="••••••••">
                        <button type="button"
                                onclick="togglePassword('login-password', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 transition p-1 rounded-lg"
                                tabindex="-1"
                                title="Show/hide password">
                            <svg id="login-password-eye-show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="login-password-eye-hide" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-primary-600">
                        Remember me
                    </label>
                </div>

                <button type="submit"
                        class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 rounded-xl transition shadow-md">
                    Sign In →
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-primary-600 font-semibold hover:underline">Register here</a>
            </p>

            {{-- Demo accounts --}}
            <div class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-100">
                <p class="text-xs font-semibold text-blue-700 mb-2">Demo Accounts:</p>
                <div class="space-y-1 text-xs text-blue-600">
                    <p>👤 Student: student@nabha.edu | password: password</p>
                    <p>👨‍🏫 Teacher: teacher@nabha.edu | password: password</p>
                    <p>⚙️ Admin: admin@nabha.edu | password: password</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, btn) {
    const input  = document.getElementById(inputId);
    const isHide = input.type === 'password';
    input.type   = isHide ? 'text' : 'password';

    const showIcon = document.getElementById(inputId + '-eye-show');
    const hideIcon = document.getElementById(inputId + '-eye-hide');
    if (showIcon) showIcon.classList.toggle('hidden', isHide);
    if (hideIcon) hideIcon.classList.toggle('hidden', !isHide);
}
</script>
@endsection
