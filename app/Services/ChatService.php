<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ChatService
{
    private const API_URL = 'https://openrouter.ai/api/v1/chat/completions';

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = env('OPENROUTER_API_KEY');

        if (!$this->apiKey) {
            throw new \Exception('OPENROUTER_API_KEY n\'est pas configurée');
        }
    }

    public function ask(string $model, string $question): string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])->timeout(30)->withoutVerifying()->post(self::API_URL, [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $question,
                    ],
                ],
                'temperature' => 0.7,
                'max_tokens' => 2000,
            ]);

            if ($response->failed()) {
                $errorBody = $response->json();
                $errorMsg = $errorBody['error']['message'] ?? $response->body();
                throw new \Exception("API Error ({$response->status()}): {$errorMsg}");
            }

            $data = $response->json();

            if (!isset($data['choices'][0]['message']['content'])) {
                throw new \Exception('Structure de réponse invalide');
            }

            return $data['choices'][0]['message']['content'];
        } catch (\Exception $e) {
            \Log::error('ChatService Error: ' . $e->getMessage());
            throw new \Exception('Erreur lors de l\'appel à l\'API: ' . $e->getMessage());
        }
    }
}
