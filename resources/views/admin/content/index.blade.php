@extends('layouts.admin')

@section('title', 'Content Review - Nabha Learning')

@section('admin-content')
<div class="space-y-5">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Content Review</h1>
        <p class="text-gray-500 text-sm mt-1">Review and approve lessons submitted by teachers</p>
    </div>

    <!-- Filter Tabs -->
    <div class="flex gap-2 flex-wrap">
        @php $statuses = ['' => 'All', 'pending' => 'Pending', 'published' => 'Approved', 'rejected' => 'Rejected']; @endphp
        @foreach($statuses as $value => $label)
            <a href="{{ route('admin.content', ['status' => $value]) }}"
               class="px-4 py-2 text-sm font-medium rounded-xl transition
                      {{ request('status', '') === $value
                         ? 'bg-indigo-600 text-white'
                         : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if($lessons->isEmpty())
        <div class="bg-white rounded-xl p-12 text-center shadow-sm border border-gray-100">
            <p class="text-gray-500">No lessons to review. Everything is up to date.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($lessons as $lesson)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex flex-col md:flex-row md:items-start gap-4">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="font-semibold text-gray-800">{{ $lesson->title }}</h3>
                                    <span class="text-xs px-2 py-0.5 rounded-full
                                        {{ $lesson->status === 'published' ? 'bg-emerald-100 text-emerald-700' : ($lesson->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                        {{ ucfirst($lesson->status) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 line-clamp-2">{{ $lesson->description }}</p>
                                <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                                    <span>{{ $lesson->teacher->name }}</span>
                                    <span>•</span>
                                    <span>{{ $lesson->subject }}</span>
                                    <span>•</span>
                                    <span>{{ $lesson->class_level }}</span>
                                    <span>•</span>
                                    <span>{{ $lesson->created_at->format('d M Y') }}</span>
                                </div>
                                <div class="flex gap-3 mt-2 text-xs text-gray-500">
                                    <span>{{ $lesson->view_count }} views</span>
                                    <span>{{ $lesson->download_count }} downloads</span>
                                    <span>{{ $lesson->quizzes_count }} quizzes</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="{{ route('admin.content.preview', $lesson->id) }}"
                               class="text-xs bg-indigo-100 hover:bg-indigo-200 text-indigo-700 px-3 py-2 rounded-lg transition">
                                Preview
                            </a>
                            @if($lesson->status !== 'published')
                                <form method="POST" action="{{ route('admin.content.approve', $lesson->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs bg-emerald-100 hover:bg-emerald-200 text-emerald-700 px-4 py-2 rounded-lg font-medium transition">
                                        Approve
                                    </button>
                                </form>
                            @endif
                            @if($lesson->status !== 'rejected')
                                <form method="POST" action="{{ route('admin.content.reject', $lesson->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-4 py-2 rounded-lg font-medium transition">
                                        Reject
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.content.destroy', $lesson->id) }}"
                                  onsubmit="return confirm('Permanently delete this lesson?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2 rounded-lg transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div>{{ $lessons->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
