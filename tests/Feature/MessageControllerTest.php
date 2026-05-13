<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_requires_authentication()
    {
        $conversation = Conversation::factory()->create();

        $response = $this->postJson("/conversations/{$conversation->id}/messages", [
            'content' => 'Test',
            'model' => 'gpt-4o-mini',
        ]);

        $response->assertStatus(401);
    }

    public function test_store_is_forbidden_for_another_users_conversation()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conversation = Conversation::factory()->for($user2)->create();

        $response = $this->actingAs($user1)->postJson(
            "/conversations/{$conversation->id}/messages",
            [
                'content' => 'Test',
                'model' => 'gpt-4o-mini',
            ]
        );

        $response->assertStatus(403);
    }

    public function test_store_validates_required_fields()
    {
        $user = User::factory()->create();
        $conversation = Conversation::factory()->for($user)->create();

        $response = $this->actingAs($user)->postJson(
            "/conversations/{$conversation->id}/messages",
            []
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['content', 'model']);
    }

    public function test_store_validates_content_max_length()
    {
        $user = User::factory()->create();
        $conversation = Conversation::factory()->for($user)->create();

        $this->mock(ChatService::class, function ($mock) {
            $mock->shouldReceive('askWithHistory')->andReturn('Réponse');
        });

        $response = $this->actingAs($user)->postJson(
            "/conversations/{$conversation->id}/messages",
            [
                'content' => str_repeat('a', 5001),
                'model' => 'gpt-4o-mini',
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['content']);
    }

    public function test_store_creates_user_and_assistant_messages()
    {
        $user = User::factory()->create();
        $conversation = Conversation::factory()->for($user)->create();

        $this->mock(ChatService::class, function ($mock) {
            $mock->shouldReceive('askWithHistory')
                ->once()
                ->andReturn([
                    'content' => 'Réponse IA test',
                    'tokens' => 35
                ]);
        });

        $response = $this->actingAs($user)->postJson(
            "/conversations/{$conversation->id}/messages",
            [
                'content' => 'Bonjour',
                'model' => 'gpt-4o-mini',
            ]
        );

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => 'Bonjour',
        ]);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => 'Réponse IA test',
            'tokens_used' => 35,
        ]);
    }

    public function test_store_updates_model_used_on_first_message()
    {
        $user = User::factory()->create();
        $conversation = Conversation::factory()->for($user)->create();

        $this->mock(ChatService::class, function ($mock) {
            $mock->shouldReceive('askWithHistory')->andReturn([
                'content' => 'Réponse',
                'tokens' => 20
            ]);
        });

        $this->actingAs($user)->postJson(
            "/conversations/{$conversation->id}/messages",
            [
                'content' => 'Test',
                'model' => 'gpt-4',
            ]
        );

        $this->assertEquals('gpt-4', $conversation->fresh()->model_used);
    }

    public function test_store_generates_title_after_4_messages()
    {
        $user = User::factory()->create();
        $conversation = Conversation::factory()->for($user)->create();

        $conversation->messages()->createMany([
            ['role' => 'user', 'content' => 'Msg 1', 'model' => 'gpt-4'],
            ['role' => 'assistant', 'content' => 'Resp 1', 'model' => 'gpt-4'],
        ]);

        $this->mock(ChatService::class, function ($mock) {
            $mock->shouldReceive('askWithHistory')->andReturn([
                'content' => 'Resp 2',
                'tokens' => 28
            ]);
            $mock->shouldReceive('ask')->andReturn([
                'content' => 'Titre généré',
                'tokens' => 15
            ]);
        });

        $response = $this->actingAs($user)->postJson(
            "/conversations/{$conversation->id}/messages",
            [
                'content' => 'Msg 2',
                'model' => 'gpt-4',
            ]
        );

        $response->assertJsonPath('title_updated', true);
        $this->assertEquals('Titre généré', $conversation->fresh()->title);
    }

    public function test_stream_store_requires_authentication()
    {
        $conversation = Conversation::factory()->create();

        $response = $this->postJson(
            "/conversations/{$conversation->id}/messages/stream",
            [
                'content' => 'Test',
                'model' => 'gpt-4o-mini',
            ]
        );

        $response->assertStatus(401);
    }

    public function test_stream_store_returns_event_stream_content_type()
    {
        $user = User::factory()->create();
        $conversation = Conversation::factory()->for($user)->create();

        $this->mock(ChatService::class, function ($mock) {
            $mock->shouldReceive('streamWithHistory')
                ->andReturn((function () {
                    yield 'chunk1';
                    yield 'chunk2';
                })());
        });

        $response = $this->actingAs($user)->postJson(
            "/conversations/{$conversation->id}/messages/stream",
            [
                'content' => 'Test',
                'model' => 'gpt-4o-mini',
            ]
        );

        $response->assertStatus(200);
        $this->assertStringStartsWith('text/event-stream', $response->headers->get('Content-Type'));
    }
}
