@php
use Illuminate\Support\Str;
$accentAdd = $routePrefix === 'admin' ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-emerald-600 hover:bg-emerald-700';
$accentRing = $routePrefix === 'admin' ? 'focus:ring-indigo-500' : 'focus:ring-emerald-500';
@endphp

<div class="space-y-6 max-w-6xl" x-data="qaManager()">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">🤖 Chatbot Q&A Training</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Add questions &amp; answers to train the offline chatbot.
                Total: <strong>{{ $qaList->total() }}</strong> entries
            </p>
        </div>
        <button @click="showForm = !showForm"
                class="{{ $accentAdd }} text-white px-5 py-2.5 rounded-xl font-semibold text-sm transition shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span x-text="showForm ? 'Cancel' : 'Add New Q&A'"></span>
        </button>
    </div>

    {{-- ── Add / Edit Form ── --}}
    <div x-show="showForm" x-transition x-cloak
         class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-bold text-gray-800 mb-4 text-base" x-text="editId ? '✏️ Edit Q&A Entry' : '➕ Add New Q&A Entry'"></h2>

        {{-- Add form --}}
        <form id="qa-add-form" method="POST" action="{{ route($routePrefix . '.chatbot-qa.store') }}"
              x-show="!editId" class="space-y-4">
            @csrf
            @include('chatbot-qa._form-fields', ['accentRing' => $accentRing, 'accentAdd' => $accentAdd, 'btnLabel' => 'Add to Knowledge Base'])
        </form>

        {{-- Edit forms (one per entry, shown via Alpine) --}}
        @foreach($qaList as $qa)
            <form id="qa-edit-form-{{ $qa->id }}" method="POST"
                  action="{{ route($routePrefix . '.chatbot-qa.update', $qa) }}"
                  x-show="editId === {{ $qa->id }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Question / Topic</label>
                    <input type="text" name="question" value="{{ old('question', $qa->question) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 {{ $accentRing }} transition"
                           placeholder="e.g. what is newton law">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Answer</label>
                    <textarea name="answer" rows="5" required
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 {{ $accentRing }} transition font-mono resize-y"
                              placeholder="Full answer — supports **bold** and newlines">{{ old('answer', $qa->answer) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keywords <span class="font-normal text-gray-400">(comma-separated trigger words)</span></label>
                    <input type="text" name="keywords" value="{{ old('keywords', $qa->keywords) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 {{ $accentRing }} transition"
                           placeholder="newton, laws of motion, inertia, force">
                    <p class="text-xs text-gray-400 mt-1">Student can trigger this answer by typing any of these words.</p>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="{{ $accentAdd }} text-white px-5 py-2.5 rounded-xl font-semibold text-sm transition">
                        Save Changes
                    </button>
                    <button type="button" @click="editId = null; showForm = false"
                            class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 border border-gray-300 hover:bg-gray-50 transition">
                        Cancel
                    </button>
                </div>
            </form>
        @endforeach
    </div>

    {{-- ── Search ── --}}
    <form method="GET" action="{{ route($routePrefix . '.chatbot-qa') }}" data-no-loading class="flex gap-3">
        <input type="text" name="search" value="{{ $search }}"
               placeholder="Search questions or keywords..."
               class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 {{ $accentRing }} transition">
        <button type="submit" class="px-5 py-2.5 bg-gray-700 hover:bg-gray-800 text-white rounded-xl text-sm font-semibold transition">
            Search
        </button>
        @if($search)
            <a href="{{ route($routePrefix . '.chatbot-qa') }}"
               class="px-4 py-2.5 border border-gray-300 text-gray-600 rounded-xl text-sm hover:bg-gray-50 transition">
                Clear
            </a>
        @endif
    </form>

    {{-- ── Q&A Table ── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($qaList->isEmpty())
            <div class="py-16 text-center">
                <div class="text-5xl mb-3">🤖</div>
                <h3 class="font-bold text-gray-700 mb-1">No Q&A entries yet</h3>
                <p class="text-sm text-gray-400">Add your first question and answer to start training the chatbot.</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-8">#</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Question</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Keywords</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Added by</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($qaList as $qa)
                        <tr class="hover:bg-gray-50/60 transition group"
                            :class="editId === {{ $qa->id }} ? 'bg-amber-50' : ''">
                            <td class="px-5 py-4 text-gray-400 text-xs">{{ $qa->id }}</td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-gray-800 text-sm">{{ $qa->question }}</p>
                                <p class="text-xs text-gray-400 mt-0.5 line-clamp-2">{{ Str::limit(str_replace(['**', "\n"], ['', ' '], $qa->answer), 90) }}</p>
                            </td>
                            <td class="px-5 py-4 hidden lg:table-cell">
                                @if($qa->keywords)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(array_slice($qa->keywords_array, 0, 4) as $kw)
                                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ $kw }}</span>
                                        @endforeach
                                        @if(count($qa->keywords_array) > 4)
                                            <span class="text-xs text-gray-400">+{{ count($qa->keywords_array) - 4 }} more</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-300 italic">none</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 hidden md:table-cell text-xs text-gray-500">
                                {{ $qa->creator?->name ?? 'Seeder' }}
                                <span class="block text-gray-400">{{ $qa->created_at->format('d M Y') }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition">
                                    {{-- Edit --}}
                                    <button @click="editId = {{ $qa->id }}; showForm = true; $nextTick(() => $el.closest('.space-y-6').querySelector('#qa-add-form')?.closest('div')?.scrollIntoView({behavior:'smooth'}))"
                                            class="text-xs text-indigo-600 hover:text-indigo-800 font-medium px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition">
                                        ✏️ Edit
                                    </button>
                                    {{-- Delete --}}
                                    <form method="POST" action="{{ route($routePrefix . '.chatbot-qa.destroy', $qa) }}"
                                          onsubmit="return confirm('Delete this Q&A entry?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="text-xs text-red-500 hover:text-red-700 font-medium px-3 py-1.5 rounded-lg hover:bg-red-50 transition">
                                            🗑 Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            @if($qaList->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $qaList->links() }}
                </div>
            @endif
        @endif
    </div>

</div>

@push('scripts')
<script>
function qaManager() {
    return {
        showForm: {{ $errors->any() ? 'true' : 'false' }},
        editId: null,
    };
}
</script>
@endpush
