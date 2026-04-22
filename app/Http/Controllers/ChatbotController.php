<?php

namespace App\Http\Controllers;

use App\Models\ChatbotLog;
use App\Services\ChatbotService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function __construct(private ChatbotService $chatbot) {}

    public function index()
    {
        $recentChats = ChatbotLog::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        return view('student.chatbot', compact('recentChats'));
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        $message = trim($request->input('message'));
        $result = $this->chatbot->respond($message);

        $log = ChatbotLog::create([
            'user_id' => auth()->id(),
            'message' => $message,
            'response' => $result['response'],
            'intent' => $result['intent'],
            'subject' => $result['subject'],
            'confidence' => $result['confidence'],
            'session_id' => session()->getId(),
        ]);

        return response()->json([
            'success' => true,
            'response' => $result['response'],
            'intent' => $result['intent'],
            'subject' => $result['subject'],
            'confidence' => $result['confidence'],
            'log_id' => $log->id,
        ]);
    }

    public function feedback(Request $request, ChatbotLog $log)
    {
        $request->validate(['helpful' => ['required', 'boolean']]);
        $log->update(['was_helpful' => $request->boolean('helpful')]);
        return response()->json(['success' => true]);
    }
}
