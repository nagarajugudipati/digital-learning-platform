@extends('layouts.admin')

@section('title', 'Manage Users - Nabha Learning')

@section('admin-content')
<div class="space-y-5" x-data="{ showAdd: false }">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manage Users</h1>
            <p class="text-gray-500 text-sm mt-1">Manage all platform users</p>
        </div>
        <button @click="showAdd = !showAdd"
                class="flex items-center gap-2 bg-indigo-600 text-white px-4 py-2.5 rounded-xl hover:bg-indigo-700 transition text-sm font-medium">
            Add User
        </button>
    </div>

    <!-- Add User Form -->
    <div x-show="showAdd" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">Add New User</h3>
        <form method="POST" action="{{ route('admin.users.store') }}"
              class="grid grid-cols-1 md:grid-cols-3 gap-4" x-data="{ role: 'student' }">
            @csrf
            <input type="text" name="name" required placeholder="Full Name"
                   class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <input type="email" name="email" required placeholder="Email Address"
                   class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <input type="password" name="password" required placeholder="Password"
                   class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <select name="role" x-model="role" required
                    class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
                <option value="admin">Admin</option>
            </select>
            <div x-show="role === 'student'">
                <select name="class_level" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach(['Class 6','Class 7','Class 8','Class 9','Class 10'] as $class)
                        <option>{{ $class }}</option>
                    @endforeach
                </select>
            </div>
            <div x-show="role === 'teacher'">
                <select name="subject_specialization" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach(['Mathematics','Science','English','Hindi','Social Studies'] as $s)
                        <option>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-indigo-700 transition">
                Create User
            </button>
        </form>
    </div>

    <!-- Filter -->
    <form method="GET" data-no-loading class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
               class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <select name="role" class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">All Roles</option>
            <option value="student" {{ request('role') === 'student' ? 'selected':'' }}>Students</option>
            <option value="teacher" {{ request('role') === 'teacher' ? 'selected':'' }}>Teachers</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected':'' }}>Admins</option>
        </select>
        <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-sm hover:bg-indigo-700 transition font-medium">Filter</button>
    </form>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">User</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Class/Subject</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Activity</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->avatar_url }}" class="w-8 h-8 rounded-full" alt="">
                                    <div>
                                        <p class="font-medium text-sm text-gray-800">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                    {{ $user->role === 'admin' ? 'bg-gray-100 text-gray-700' : ($user->role === 'teacher' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $user->class_level ?? $user->subject_specialization ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $user->quiz_attempts_count }} attempts | {{ $user->progress_reports_count }} lessons
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('admin.users.toggle', $user->id) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="text-xs px-3 py-1.5 rounded-lg transition
                                                       {{ $user->is_active ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }}">
                                            {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                                              onsubmit="return confirm('Delete user {{ $user->name }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1.5 rounded-lg transition">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $users->withQueryString()->links() }}</div>
</div>
@endsection
