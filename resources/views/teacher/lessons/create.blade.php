@extends('layouts.teacher')

@section('title', isset($lesson) ? 'Edit Lesson' : 'Upload Lesson - Nabha Learning')

@section('teacher-content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('teacher.lessons') }}" class="text-gray-500 hover:text-gray-700">← Back</a>
        <h1 class="text-2xl font-bold text-gray-800">{{ isset($lesson) ? 'Edit Lesson' : 'Upload New Lesson' }}</h1>
    </div>

    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
            <ul class="space-y-1">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ isset($lesson) ? route('teacher.lessons.update', $lesson->id) : route('teacher.lessons.store') }}"
          enctype="multipart/form-data"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5"
          x-data="{ fileType: '{{ old('file_type', $lesson->file_type ?? 'pdf') }}' }">
        @csrf
        @if(isset($lesson)) @method('PUT') @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Lesson Title *</label>
                <input type="text" name="title" value="{{ old('title', $lesson->title ?? '') }}" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                       placeholder="e.g., Introduction to Photosynthesis">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Subject *</label>
                <input type="text" name="subject" value="{{ old('subject', $lesson->subject ?? '') }}" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                       placeholder="e.g., Mathematics, Physics, AI, Web Dev">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Class Level *</label>
                <select name="class_level" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                    <option value="">Select Class</option>
                    @foreach(['Class 6','Class 7','Class 8','Class 9','Class 10'] as $class)
                        <option value="{{ $class }}" {{ old('class_level', $lesson->class_level ?? '') === $class ? 'selected':'' }}>{{ $class }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Description *</label>
                <textarea name="description" rows="3" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm resize-none"
                          placeholder="Brief description of what this lesson covers...">{{ old('description', $lesson->description ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Content Type *</label>
                <select name="file_type" x-model="fileType" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                    <option value="pdf">PDF Document</option>
                    <option value="video">Video</option>
                    <option value="text">Text Content (online only)</option>
                    <option value="image">Image</option>
                </select>
            </div>

            <div x-show="fileType !== 'text'">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Upload File <span class="text-gray-400">(max 50MB)</span></label>
                <input type="file" name="file"
                       :accept="fileType === 'pdf' ? '.pdf' : (fileType === 'video' ? '.mp4,.webm' : '.jpg,.jpeg,.png')"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                @if(isset($lesson) && $lesson->file_path)
                    <p class="text-xs text-gray-500 mt-1">Current file: {{ basename($lesson->file_path) }} (leave empty to keep)</p>
                @endif
            </div>

            <div x-show="fileType === 'video'">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Video Duration (minutes)</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $lesson->duration_minutes ?? '') }}" min="1"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                       placeholder="e.g., 15">
            </div>

            <div x-show="fileType === 'text'" class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Lesson Text Content</label>
                <textarea name="content" rows="10"
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                          placeholder="Type or paste your lesson content here...">{{ old('content', $lesson->content ?? '') }}</textarea>
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-800">
            <p class="font-medium">Important:</p>
            <ul class="mt-1 space-y-1 text-xs">
                <li>• Your lesson will be reviewed by Admin before it appears to students.</li>
                <li>• Upload clear, school-appropriate content aligned with the curriculum.</li>
                <li>• Supported formats: PDF, MP4/WebM videos, JPG/PNG images</li>
            </ul>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-semibold transition">
                {{ isset($lesson) ? 'Update Lesson' : 'Submit for Review' }}
            </button>
            <a href="{{ route('teacher.lessons') }}"
               class="px-6 py-3 border border-gray-300 text-gray-600 rounded-xl hover:bg-gray-50 transition font-medium">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
