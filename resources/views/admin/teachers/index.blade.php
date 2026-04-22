@extends('layouts.admin')

@section('title', 'Teacher Approvals - Admin')

@section('admin-content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Teacher Approvals</h1>
            <p class="text-gray-500 text-sm mt-1">Review and manage teacher account requests</p>
        </div>
        {{-- Pending count badge --}}
        @php $pendingCount = \App\Models\User::where('role','teacher')->where('status','pending')->count(); @endphp
        @if($pendingCount > 0)
            <span class="inline-flex items-center gap-1.5 bg-yellow-100 text-yellow-700 text-sm font-semibold px-4 py-2 rounded-xl">
                {{ $pendingCount }} pending review
            </span>
        @endif
    </div>

    {{-- Status Tabs + optional search --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="flex gap-2 flex-wrap">
            @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All'] as $val => $label)
                <a href="{{ route('admin.teachers', ['status' => $val]) }}"
                   class="px-4 py-2 rounded-xl text-sm font-semibold transition
                       {{ $status === $val
                           ? 'bg-indigo-600 text-white shadow-sm'
                           : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                    {{ $label }}
                    @if($val === 'pending' && $pendingCount > 0)
                        <span class="ml-1 bg-yellow-400 text-gray-800 text-xs px-1.5 py-0.5 rounded-full">{{ $pendingCount }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    @if($teachers->isEmpty())
        <div class="bg-white rounded-2xl py-20 text-center shadow-sm border border-gray-100">
            <p class="text-gray-500 text-sm">No teacher accounts with status "{{ $status }}".</p>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- Mobile: stacked cards --}}
            <div class="sm:hidden divide-y divide-gray-100">
                @foreach($teachers as $teacher)
                    @php $st = $teacher->status ?? 'approved'; @endphp
                    <div class="p-4 space-y-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $teacher->avatar_url }}" alt="{{ $teacher->name }}"
                                 class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 text-sm truncate">{{ $teacher->name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $teacher->email }}</p>
                            </div>
                            <span class="flex-shrink-0 px-2.5 py-1 rounded-full text-xs font-bold
                                {{ $st === 'approved' ? 'bg-emerald-100 text-emerald-700' :
                                   ($st === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst($st) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>{{ $teacher->subject_specialization ?? 'N/A' }}</span>
                            <span>{{ $teacher->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex gap-2">
                            @if($teacher->status !== 'approved')
                                <form method="POST" action="{{ route('admin.teachers.approve', $teacher) }}" class="flex-1">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="w-full py-2 bg-emerald-600 text-white text-xs rounded-lg hover:bg-emerald-700 transition font-semibold">
                                        Approve
                                    </button>
                                </form>
                            @endif
                            @if($teacher->status !== 'rejected')
                                <form method="POST" action="{{ route('admin.teachers.reject', $teacher) }}" class="flex-1"
                                      onsubmit="return confirm('Reject {{ addslashes($teacher->name) }}?')">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="w-full py-2 bg-red-600 text-white text-xs rounded-lg hover:bg-red-700 transition font-semibold">
                                        Reject
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Desktop: table --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-5 py-4 font-semibold text-gray-500 uppercase text-xs tracking-wide">Teacher</th>
                            <th class="text-left px-5 py-4 font-semibold text-gray-500 uppercase text-xs tracking-wide">Specialization</th>
                            <th class="text-left px-5 py-4 font-semibold text-gray-500 uppercase text-xs tracking-wide">Registered</th>
                            <th class="text-center px-5 py-4 font-semibold text-gray-500 uppercase text-xs tracking-wide">Status</th>
                            <th class="text-center px-5 py-4 font-semibold text-gray-500 uppercase text-xs tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($teachers as $teacher)
                            @php $st = $teacher->status ?? 'approved'; @endphp
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $teacher->avatar_url }}" alt="{{ $teacher->name }}"
                                             class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                                        <div>
                                            <div class="font-medium text-gray-800">{{ $teacher->name }}</div>
                                            <div class="text-xs text-gray-400 mt-0.5">{{ $teacher->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-gray-600">{{ $teacher->subject_specialization ?? '—' }}</td>
                                <td class="px-5 py-4 text-gray-500">{{ $teacher->created_at->format('d M Y') }}</td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                                        {{ $st === 'approved' ? 'bg-emerald-100 text-emerald-700' :
                                           ($st === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                        {{ ucfirst($st) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($teacher->status !== 'approved')
                                            <form method="POST" action="{{ route('admin.teachers.approve', $teacher) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                        class="px-4 py-1.5 bg-emerald-600 text-white text-xs rounded-lg hover:bg-emerald-700 transition font-semibold">
                                                    Approve
                                                </button>
                                            </form>
                                        @endif
                                        @if($teacher->status !== 'rejected')
                                            <form method="POST" action="{{ route('admin.teachers.reject', $teacher) }}"
                                                  onsubmit="return confirm('Reject {{ addslashes($teacher->name) }}?')">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                        class="px-4 py-1.5 bg-red-600 text-white text-xs rounded-lg hover:bg-red-700 transition font-semibold">
                                                    Reject
                                                </button>
                                            </form>
                                        @endif
                                        @if($teacher->status === 'approved' && $teacher->status === 'rejected')
                                            <span class="text-xs text-gray-400">No actions</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($teachers->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-sm text-gray-500">
                        Showing {{ $teachers->firstItem() }}–{{ $teachers->lastItem() }} of {{ $teachers->total() }} teachers
                    </p>
                    <div class="text-sm">{{ $teachers->withQueryString()->links() }}</div>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
