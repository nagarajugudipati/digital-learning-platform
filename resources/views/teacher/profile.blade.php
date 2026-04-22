@extends('layouts.teacher')

@section('title', 'My Profile - Nabha Learning')

@section('teacher-content')
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
                <div class="flex items-center gap-2 mt-1">
                    <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-700 text-xs rounded-full font-medium">
                        Teacher
                    </span>
                    @php $st = $user->status ?? 'approved'; @endphp
                    <span class="px-2.5 py-0.5 text-xs rounded-full font-medium
                        {{ $st === 'approved' ? 'bg-blue-100 text-blue-700' :
                           ($st === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ ucfirst($st) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Read-only info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">Email</p>
                <p class="text-sm font-medium text-gray-800">{{ $user->email }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">Specialization</p>
                <p class="text-sm font-medium text-gray-800">{{ $user->subject_specialization ?? 'Not set' }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">Phone</p>
                <p class="text-sm font-medium text-gray-800">{{ $user->phone ?? 'Not set' }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 mb-1">Member Since</p>
                <p class="text-sm font-medium text-gray-800">{{ $user->created_at->format('d M Y') }}</p>
            </div>
        </div>

        <!-- Edit form -->
        <form method="POST" action="{{ route('teacher.profile.update') }}">
            @csrf @method('PUT')
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Edit Details</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm
                               @error('name') border-red-400 @enderror">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                           placeholder="e.g., +91 98765 43210">
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="mt-5">
                <button type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-semibold transition text-sm">
                    Update Profile
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-5">Change Password</h3>

        <form method="POST" action="{{ route('teacher.profile.password') }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Current Password *</label>
                    <input type="password" name="current_password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm
                               @error('current_password') border-red-400 @enderror">
                    @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">New Password *</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm
                               @error('password') border-red-400 @enderror"
                           placeholder="Minimum 6 characters">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm New Password *</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
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
