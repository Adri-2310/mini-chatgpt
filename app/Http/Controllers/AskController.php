<?php

namespace App\Http\Controllers;

use App\Services\ChatService;
use Illuminate\Http\Request;
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
        $models = [
            [
                'id' => 'openai/gpt-4o-mini',
                'name' => 'GPT-4o mini',
                'provider' => 'OpenAI',
            ],
            [
                'id' => 'google/gemini-3-flash-preview',
                'name' => 'Gemini 3 Flash Preview',
                'provider' => 'Google',
            ],
            [
                'id' => 'anthropic/claude-3.5-haiku',
                'name' => 'Claude 3.5 Haiku',
                'provider' => 'Anthropic',
            ],
        ];

        return Inertia::render('Ask', [
            'models' => $models,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|min:5|max:2000',
            'model' => 'required|string',
        ]);

        try {
            $systemPrompt = null;
            $customInstruction = auth()->user()->customInstruction;
            if ($customInstruction && $customInstruction->enabled) {
                $systemPrompt = $customInstruction->instructions;
            }

            $response = $this->chatService->ask(
                $request->input('model'),
                $request->input('question'),
                $systemPrompt
            );

            return response()->json([
                'success' => true,
                'response' => $response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
