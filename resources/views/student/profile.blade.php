@extends('layouts.student')

@section('title', 'My Profile - Nabha Learning')

@section('student-content')
<div class="max-w-2xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-800">My Profile</h1>
        <p class="text-gray-500 text-sm mt-1">View and update your account details</p>
    </div>

    <!-- Profile Info Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-4 mb-6 pb-5 border-b border-gray-100">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                 class="w-16 h-16 rounded-full object-cover">
            <div>
                <h2 class="text-lg font-bold text-gray-800">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                <span class="inline-block mt-1 px-2.5 py-0.5 bg-primary-100 text-primary-700 text-xs rounded-full font-medium">
                    {{ ucfirst($user->role) }}
                </span>
            </div>
        </div>

        <!-- Read-only info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">Email</p>
                <p class="text-sm font-medium text-gray-800">{{ $user->email }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">Class Level</p>
                <p class="text-sm font-medium text-gray-800">{{ $user->class_level ?? 'Not set' }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">School</p>
                <p class="text-sm font-medium text-gray-800">{{ $user->school ?? 'Not set' }}</p>
            </div>
            @if($user->streak_count > 0)
                <div class="bg-orange-50 rounded-xl p-4 border border-orange-100">
                    <p class="text-xs text-orange-600 mb-1">Daily Streak</p>
                    <p class="text-sm font-bold text-orange-700">🔥 {{ $user->streak_count }} day{{ $user->streak_count !== 1 ? 's' : '' }}</p>
                </div>
            @endif
        </div>

        <!-- Edit form -->
        <form method="POST" action="{{ route('student.profile.update') }}">
            @csrf @method('PUT')
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Edit Details</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm
                               @error('name') border-red-400 @enderror">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm"
                           placeholder="e.g., +91 98765 43210">
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="mt-5">
                <button type="submit"
                        class="w-full bg-primary-600 hover:bg-primary-700 text-white py-3 rounded-xl font-semibold transition text-sm">
                    Update Profile
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-5">Change Password</h3>

        <form method="POST" action="{{ route('student.profile.password') }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Current Password *</label>
                    <input type="password" name="current_password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm
                               @error('current_password') border-red-400 @enderror">
                    @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">New Password *</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm
                               @error('password') border-red-400 @enderror"
                           placeholder="Minimum 6 characters">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm New Password *</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
                </div>
            </div>
            <div class="mt-5">
                <button type="submit"
                        class="w-full bg-gray-800 hover:bg-gray-900 text-white py-3 rounded-xl font-semibold transition text-sm">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
