<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\ChatService;
use App\Traits\CalculateCosts;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    use CalculateCosts;

    private ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Crée un message utilisateur et génère une réponse IA (non-streaming)
     *
     * @param Request $request Contient 'content' et 'model'
     * @param Conversation $conversation La conversation cible
     * @return \Illuminate\Http\JsonResponse
     */
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
            $model = $request->input('model');

            $userMessage = $conversation->messages()->create([
                'role' => 'user',
                'content' => $request->input('content'),
                'model' => $model,
            ]);

            if ($conversation->messages()->count() === 1) {
                $conversation->update(['model_used' => $model]);
            }

            ['messageHistory' => $messageHistory, 'systemPrompt' => $systemPrompt] = $this->prepareMessageContext($conversation);

            $aiResult = $this->chatService->askWithHistory(
                $request->input('model'),
                $messageHistory,
                $systemPrompt
            );

            $inputTokens = $aiResult['input_tokens'] ?? 0;
            $outputTokens = $aiResult['output_tokens'] ?? 0;
            $cost = $this->calculateMessageCost(
                $request->input('model'),
                $inputTokens,
                $outputTokens
            );

            // Sauvegarder juste tokens_used (l'API le retourne correctement)
            // Le coût sera calculé dynamiquement depuis les stats
            $assistantMessage = $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $aiResult['content'],
                'model' => $request->input('model'),
                'tokens_used' => $aiResult['tokens'],
            ]);

            $titleUpdated = false;
            if (!$conversation->title || $conversation->title === 'Nouvelle conversation') {
                $messageCount = $conversation->messages()->count();

                if ($messageCount >= 4) {
                    $title = $this->generateConversationTitle($conversation, $request->input('model'));
                    $conversation->update(['title' => $title]);
                    $titleUpdated = true;
                }
            }

            $messages = $conversation->messages()
                ->orderBy('created_at')
                ->get(['id', 'role', 'content', 'tokens_used', 'created_at']);

            return response()->json([
                'success' => true,
                'messages' => $messages,
                'title_updated' => $titleUpdated,
                'new_title' => $titleUpdated ? $conversation->title : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Message store failed', [
                'conversation_id' => $conversation->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Génère un titre court pour la conversation basé sur les 4 premiers messages
     *
     * @param Conversation $conversation
     * @param string $model
     * @return string Titre généré (2-4 mots, max 50 caractères)
     */
    private function generateConversationTitle(Conversation $conversation, string $model): string
    {
        $messages = $conversation->messages()
            ->orderBy('created_at')
            ->limit(4)
            ->get(['role', 'content'])
            ->map(fn($msg) => ['role' => $msg->role, 'content' => $msg->content])
            ->toArray();

        $conversationContext = "Réponds UNIQUEMENT par le titre, rien d'autre.\nGénère un titre logique et court (3-5 mots, max 40 caractères) basé sur cette conversation.\nIdentifie l'action/intention ET l'objet principal.\n\nExemples de réponses attendues:\n- Recette Poulet Rapide\n- Recette Ingrédients Frigo\n- Configuration Docker Laravel\n\nConversation:\n";

        foreach ($messages as $msg) {
            $role = $msg['role'] === 'user' ? 'User' : 'AI';
            $conversationContext .= "$role: " . substr($msg['content'], 0, 100) . "\n";
        }
        $conversationContext .= "\nRéponse (TITRE UNIQUEMENT):";

        try {
            $result = $this->chatService->ask($model, $conversationContext);
            $title = trim($result['content']);
            $title = trim($title, '\'"');

            if (strlen($title) > 50) {
                $title = substr($title, 0, 50);
            }

            return !empty($title) ? $title : 'Conversation';
        } catch (\Exception $e) {
            return substr($messages[0]['content'] ?? 'Conversation', 0, 40);
        }
    }

    /**
     * Prépare le contexte du message (historique + system prompt)
     *
     * @param Conversation $conversation
     * @return array ['messageHistory' => array, 'systemPrompt' => string]
     */
    private function prepareMessageContext(Conversation $conversation): array
    {
        $messageHistory = $conversation->messages()
            ->orderBy('created_at')
            ->get(['role', 'content'])
            ->map(fn($msg) => ['role' => $msg->role, 'content' => $msg->content])
            ->toArray();

        $systemPrompt = config('saveurial.default_system_prompt');
        $customInstruction = auth()->user()->customInstruction;
        if ($customInstruction && $customInstruction->enabled && $customInstruction->instructions) {
            $systemPrompt .= "\n\n" . $customInstruction->instructions;
        }

        return ['messageHistory' => $messageHistory, 'systemPrompt' => $systemPrompt];
    }

    /**
     * Crée un message utilisateur et génère une réponse IA avec streaming SSE
     *
     * @param Request $request Contient 'content' et 'model'
     * @param Conversation $conversation La conversation cible
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
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
            $model = $request->input('model');

            $userMessage = $conversation->messages()->create([
                'role' => 'user',
                'content' => $request->input('content'),
                'model' => $model,
            ]);

            if ($conversation->messages()->count() === 1) {
                $conversation->update(['model_used' => $model]);
            }

            ['messageHistory' => $messageHistory, 'systemPrompt' => $systemPrompt] = $this->prepareMessageContext($conversation);

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

                // 4. Sauvegarder le message avec les tokens du streaming
                // Note: tokens_used vient de getLastStreamTokens() qui capture total_tokens de l'API
                $tokensUsed = $this->chatService->getLastStreamTokens();

                $conversation->messages()->create([
                    'role' => 'assistant',
                    'content' => $fullResponse,
                    'model' => $model,
                    'tokens_used' => $tokensUsed,
                ]);

                if (!$conversation->title || $conversation->title === 'Nouvelle conversation') {
                    $messageCount = $conversation->messages()->count();
                    if ($messageCount >= 4) {
                        $title = $this->generateConversationTitle($conversation, $model);
                        $conversation->update(['title' => $title]);
                    }
                }

                // 5. Envoyer l'utilisation des tokens au frontend avant de fermer
                if ($tokensUsed) {
                    echo "data: " . json_encode(['type' => 'usage', 'tokens' => $tokensUsed]) . "\n\n";
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                }

                // 6. Envoyer le signal de fin au frontend
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
            Log::error('Message stream store failed', [
                'conversation_id' => $conversation->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}