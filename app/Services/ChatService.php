<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ChatService
{
    private const API_URL = 'https://openrouter.ai/api/v1/chat/completions';

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.key');

        if (!$this->apiKey) {
            throw new \Exception('OPENROUTER_API_KEY n\'est pas configurée');
        }
    }

    public function ask(string $model, string $question, ?string $systemPrompt = null): string
    {
        try {
            $messages = [];

            if ($systemPrompt) {
                $messages[] = [
                    'role' => 'system',
                    'content' => $systemPrompt,
                ];
            }

            $messages[] = [
                'role' => 'user',
                'content' => $question,
            ];

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])->timeout(30)->withoutVerifying()->post(self::API_URL, [
                'model' => $model,
                'messages' => $messages,
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

    public function askWithHistory(string $model, array $messages, ?string $systemPrompt = null): string
    {
        try {
            if ($systemPrompt) {
                array_unshift($messages, [
                    'role' => 'system',
                    'content' => $systemPrompt,
                ]);
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])->timeout(30)->withoutVerifying()->post(self::API_URL, [
                'model' => $model,
                'messages' => $messages,
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

    public function streamAsk(string $model, string $question, ?string $systemPrompt = null): \Generator
    {
        try {
            $messages = [];

            if ($systemPrompt) {
                $messages[] = [
                    'role' => 'system',
                    'content' => $systemPrompt,
                ];
            }

            $messages[] = [
                'role' => 'user',
                'content' => $question,
            ];

            yield from $this->streamMessages($model, $messages);
        } catch (\Exception $e) {
            \Log::error('ChatService Stream Error: ' . $e->getMessage());
            throw new \Exception('Erreur lors du streaming: ' . $e->getMessage());
        }
    }

    public function streamWithHistory(string $model, array $messages, ?string $systemPrompt = null): \Generator
    {
        try {
            if ($systemPrompt) {
                array_unshift($messages, [
                    'role' => 'system',
                    'content' => $systemPrompt,
                ]);
            }

            yield from $this->streamMessages($model, $messages);
        } catch (\Exception $e) {
            \Log::error('ChatService Stream Error: ' . $e->getMessage());
            throw new \Exception('Erreur lors du streaming: ' . $e->getMessage());
        }
    }

    private function streamMessages(string $model, array $messages): \Generator
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post(self::API_URL, [
                'headers' => [
                    'Authorization' => "Bearer {$this->apiKey}",
                    'HTTP-Referer' => config('app.url'),
                    'X-Title' => config('app.name'),
                ],
                'json' => [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                    'stream' => true,
                ],
                'stream' => true,
                'verify' => false,
                'timeout' => 30,
                'http_errors' => false, // <-- TRÈS IMPORTANT : Empêche Guzzle de crasher sur une erreur 4xx/5xx
            ]);

            // VÉRIFICATION DE L'ERREUR HTTP ICI
            if ($response->getStatusCode() !== 200) {
                $errorBody = (string) $response->getBody();
                \Log::error("Erreur OpenRouter : " . $errorBody);
                
                // On renvoie l'erreur sous forme de texte pour la voir à l'écran
                yield "Erreur de l'API : " . $errorBody;
                return;
            }

            // LECTURE CORRECTE DU FLUX
            $body = $response->getBody();
            $buffer = '';

            // On utilise eof() et read() car Guzzle retourne un Stream PSR-7 (non itérable directement avec foreach)
            while (!$body->eof()) {
                $chunk = $body->read(1024);
                
                if ($chunk === '') {
                    break;
                }

                $buffer .= $chunk;
                $lines = explode("\n", $buffer);
                $buffer = end($lines);

                foreach (array_slice($lines, 0, -1) as $line) {
                    $line = trim($line);

                    if (empty($line) || !str_starts_with($line, 'data: ')) {
                        continue;
                    }

                    $jsonStr = substr($line, 6);
                    
                    if ($jsonStr === '[DONE]') {
                        return;
                    }

                    $data = json_decode($jsonStr, true);

                    // Vérification d'une erreur API à l'intérieur du flux
                    if ($data && isset($data['error'])) {
                        \Log::error('Erreur OpenRouter dans le flux : ' . print_r($data['error'], true));
                        yield "Erreur de l'IA : " . ($data['error']['message'] ?? 'Erreur inconnue');
                        return;
                    }

                    // On envoie le texte au fur et à mesure
                    if ($data && isset($data['choices'][0]['delta']['content'])) {
                        yield $data['choices'][0]['delta']['content'];
                    }
                }
            }

            // Traitement du reste du buffer à la fin
            if (!empty($buffer)) {
                $line = trim($buffer);
                if (!empty($line) && str_starts_with($line, 'data: ')) {
                    $jsonStr = substr($line, 6);
                    if ($jsonStr !== '[DONE]') {
                        $data = json_decode($jsonStr, true);
                        if ($data && isset($data['choices'][0]['delta']['content'])) {
                            yield $data['choices'][0]['delta']['content'];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('ChatService Stream Error: ' . $e->getMessage());
            throw new \Exception('Erreur lors du streaming: ' . $e->getMessage());
        }
    }
}
