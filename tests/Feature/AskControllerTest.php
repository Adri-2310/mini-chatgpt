<?php

namespace Tests\Feature;

use App\Models\LlmModel;
use App\Services\ChatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AskControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::forget('llm_models_enabled');
        LlmModel::truncate();

        LlmModel::create([
            'model_id' => 'gpt-4o-mini',
            'name' => 'GPT-4o mini',
            'provider' => 'OpenAI',
            'enabled' => true,
        ]);

        Cache::forget('llm_models_enabled');
    }

    public function test_store_requires_authentication()
    {
        $response = $this->postJson('/api/ask', [
            'question' => 'Test',
            'model' => 'gpt-4o-mini',
        ]);

        $response->assertStatus(401);
    }

    public function test_store_returns_ai_response()
    {
        $user = $this->createUser();

        $this->mock(ChatService::class, function ($mock) {
            $mock->shouldReceive('buildSystemPrompt')->andReturn('System prompt');
            $mock->shouldReceive('ask')
                ->once()
                ->andReturn([
                    'content' => 'Réponse test de l\'IA',
                    'tokens' => 42
                ]);
        });

        $response = $this->actingAs($user)->postJson('/api/ask', [
            'question' => 'Quelle est ta3 nom?',
            'model' => 'gpt-4o-mini',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('response', 'Réponse test de l\'IA');
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('tokens', 42);
    }

    public function test_store_validates_required_fields()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->postJson('/api/ask', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['question', 'model']);
    }

    public function test_store_validates_content_max_length()
    {
        $user = $this->createUser();

        $this->mock(ChatService::class, function ($mock) {
            $mock->shouldReceive('ask')->andReturn('Réponse');
        });

        $response = $this->actingAs($user)->postJson('/api/ask', [
            'question' => str_repeat('a', 2001),
            'model' => 'gpt-4o-mini',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['question']);
    }

    public function test_stream_requires_authentication()
    {
        $response = $this->postJson('/api/ask/stream', [
            'question' => 'Test',
            'model' => 'gpt-4o-mini',
        ]);

        $response->assertStatus(401);
    }

    public function test_stream_returns_event_stream()
    {
        $user = $this->createUser();

        $this->mock(ChatService::class, function ($mock) {
            $mock->shouldReceive('buildSystemPrompt')->andReturn('System prompt');
            $mock->shouldReceive('streamAsk')
                ->andReturn((function () {
                    yield 'chunk1';
                    yield 'chunk2';
                })());
            $mock->shouldReceive('getLastStreamTokens')->andReturn(100);
        });

        $response = $this->actingAs($user)->postJson('/api/ask/stream', [
            'question' => 'Testing stream response',
            'model' => 'gpt-4o-mini',
        ]);

        $response->assertStatus(200);
        $this->assertStringStartsWith('text/event-stream', $response->headers->get('Content-Type'));
    }

    private function createUser()
    {
        return \App\Models\User::factory()->create();
    }
}
