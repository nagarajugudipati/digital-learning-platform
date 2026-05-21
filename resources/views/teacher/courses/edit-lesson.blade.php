@extends('layouts.teacher')

@section('title', 'Edit Lesson — ' . $course->title)

@php
$blocksJson = $lesson->contents->map(fn($c) => [
    'id'           => $c->id,
    'type'         => $c->type,
    'title'        => $c->title ?? '',
    'content_text' => $c->content_text ?? '',
    'existingFile' => $c->file_path ? basename($c->file_path) : null,
    'fileName'     => '',
    'charCount'    => mb_strlen($c->content_text ?? ''),
])->toJson(JSON_HEX_APOS);
@endphp

@section('teacher-content')
<div class="max-w-3xl mx-auto" x-data="lessonEditor({{ $blocksJson }})">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('teacher.courses.show', $course) }}"
           class="text-gray-500 hover:text-gray-800 text-sm transition">
            ← Back
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Lesson</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Course: <span class="font-semibold text-indigo-600">{{ $course->title }}</span>
            </p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-4 text-sm">
            <p class="font-semibold mb-1">Please fix the following errors:</p>
            <ul class="space-y-1 list-disc list-inside">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('teacher.courses.update-lesson', [$course, $lesson]) }}"
          enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        {{-- Hidden deleted_ids[] inputs --}}
        <template x-for="delId in deletedIds" :key="delId">
            <input type="hidden" name="deleted_ids[]" :value="delId">
        </template>

        {{-- Lesson Details --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            <h2 class="font-bold text-gray-800 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">1</span>
                Lesson Details
            </h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Lesson Title <span class="text-red-400">*</span></label>
                <input type="text" name="title" value="{{ old('title', $lesson->title) }}" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Description <span class="text-red-400">*</span></label>
                <textarea name="description" rows="3" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm resize-none">{{ old('description', $lesson->description) }}</textarea>
            </div>
        </div>

        {{-- Content Blocks --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">2</span>
                    Content Blocks
                </h2>
                <button type="button" @click="addBlock()"
                        class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl transition font-medium">
                    Add Content Block
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(block, index) in blocks" :key="block._key">
                    <div class="border rounded-2xl overflow-hidden transition-all duration-200"
                         :class="block.type === 'text' ? 'border-purple-200 bg-purple-50/30'
                                : block.type === 'video' ? 'border-blue-200 bg-blue-50/30'
                                : block.type === 'image' ? 'border-pink-200 bg-pink-50/30'
                                : 'border-orange-200 bg-orange-50/30'">

                        {{-- Block header --}}
                        <div class="flex items-center gap-3 px-4 py-3 border-b"
                             :class="block.type === 'text' ? 'border-purple-200 bg-purple-50'
                                    : block.type === 'video' ? 'border-blue-200 bg-blue-50'
                                    : block.type === 'image' ? 'border-pink-200 bg-pink-50'
                                    : 'border-orange-200 bg-orange-50'">

                            <div class="flex-1 flex items-center gap-3">
                                <span class="text-sm font-bold text-gray-700" x-text="'Block ' + (index + 1)"></span>
                                <span x-show="block.id" class="text-xs bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded-full font-medium">existing</span>
                                <span class="text-xs px-2 py-0.5 rounded-full font-semibold uppercase tracking-wide"
                                      :class="block.type === 'text' ? 'bg-purple-100 text-purple-700'
                                             : block.type === 'video' ? 'bg-blue-100 text-blue-700'
                                             : block.type === 'image' ? 'bg-pink-100 text-pink-700'
                                             : 'bg-orange-100 text-orange-700'"
                                      x-text="block.type"></span>
                            </div>

                            <button type="button" @click="removeBlock(index)"
                                    x-show="blocks.length > 1"
                                    class="text-xs text-red-500 hover:text-red-700 hover:bg-red-50 px-2 py-1 rounded-lg transition font-medium">
                                Remove
                            </button>
                        </div>

                        {{-- Hidden id for existing blocks --}}
                        <input type="hidden" :name="'contents[' + index + '][id]'" :value="block.id || ''">

                        {{-- Block fields --}}
                        <div class="p-4 space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Content Type <span class="text-red-400">*</span></label>
                                    <select :name="'contents[' + index + '][type]'" x-model="block.type" required
                                            class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                                        <option value="pdf">PDF Document</option>
                                        <option value="video">Video</option>
                                        <option value="image">Image</option>
                                        <option value="text">Text / Notes</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Block Title</label>
                                    <input type="text" :name="'contents[' + index + '][title]'"
                                           x-model="block.title"
                                           placeholder="Optional"
                                           class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>

                            {{-- File upload --}}
                            <div x-show="block.type !== 'text'" x-transition>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Upload File
                                    <span class="text-gray-400 font-normal ml-1">(max 50MB — <span x-text="acceptLabel(block.type)"></span>)</span>
                                </label>

                                {{-- Existing file badge --}}
                                <p x-show="block.existingFile && !block.fileName"
                                   class="text-xs text-indigo-600 mb-2 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>
                                    Current: <span x-text="block.existingFile" class="font-medium"></span>
                                </p>

                                <label class="flex items-center gap-3 border-2 border-dashed border-gray-300 hover:border-indigo-400 rounded-xl p-4 cursor-pointer transition group">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-600 group-hover:text-indigo-600 transition font-medium">
                                            <span x-text="block.existingFile ? 'Replace file' : 'Choose file'"></span>
                                        </p>
                                        <p class="text-xs text-gray-400" x-text="acceptLabel(block.type)"></p>
                                    </div>
                                    <input type="file"
                                           :name="'contents[' + index + '][file]'"
                                           :accept="acceptAttr(block.type)"
                                           @change="block.fileName = $event.target.files[0]?.name ?? ''"
                                           class="hidden">
                                </label>
                                <p x-show="block.fileName" class="text-xs text-emerald-600 mt-1.5">
                                    New file: <span x-text="block.fileName"></span>
                                </p>
                            </div>

                            {{-- Text content --}}
                            <div x-show="block.type === 'text'" x-transition>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Text Content <span class="text-red-400">*</span></label>
                                <textarea :name="'contents[' + index + '][content_text]'"
                                          x-model="block.content_text"
                                          rows="8"
                                          @input="block.charCount = $event.target.value.length"
                                          placeholder="Type or paste your lesson notes here..."
                                          class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 resize-y"></textarea>
                                <p class="text-xs text-gray-400 text-right mt-1">
                                    <span x-text="block.charCount"></span> characters
                                </p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <button type="button" @click="addBlock()"
                    class="w-full border-2 border-dashed border-gray-300 hover:border-indigo-400 hover:bg-indigo-50/50 text-gray-500 hover:text-indigo-600 py-3.5 rounded-2xl text-sm font-medium transition">
                Add Another Content Block
            </button>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3.5 rounded-2xl font-bold transition">
                Save Changes
            </button>
            <a href="{{ route('teacher.courses.show', $course) }}"
               class="px-6 py-3.5 border border-gray-300 text-gray-600 rounded-2xl hover:bg-gray-50 transition font-medium">
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function lessonEditor(existingBlocks) {
    return {
        blocks: existingBlocks.map((b, i) => ({ ...b, _key: i })),
        deletedIds: [],
        _counter: existingBlocks.length,

        addBlock() {
            this.blocks.push({
                _key: ++this._counter,
                id: null,
                type: 'pdf',
                title: '',
                content_text: '',
                existingFile: null,
                fileName: '',
                charCount: 0,
            });
        },

        removeBlock(index) {
            const block = this.blocks[index];
            if (block.id) {
                this.deletedIds.push(block.id);
            }
            this.blocks.splice(index, 1);
        },

        acceptLabel(type) {
            return { pdf: 'PDF files only', video: 'MP4 or WebM', image: 'JPG, PNG', text: '' }[type] ?? '';
        },

        acceptAttr(type) {
            return { pdf: '.pdf', video: '.mp4,.webm', image: '.jpg,.jpeg,.png,.gif,.webp', text: '' }[type] ?? '';
        },
    };
}
</script>
@endpush
@endsection
