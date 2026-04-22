@extends('layouts.app')

@section('title', 'Register - Nabha Digital Learning')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-primary-900 via-primary-800 to-indigo-900 flex items-center justify-center p-4">
    <div class="w-full max-w-lg">
        <div class="text-center mb-8 text-white">
            <a href="/" class="inline-flex items-center gap-2 mb-4">
                <span class="text-4xl">🎓</span>
                <span class="font-bold text-2xl">Nabha Digital Learning</span>
            </a>
            <p class="text-primary-300">Create your free account and start learning today!</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Create Account</h2>

            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
                    <ul class="space-y-1">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4"
                  x-data="{ role: '{{ old('role', 'student') }}' }">
                @csrf

                {{-- Role selector --}}
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" @click="role='student'"
                            :class="role==='student' ? 'border-primary-600 bg-primary-50 text-primary-700' : 'border-gray-200 text-gray-500'"
                            class="p-3 border-2 rounded-xl text-sm font-medium text-center transition cursor-pointer">
                        <div class="text-2xl mb-1">👨‍🎓</div>
                        I'm a Student
                    </button>
                    <button type="button" @click="role='teacher'"
                            :class="role==='teacher' ? 'border-emerald-600 bg-emerald-50 text-emerald-700' : 'border-gray-200 text-gray-500'"
                            class="p-3 border-2 rounded-xl text-sm font-medium text-center transition cursor-pointer">
                        <div class="text-2xl mb-1">👨‍🏫</div>
                        I'm a Teacher
                    </button>
                </div>
                <input type="hidden" name="role" :value="role">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                           placeholder="Your full name">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                           placeholder="your@email.com">
                </div>

                {{-- Student field --}}
                <div x-show="role==='student'">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Your Class</label>
                    <select name="class_level"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                        <option value="">Select your class</option>
                        @foreach(['Class 6','Class 7','Class 8','Class 9','Class 10'] as $class)
                            <option value="{{ $class }}" {{ old('class_level')===$class ? 'selected':'' }}>{{ $class }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Teacher field --}}
                <div x-show="role==='teacher'">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Subject Specialization</label>
                    <select name="subject_specialization"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                        <option value="">Select your subject</option>
                        @foreach(['Mathematics','Science','English','Hindi','Social Studies','Physical Education'] as $subj)
                            <option value="{{ $subj }}" {{ old('subject_specialization')===$subj ? 'selected':'' }}>{{ $subj }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone (Optional)</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                           placeholder="+91 XXXXX XXXXX">
                </div>

                {{-- Password fields side-by-side with toggles --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="reg-password" required
                                   class="w-full px-4 py-3 pr-11 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                                   placeholder="Min 6 chars">
                            <button type="button"
                                    onclick="togglePassword('reg-password', this)"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 transition"
                                    tabindex="-1" title="Show/hide password">
                                <svg id="reg-password-eye-show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="reg-password-eye-hide" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="reg-confirm" required
                                   class="w-full px-4 py-3 pr-11 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
                                   placeholder="Repeat password">
                            <button type="button"
                                    onclick="togglePassword('reg-confirm', this)"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 transition"
                                    tabindex="-1" title="Show/hide password">
                                <svg id="reg-confirm-eye-show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="reg-confirm-eye-hide" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 rounded-xl transition shadow-md mt-2">
                    Create My Account →
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-5">
                Already have an account?
                <a href="{{ route('login') }}" class="text-primary-600 font-semibold hover:underline">Sign in here</a>
            </p>
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
