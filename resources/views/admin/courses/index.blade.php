@extends('layouts.admin')

@section('title', 'Course Approval - Nabha Learning')

@section('admin-content')
<div class="space-y-5">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Course Approval</h1>
        <p class="text-gray-500 text-sm mt-1">Review and approve courses submitted by teachers</p>
    </div>

    {{-- Filter tabs --}}
    <div class="flex gap-2 flex-wrap">
        @foreach(['' => 'All', 'pending' => 'Pending', 'published' => 'Approved', 'rejected' => 'Rejected', 'draft' => 'Draft'] as $val => $label)
            <a href="{{ route('admin.courses', ['status' => $val]) }}"
               class="px-4 py-2 text-sm font-medium rounded-xl transition
                      {{ request('status', '') === $val ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if($courses->isEmpty())
        <div class="bg-white rounded-xl p-12 text-center shadow-sm border border-gray-100">
            <p class="text-gray-500">No courses to review.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($courses as $course)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex flex-col md:flex-row md:items-start gap-4">
                        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                             class="w-24 h-16 object-cover rounded-lg flex-shrink-0">

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-semibold text-gray-800">{{ $course->title }}</h3>
                                @php $sc = ['draft'=>'bg-gray-100 text-gray-600','pending'=>'bg-yellow-100 text-yellow-700','published'=>'bg-emerald-100 text-emerald-700','rejected'=>'bg-red-100 text-red-700']; @endphp
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $sc[$course->status] ?? '' }}">
                                    {{ ucfirst($course->status) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 line-clamp-2">{{ $course->description }}</p>
                            <div class="flex flex-wrap gap-3 mt-2 text-xs text-gray-500">
                                <span>{{ $course->teacher->name }}</span>
                                <span>•</span>
                                <span>{{ $course->lessons_count }} lessons</span>
                                <span>•</span>
                                <span>{{ $course->enrollments_count }} enrolled</span>
                                <span>•</span>
                                <span>{{ $course->class_level }} | {{ $course->subject }}</span>
                                <span>•</span>
                                <span class="font-semibold text-emerald-700">{{ $course->price > 0 ? '₹' . number_format($course->price, 2) : 'Free' }}</span>
                                <span>•</span>
                                <span>{{ $course->created_at->format('d M Y') }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="{{ route('admin.courses.preview', $course) }}"
                               class="text-xs bg-indigo-100 hover:bg-indigo-200 text-indigo-700 px-3 py-2 rounded-lg transition">
                                Preview
                            </a>
                            @if($course->status !== 'published')
                                <form method="POST" action="{{ route('admin.courses.approve', $course) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs bg-emerald-100 hover:bg-emerald-200 text-emerald-700 px-4 py-2 rounded-lg font-medium transition">
                                        Approve
                                    </button>
                                </form>
                            @endif
                            @if($course->status !== 'rejected')
                                <form method="POST" action="{{ route('admin.courses.reject', $course) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-4 py-2 rounded-lg font-medium transition">
                                        Reject
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                                  onsubmit="return confirm('Permanently delete this course?')">
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
        <div>{{ $courses->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
