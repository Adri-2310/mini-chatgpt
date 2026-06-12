<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatService
{
    private const API_URL = 'https://openrouter.ai/api/v1/chat/completions';

    private string $apiKey;
    private ?int $lastStreamTokens = null;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.key');

        if (!$this->apiKey) {
            throw new \Exception('OPENROUTER_API_KEY n\'est pas configurée');
        }
    }

    public function getLastStreamTokens(): ?int
    {
        return $this->lastStreamTokens;
    }

    public function ask(string $model, string $question, ?string $systemPrompt = null): array
    {
        $startTime = microtime(true);

        try {
            Log::channel('ai')->info('IA call started', [
                'model' => $model,
                'method' => 'ask',
            ]);

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

            $duration = round((microtime(true) - $startTime) * 1000);
            Log::channel('ai')->info('IA call succeeded', [
                'model' => $model,
                'method' => 'ask',
                'duration_ms' => $duration,
            ]);

            return [
                'content' => $data['choices'][0]['message']['content'],
                'tokens'  => $data['usage']['total_tokens'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::channel('ai')->error('IA call failed', [
                'model' => $model,
                'method' => 'ask',
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Erreur lors de l\'appel à l\'API: ' . $e->getMessage());
        }
    }

    public function askWithHistory(string $model, array $messages, ?string $systemPrompt = null): array
    {
        $startTime = microtime(true);

        try {
            Log::channel('ai')->info('IA call started', [
                'model' => $model,
                'method' => 'askWithHistory',
                'messages_count' => count($messages),
            ]);

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

            $duration = round((microtime(true) - $startTime) * 1000);
            Log::channel('ai')->info('IA call succeeded', [
                'model' => $model,
                'method' => 'askWithHistory',
                'duration_ms' => $duration,
            ]);

            return [
                'content' => $data['choices'][0]['message']['content'],
                'tokens'  => $data['usage']['total_tokens'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::channel('ai')->error('IA call failed', [
                'model' => $model,
                'method' => 'askWithHistory',
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Erreur lors de l\'appel à l\'API: ' . $e->getMessage());
        }
    }

    public function streamAsk(string $model, string $question, ?string $systemPrompt = null): \Generator
    {
        try {
            Log::channel('ai')->info('IA streaming started', [
                'model' => $model,
                'method' => 'streamAsk',
            ]);

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

            yield from $this->streamMessages($model, $messages, 'streamAsk');
        } catch (\Exception $e) {
            Log::channel('ai')->error('IA streaming failed', [
                'model' => $model,
                'method' => 'streamAsk',
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Erreur lors du streaming: ' . $e->getMessage());
        }
    }

    public function streamWithHistory(string $model, array $messages, ?string $systemPrompt = null): \Generator
    {
        try {
            Log::channel('ai')->info('IA streaming started', [
                'model' => $model,
                'method' => 'streamWithHistory',
                'messages_count' => count($messages),
            ]);

            if ($systemPrompt) {
                array_unshift($messages, [
                    'role' => 'system',
                    'content' => $systemPrompt,
                ]);
            }

            yield from $this->streamMessages($model, $messages, 'streamWithHistory');
        } catch (\Exception $e) {
            Log::channel('ai')->error('IA streaming failed', [
                'model' => $model,
                'method' => 'streamWithHistory',
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Erreur lors du streaming: ' . $e->getMessage());
        }
    }

    private function streamMessages(string $model, array $messages, string $method = 'unknown'): \Generator
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
                'http_errors' => false,
            ]);

            if ($response->getStatusCode() !== 200) {
                $errorBody = (string) $response->getBody();
                Log::channel('ai')->error('IA streaming HTTP error', [
                    'model' => $model,
                    'method' => $method,
                    'status' => $response->getStatusCode(),
                    'error' => $errorBody,
                ]);

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

                    if ($data && isset($data['error'])) {
                        Log::channel('ai')->error('IA streaming error in response', [
                            'model' => $model,
                            'method' => $method,
                            'error' => $data['error']['message'] ?? 'Erreur inconnue',
                        ]);
                        yield "Erreur de l'IA : " . ($data['error']['message'] ?? 'Erreur inconnue');
                        return;
                    }

                    // Capture tokens from usage if present
                    if ($data && isset($data['usage']['total_tokens'])) {
                        $this->lastStreamTokens = $data['usage']['total_tokens'];
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

                        // Capture tokens from usage if present
                        if ($data && isset($data['usage']['total_tokens'])) {
                            $this->lastStreamTokens = $data['usage']['total_tokens'];
                            $this->lastStreamInputTokens = $data['usage']['prompt_tokens'] ?? null;
                            $this->lastStreamOutputTokens = $data['usage']['completion_tokens'] ?? null;
                        }

                        if ($data && isset($data['choices'][0]['delta']['content'])) {
                            yield $data['choices'][0]['delta']['content'];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::channel('ai')->error('IA streaming exception', [
                'model' => $model,
                'method' => $method,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Erreur lors du streaming: ' . $e->getMessage());
        }
    }
}
