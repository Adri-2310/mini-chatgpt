<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\LlmModel;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
            'models' => LlmModel::where('enabled', true)
                ->select(['id', 'model_id', 'name', 'provider'])
                ->get(),
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

        $conversation->delete();

        return response()->json(['success' => true]);
    }

    public function stats(Conversation $conversation)
    {
        try {
            $this->authorize('view', $conversation);
        } catch (AuthorizationException) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $allMessages = $conversation->messages()->get();
        $assistantMessages = $conversation->messages()->where('role', 'assistant')->get();

        $totalTokens = $allMessages->sum('tokens_used') ?? 0;
        $totalCost = 0;

        // Calculer le coût pour chaque message assistant
        foreach ($assistantMessages as $msg) {
            if ($msg->tokens_used) {
                // Coût moyen par million de tokens
                $costPer1M = [
                    'openai/gpt-4o-mini' => 0.75,
                    'google/gemini-2.5-flash' => 0.20,
                    'anthropic/claude-3.5-haiku' => 2.40,
                    'anthropic/claude-3.5-sonnet' => 3.00,
                ];
                $modelCost = $costPer1M[$msg->model ?? $conversation->model_used] ?? 1.50;
                $messageCost = ($msg->tokens_used / 1_000_000) * $modelCost;
                $totalCost += $messageCost;
            }
        }

        return response()->json([
            'total_messages' => $allMessages->count(),
            'total_tokens' => $totalTokens,
            'total_cost_usd' => round($totalCost, 6),
        ]);
    }

    public function search(Conversation $conversation, \Illuminate\Http\Request $request)
    {
        try {
            $this->authorize('view', $conversation);
        } catch (AuthorizationException) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'results' => [],
                'count' => 0,
            ]);
        }

        $messages = $conversation->messages()
            ->where('content', 'like', '%' . $query . '%')
            ->orderBy('created_at')
            ->get(['id', 'role', 'content', 'created_at']);

        return response()->json([
            'results' => $messages,
            'count' => $messages->count(),
            'query' => $query,
        ]);
    }

    public function export(Conversation $conversation, \Illuminate\Http\Request $request)
    {
        try {
            $this->authorize('view', $conversation);
        } catch (AuthorizationException) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $format = $request->input('format', 'json');
        $messages = $conversation->messages()->orderBy('created_at')->get();

        if ($format === 'markdown') {
            $content = "# {$conversation->title}\n\n";
            $content .= "**Modèle:** {$conversation->model_used}\n";
            $content .= "**Date:** " . $conversation->created_at->format('d/m/Y H:i') . "\n\n";
            $content .= "---\n\n";

            foreach ($messages as $message) {
                $role = $message->role === 'user' ? '👤 Utilisateur' : '🤖 Assistant';
                $content .= "## $role\n\n";
                $content .= "{$message->content}\n\n";
                if ($message->tokens_used) {
                    $content .= "*Tokens: {$message->tokens_used}*\n\n";
                }
                $content .= "---\n\n";
            }

            return response($content)
                ->header('Content-Type', 'text/markdown')
                ->header('Content-Disposition', 'attachment; filename="' . Str::slug($conversation->title) . '.md"');
        } else {
            $data = [
                'title' => $conversation->title,
                'model' => $conversation->model_used,
                'created_at' => $conversation->created_at,
                'updated_at' => $conversation->updated_at,
                'messages' => $messages->map(fn($msg) => [
                    'role' => $msg->role,
                    'content' => $msg->content,
                    'tokens' => $msg->tokens_used,
                    'input_tokens' => $msg->input_tokens,
                    'output_tokens' => $msg->output_tokens,
                    'cost_usd' => $msg->cost_usd,
                    'created_at' => $msg->created_at,
                ]),
            ];

            return response()->json($data)
                ->header('Content-Disposition', 'attachment; filename="' . Str::slug($conversation->title) . '.json"');
        }
    }
}
