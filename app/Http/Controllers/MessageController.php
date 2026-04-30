<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\ChatService;
use Illuminate\Auth\Access\AuthorizationException;
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
        try {
            $this->authorize('addMessage', $conversation);
        } catch (AuthorizationException) {
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

            $model = $request->input('model');
            if ($conversation->messages()->count() === 1) {
                $conversation->update(['model_used' => $model]);
            }

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

    public function streamStore(Request $request, Conversation $conversation)
    {
        try {
            $this->authorize('addMessage', $conversation);
        } catch (AuthorizationException) {
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

            $model = $request->input('model');
            if ($conversation->messages()->count() === 1) {
                $conversation->update(['model_used' => $model]);
            }

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

            return response()->stream(function () use ($conversation, $messageHistory, $systemPrompt, $model, $request) {
                $fullResponse = '';

                // 1. Lecture du flux depuis le ChatService
                $stream = $this->chatService->streamWithHistory($model, $messageHistory, $systemPrompt);

                foreach ($stream as $chunk) {
                    if (!empty($chunk)) {
                        $fullResponse .= $chunk;
                        
                        // 2. Formatage SSE avec echo (et encodage JSON pour la sécurité des caractères)
                        echo "data: " . json_encode(['content' => $chunk]) . "\n\n";
                        
                        // 3. Vidage du buffer pour forcer l'envoi immédiat au navigateur
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    }
                }

                // 4. Une fois que l'IA a fini de parler, on sauvegarde en base de données
                $conversation->messages()->create([
                    'role' => 'assistant',
                    'content' => $fullResponse,
                    'model' => $model,
                ]);

                if (!$conversation->title || $conversation->title === 'Nouvelle conversation') {
                    $title = substr($request->input('content'), 0, 50);
                    $conversation->update(['title' => $title]);
                }

                // 5. On envoie le signal de fin au front-end
                echo "data: [DONE]\n\n";
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();

            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no',
            ]);
        } catch (\Exception $e) {
            // Note: si l'erreur se produit après le début du stream, ce JSON ne sera pas bien lu par le front, 
            // mais c'est une protection standard.
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}