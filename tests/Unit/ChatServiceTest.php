<?php

namespace Tests\Unit;

use App\Services\ChatService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatServiceTest extends TestCase
{
    private ChatService $chatService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->chatService = app(ChatService::class);
    }

    public function test_ask_returns_content_from_api_response()
    {
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => 'Réponse test']]],
                'usage' => ['total_tokens' => 42]
            ], 200),
        ]);

        $result = $this->chatService->ask('gpt-4o-mini', 'Test question');

        $this->assertIsArray($result);
        $this->assertStringContainsString('Réponse test', $result['content']);
        $this->assertEquals(42, $result['tokens']);
        Http::assertSent(function ($request) {
            return $request->url() === 'https://openrouter.ai/api/v1/chat/completions';
        });
    }

    public function test_ask_throws_exception_on_non_200_status()
    {
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([], 500),
        ]);

        $this->expectException(\Exception::class);

        $this->chatService->ask('gpt-4o-mini', 'Test');
    }

    public function test_ask_with_history_sends_full_message_array()
    {
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => 'Réponse']]],
                'usage' => ['total_tokens' => 50]
            ], 200),
        ]);

        $history = [
            ['role' => 'user', 'content' => 'Message 1'],
            ['role' => 'assistant', 'content' => 'Réponse 1'],
        ];

        $result = $this->chatService->askWithHistory('gpt-4o-mini', $history);

        $this->assertIsArray($result);
        $this->assertEquals('Réponse', $result['content']);
        $this->assertEquals(50, $result['tokens']);

        Http::assertSent(function ($request) use ($history) {
            $body = json_decode($request->body(), true);
            return count($body['messages']) === 2;
        });
    }

    public function test_ask_throws_exception_when_api_returns_error_in_json()
    {
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'error' => ['message' => 'Invalid request']
            ], 200),
        ]);

        $this->expectException(\Exception::class);

        $this->chatService->ask('gpt-4o-mini', 'Test');
    }

    public function test_ask_with_system_prompt_includes_it()
    {
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => 'Réponse']]]
            ], 200),
        ]);

        $this->chatService->ask('gpt-4o-mini', 'Question', 'Système prompt');

        Http::assertSent(function ($request) {
            $body = json_decode($request->body(), true);
            $messages = $body['messages'];
            return $messages[0]['role'] === 'system' && $messages[0]['content'] === 'Système prompt';
        });
    }
}
