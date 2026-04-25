<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConversationController extends Controller
{
    public function chat()
    {
        $conversations = auth()->user()->conversations()
            ->orderByDesc('updated_at')
            ->get(['id', 'title', 'model_used', 'updated_at']);

        $models = [
            ['id' => 'openai/gpt-4o-mini', 'name' => 'GPT-4o mini', 'provider' => 'OpenAI'],
            ['id' => 'google/gemini-3-flash-preview', 'name' => 'Gemini 3 Flash Preview', 'provider' => 'Google'],
            ['id' => 'anthropic/claude-3.5-haiku', 'name' => 'Claude 3.5 Haiku', 'provider' => 'Anthropic'],
        ];

        return Inertia::render('Chat', [
            'conversations' => $conversations,
            'models' => $models,
        ]);
    }

    public function index()
    {
        $conversations = auth()->user()->conversations()
            ->orderByDesc('updated_at')
            ->get(['id', 'title', 'model_used', 'updated_at']);

        return response()->json($conversations);
    }

    public function store(Request $request)
    {
        $conversation = auth()->user()->conversations()->create([
            'title' => 'Nouvelle conversation',
            'model_used' => $request->input('model') ?? 'openai/gpt-4o',
        ]);

        return response()->json($conversation);
    }

    public function show(Conversation $conversation)
    {
        if ($conversation->user_id !== auth()->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $messages = $conversation->messages()
            ->orderBy('created_at')
            ->get(['id', 'role', 'content', 'created_at']);

        return response()->json([
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    public function destroy(Conversation $conversation)
    {
        if ($conversation->user_id !== auth()->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $conversation->messages()->delete();
        $conversation->delete();

        return response()->json(['success' => true]);
    }
}
