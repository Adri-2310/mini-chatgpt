<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConversationController extends Controller
{
    public function chat()
    {
        $conversations = auth()->user()->conversations()
            ->orderByDesc('updated_at')
            ->get(['id', 'title', 'model_used', 'updated_at']);

        return Inertia::render('Chat', [
            'conversations' => $conversations,
            'models' => config('ai_models.available'),
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
            'title' => $request->input('title') ?? 'Nouvelle conversation',
            'model_used' => $request->input('model') ?? config('ai_models.default'),
        ]);

        return response()->json($conversation, 201);
    }

    public function show(Conversation $conversation)
    {
        try {
            $this->authorize('view', $conversation);
        } catch (AuthorizationException) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $messages = $conversation->messages()
            ->orderBy('created_at')
            ->get(['id', 'role', 'content', 'tokens_used', 'created_at']);

        return response()->json([
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    public function update(Request $request, Conversation $conversation)
    {
        try {
            $this->authorize('update', $conversation);
        } catch (AuthorizationException) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $request->validate([
            'title' => 'required|string|min:1|max:255',
        ]);

        $conversation->update([
            'title' => $request->input('title'),
        ]);

        return response()->json($conversation);
    }

    public function destroy(Conversation $conversation)
    {
        try {
            $this->authorize('delete', $conversation);
        } catch (AuthorizationException) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $conversation->messages()->delete();
        $conversation->delete();

        return response()->json(['success' => true]);
    }
}
