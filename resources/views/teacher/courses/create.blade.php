@extends('layouts.teacher')

@section('title', isset($course) ? 'Edit Course' : 'Create Course - Nabha Learning')

@section('teacher-content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('teacher.courses') }}" class="text-gray-500 hover:text-gray-700">← Back</a>
        <h1 class="text-2xl font-bold text-gray-800">{{ isset($course) ? 'Edit Course' : 'Create New Course' }}</h1>
    </div>

    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
            <ul class="space-y-1">
                @foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ isset($course) ? route('teacher.courses.update', $course) : route('teacher.courses.store') }}"
          enctype="multipart/form-data"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
        @csrf
        @if(isset($course)) @method('PUT') @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Course Title *</label>
                <input type="text" name="title" value="{{ old('title', $course->title ?? '') }}" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                       placeholder="e.g., Complete Mathematics for Class 9">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Subject *</label>
                <input type="text" name="subject" value="{{ old('subject', $course->subject ?? '') }}" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                       placeholder="e.g., Mathematics, Physics, AI, Web Dev">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Class Level *</label>
                <select name="class_level" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                    <option value="">Select Class</option>
                    @foreach(['Class 6','Class 7','Class 8','Class 9','Class 10','All Classes'] as $c)
                        <option value="{{ $c }}" {{ old('class_level', $course->class_level ?? '') === $c ? 'selected':'' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Description *</label>
                <textarea name="description" rows="4" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm resize-none"
                          placeholder="What will students learn in this course?">{{ old('description', $course->description ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Price (₹) *</label>
                <input type="number" name="price" value="{{ old('price', $course->price ?? '0') }}"
                       min="0" step="0.01" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                       placeholder="0 for free">
                <p class="text-xs text-gray-400 mt-1">Enter 0 to make the course free.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Thumbnail Image</label>
                <input type="file" name="thumbnail" accept="image/*"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                @if(isset($course) && $course->thumbnail)
                    <p class="text-xs text-gray-500 mt-1">Current: {{ basename($course->thumbnail) }} (leave empty to keep)</p>
                @endif
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm text-blue-800">
            <p class="font-medium">Next Steps After Creating:</p>
            <ul class="mt-1 space-y-1 text-xs">
                <li>• Add lessons to your course from the course management page.</li>
                <li>• Submit for admin review once lessons are ready.</li>
                <li>• Students can enroll after admin approval.</li>
            </ul>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-semibold transition">
                {{ isset($course) ? 'Save Changes' : 'Create Course' }}
            </button>
            <a href="{{ route('teacher.courses') }}"
               class="px-6 py-3 border border-gray-300 text-gray-600 rounded-xl hover:bg-gray-50 transition font-medium">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
