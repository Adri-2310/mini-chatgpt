<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\ChatService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    private ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function store(Request $request, Conversation $conversation)
    {
        if ($conversation->user_id !== auth()->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $request->validate([
            'content' => 'required|string|min:1|max:5000',
            'model' => 'required|string',
        ]);

        try {
            $userMessage = $conversation->messages()->create([
                'role' => 'user',
                'content' => $request->input('content'),
                'model' => $request->input('model'),
            ]);

            $messageHistory = $conversation->messages()
                ->orderBy('created_at')
                ->get(['role', 'content'])
                ->map(fn($msg) => ['role' => $msg->role, 'content' => $msg->content])
                ->toArray();

            $systemPrompt = null;
            $customInstruction = auth()->user()->customInstruction;
            if ($customInstruction && $customInstruction->enabled) {
                $systemPrompt = $customInstruction->instructions;
            }

            $aiResponse = $this->chatService->askWithHistory(
                $request->input('model'),
                $messageHistory,
                $systemPrompt
            );

            $assistantMessage = $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $aiResponse,
                'model' => $request->input('model'),
            ]);

            $titleUpdated = false;
            if (!$conversation->title || $conversation->title === 'Nouvelle conversation') {
                $title = substr($request->input('content'), 0, 50);
                $conversation->update(['title' => $title]);
                $titleUpdated = true;
            }

            $messages = $conversation->messages()
                ->orderBy('created_at')
                ->get(['id', 'role', 'content', 'created_at']);

            return response()->json([
                'success' => true,
                'messages' => $messages,
                'title_updated' => $titleUpdated,
                'new_title' => $titleUpdated ? $conversation->title : null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
