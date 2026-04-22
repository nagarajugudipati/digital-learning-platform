<div>
    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Question / Topic</label>
    <input type="text" name="question" value="{{ old('question') }}" required
           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 {{ $accentRing }} transition"
           placeholder="e.g. what is newton law">
    @error('question')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Answer</label>
    <textarea name="answer" rows="5" required
              class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 {{ $accentRing }} transition font-mono resize-y"
              placeholder="Full answer — supports **bold** and newlines for formatting">{{ old('answer') }}</textarea>
    @error('answer')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
        Keywords
        <span class="font-normal text-gray-400">(comma-separated trigger words)</span>
    </label>
    <input type="text" name="keywords" value="{{ old('keywords') }}"
           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 {{ $accentRing }} transition"
           placeholder="newton, laws of motion, inertia, force, f=ma">
    <p class="text-xs text-gray-400 mt-1">
        The student can trigger this answer by typing any of these words.
        The more keywords you add, the more flexible the matching.
    </p>
</div>

<div class="flex gap-2">
    <button type="submit" class="{{ $accentAdd }} text-white px-5 py-2.5 rounded-xl font-semibold text-sm transition shadow-sm">
        {{ $btnLabel }}
    </button>
    <button type="reset" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 border border-gray-300 hover:bg-gray-50 transition">
        Clear
    </button>
</div>
