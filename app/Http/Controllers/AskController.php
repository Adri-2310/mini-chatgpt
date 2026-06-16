<?php

namespace App\Http\Controllers;

use App\Models\LlmModel;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AskController extends Controller
{
    private ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function index()
    {
        return Inertia::render('Ask', [
            'models' => LlmModel::getEnabled()
                ->map(fn($m) => ['id' => $m->id, 'model_id' => $m->model_id, 'name' => $m->name, 'provider' => $m->provider]),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|min:5|max:2000',
            'model' => ['required', 'string', Rule::in(LlmModel::getEnabled()->pluck('model_id'))],
        ]);

        try {
            $systemPrompt = $this->chatService->buildSystemPrompt();

            $result = $this->chatService->ask(
                $request->input('model'),
                $request->input('question'),
                $systemPrompt
            );

            return response()->json([
                'success' => true,
                'response' => $result['content'],
                'tokens' => $result['tokens'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function stream(Request $request)
    {
        $request->validate([
            'question' => 'required|string|min:5|max:2000',
            'model' => ['required', 'string', Rule::in(LlmModel::getEnabled()->pluck('model_id'))],
        ]);

        try {
            $systemPrompt = $this->chatService->buildSystemPrompt();

            return response()->stream(function () use ($request, $systemPrompt) {
                $stream = $this->chatService->streamAsk(
                    $request->input('model'),
                    $request->input('question'),
                    $systemPrompt
                );

                foreach ($stream as $chunk) {
                    if (!empty($chunk)) {
                        // 1. Formatage strict SSE
                        // On encode en JSON pour éviter de casser le flux si le LLM génère des retours à la ligne
                        echo "data: " . json_encode(['content' => $chunk]) . "\n\n";

                        // 2. Vidage des buffers pour forcer l'envoi immédiat
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    }
                }

                // 3. Envoyer l'utilisation des tokens au frontend avant de fermer
                $tokensUsed = $this->chatService->getLastStreamTokens();
                if ($tokensUsed) {
                    echo "data: " . json_encode(['type' => 'usage', 'tokens' => $tokensUsed]) . "\n\n";
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                }

                // 4. Signal de fin
                echo "data: [DONE]\n\n";
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();

            }, 200, [
                'Content-Type' => 'text/event-stream', // Pas besoin du charset ici en général
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no',
            ]);
        } catch (\Exception $e) {
            // Attention : Si le flux a déjà commencé, vous ne pourrez pas renvoyer un JSON d'erreur propre.
            // Mais pour l'initialisation, ceci est correct.
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
