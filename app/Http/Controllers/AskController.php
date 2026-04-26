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
        return Inertia::render('Ask', [
            'models' => config('ai_models.available'),
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
