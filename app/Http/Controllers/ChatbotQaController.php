<?php

namespace App\Http\Controllers;

use App\Models\ChatbotQA;
use Illuminate\Http\Request;

class ChatbotQaController extends Controller
{
    /** Shared index: admin sees admin layout, teacher sees teacher layout. */
    public function index(Request $request)
    {
        $query = ChatbotQA::with('creator')->latest();

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('question', 'LIKE', "%{$search}%")
                  ->orWhere('keywords', 'LIKE', "%{$search}%");
            });
        }

        $qaList = $query->paginate(20)->withQueryString();

        $view = auth()->user()->isAdmin()
            ? 'admin.chatbot-qa.index'
            : 'teacher.chatbot-qa.index';

        return view($view, compact('qaList', 'search'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'answer'   => ['required', 'string'],
            'keywords' => ['nullable', 'string', 'max:500'],
        ]);

        ChatbotQA::create([...$data, 'created_by' => auth()->id()]);

        return back()->with('success', 'Q&A added to chatbot knowledge base!');
    }

    public function update(Request $request, ChatbotQA $qa)
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'answer'   => ['required', 'string'],
            'keywords' => ['nullable', 'string', 'max:500'],
        ]);

        $qa->update($data);

        return back()->with('success', 'Q&A updated successfully.');
    }

    public function destroy(ChatbotQA $qa)
    {
        $qa->delete();
        return back()->with('success', 'Q&A entry deleted.');
    }
}
