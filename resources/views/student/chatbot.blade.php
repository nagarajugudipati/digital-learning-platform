@extends('layouts.student')

@section('title', 'AI Study Assistant - Nabha Learning')

@section('student-content')
<div class="max-w-3xl mx-auto h-full">
    <div class="flex flex-col bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" style="height: calc(100vh - 160px); min-height: 500px;">
        <!-- Chat Header -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white p-4 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center font-bold text-sm">AI</div>
                <div>
                    <h2 class="font-bold">AI Study Assistant</h2>
                    <p class="text-xs text-purple-200">Powered by rule-based NLP | Available 24/7</p>
                </div>
                <div class="ml-auto flex items-center gap-1.5">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    <span class="text-xs text-purple-200">Online</span>
                </div>
            </div>
        </div>

        <!-- Suggested Topics -->
        <div class="px-4 py-3 bg-purple-50 border-b border-purple-100 flex-shrink-0">
            <p class="text-xs text-purple-600 font-medium mb-2">Quick Topics:</p>
            <div class="flex flex-wrap gap-2">
                @foreach(['Pythagoras Theorem', 'Photosynthesis', 'Newton\'s Laws', 'Fractions', 'Punjab Facts', 'Indian Constitution'] as $topic)
                    <button onclick="sendQuickMessage('{{ $topic }}')"
                            class="text-xs bg-white text-purple-700 border border-purple-200 px-3 py-1 rounded-full hover:bg-purple-100 transition">
                        {{ $topic }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Chat Messages -->
        <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4">
            <!-- Welcome message -->
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center text-xs font-bold text-purple-700 flex-shrink-0">AI</div>
                <div class="bg-purple-50 rounded-2xl rounded-tl-none p-3 max-w-sm">
                    <p class="text-sm text-gray-700">Namaste! I'm your AI study assistant for Nabha Digital School.</p>
                    <p class="text-sm text-gray-700 mt-2">I can help you with <strong>Mathematics, Science, English, Hindi,</strong> and <strong>Social Studies</strong>.</p>
                    <p class="text-sm text-gray-700 mt-2">Type <strong>help</strong> to see all topics, or just ask your question!</p>
                    <p class="text-xs text-gray-400 mt-2">Just now</p>
                </div>
            </div>

            <!-- Previous chat logs -->
            @foreach($recentChats as $log)
                <!-- User message -->
                <div class="flex items-end justify-end gap-3">
                    <div class="bg-primary-600 text-white rounded-2xl rounded-br-none p-3 max-w-sm">
                        <p class="text-sm chat-bubble">{{ $log->message }}</p>
                        <p class="text-xs text-primary-200 mt-1 text-right">{{ $log->created_at->format('h:i A') }}</p>
                    </div>
                    <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center text-xs font-bold text-primary-700 flex-shrink-0">You</div>
                </div>
                <!-- Bot response -->
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center text-xs font-bold text-purple-700 flex-shrink-0">AI</div>
                    <div class="bg-purple-50 rounded-2xl rounded-tl-none p-3 max-w-lg">
                        <div class="text-sm text-gray-700 chat-bubble formatted-response">{{ $log->response }}</div>
                        @if($log->subject)
                            <span class="inline-block mt-1.5 text-xs bg-purple-200 text-purple-700 px-2 py-0.5 rounded-full">{{ ucfirst($log->subject) }}</span>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">{{ $log->created_at->format('h:i A') }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Chat Input -->
        <div class="p-4 border-t border-gray-200 flex-shrink-0 bg-white">
            <div id="typing-indicator" class="hidden text-xs text-gray-400 mb-2">
                AI is thinking...
            </div>
            <form id="chat-form" class="flex gap-3">
                @csrf
                <input type="text" id="chat-input"
                       placeholder="Ask me anything about your syllabus..."
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                       maxlength="500" autocomplete="off">
                <button type="submit"
                        class="px-5 py-3 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition font-medium text-sm">
                    Send
                </button>
            </form>
            <p class="text-xs text-gray-400 mt-2 text-center">Press Enter to send • This AI covers Classes 6-10 syllabus topics</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
const chatMessages = document.getElementById('chat-messages');
const chatForm = document.getElementById('chat-form');
const chatInput = document.getElementById('chat-input');
const typingIndicator = document.getElementById('typing-indicator');

chatMessages.scrollTop = chatMessages.scrollHeight;

function sendQuickMessage(text) {
    chatInput.value = text;
    chatForm.dispatchEvent(new Event('submit'));
}

function formatResponse(text) {
    return text
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\n/g, '<br>')
        .replace(/•/g, '&bull;');
}

function appendMessage(message, isUser) {
    const time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    const div = document.createElement('div');

    if (isUser) {
        div.className = 'flex items-end justify-end gap-3';
        div.innerHTML = `
            <div class="bg-primary-600 text-white rounded-2xl rounded-br-none p-3 max-w-sm">
                <p class="text-sm">${message.replace(/</g,'&lt;')}</p>
                <p class="text-xs text-primary-200 mt-1 text-right">${time}</p>
            </div>
            <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center text-xs font-bold text-primary-700 flex-shrink-0">You</div>
        `;
    } else {
        div.className = 'flex items-start gap-3';
        div.innerHTML = `
            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center text-xs font-bold text-purple-700 flex-shrink-0">AI</div>
            <div class="bg-purple-50 rounded-2xl rounded-tl-none p-3 max-w-lg">
                <div class="text-sm text-gray-700">${formatResponse(message.response)}</div>
                ${message.subject ? `<span class="inline-block mt-1.5 text-xs bg-purple-200 text-purple-700 px-2 py-0.5 rounded-full">${message.subject}</span>` : ''}
                <p class="text-xs text-gray-400 mt-1">${time}</p>
            </div>
        `;
    }

    chatMessages.appendChild(div);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

chatForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const message = chatInput.value.trim();
    if (!message) return;

    appendMessage(message, true);
    chatInput.value = '';
    typingIndicator.classList.remove('hidden');
    chatMessages.scrollTop = chatMessages.scrollHeight;

    try {
        const response = await fetch('{{ route("student.chatbot.chat") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message })
        });

        const data = await response.json();
        typingIndicator.classList.add('hidden');

        if (data.success) {
            appendMessage(data, false);
        }
    } catch (err) {
        typingIndicator.classList.add('hidden');
        appendMessage({ response: "Sorry, I couldn't connect right now. Please check your internet connection.", subject: null }, false);
    }
});

chatInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        chatForm.dispatchEvent(new Event('submit'));
    }
});
</script>
@endpush
@endsection
