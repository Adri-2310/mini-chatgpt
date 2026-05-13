<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_only_authenticated_user_conversations()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conv1 = Conversation::factory()->for($user1)->create(['title' => 'Conv User 1']);
        $conv2 = Conversation::factory()->for($user2)->create(['title' => 'Conv User 2']);

        $response = $this->actingAs($user1)->getJson('/api/conversations');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['id' => $conv1->id]);
        $response->assertJsonMissing(['id' => $conv2->id]);
    }

    public function test_store_creates_conversation_for_authenticated_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/conversations', [
            'title' => 'Nouvelle conversation test',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('conversations', [
            'user_id' => $user->id,
            'title' => 'Nouvelle conversation test',
        ]);
    }

    public function test_store_requires_authentication()
    {
        $response = $this->postJson('/api/conversations', [
            'title' => 'Test',
        ]);

        $response->assertStatus(401);
    }

    public function test_update_renames_conversation()
    {
        $user = User::factory()->create();
        $conversation = Conversation::factory()->for($user)->create(['title' => 'Ancien titre']);

        $response = $this->actingAs($user)->putJson("/api/conversations/{$conversation->id}", [
            'title' => 'Nouveau titre',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('Nouveau titre', $conversation->fresh()->title);
    }

    public function test_update_is_forbidden_for_another_users_conversation()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conversation = Conversation::factory()->for($user2)->create();

        $response = $this->actingAs($user1)->putJson("/api/conversations/{$conversation->id}", [
            'title' => 'Nouveau titre',
        ]);

        $response->assertStatus(403);
    }

    public function test_destroy_deletes_conversation()
    {
        $user = User::factory()->create();
        $conversation = Conversation::factory()->for($user)->create();

        $response = $this->actingAs($user)->deleteJson("/api/conversations/{$conversation->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('conversations', ['id' => $conversation->id]);
    }

    public function test_destroy_is_forbidden_for_another_users_conversation()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conversation = Conversation::factory()->for($user2)->create();

        $response = $this->actingAs($user1)->deleteJson("/api/conversations/{$conversation->id}");

        $response->assertStatus(403);
    }

    public function test_show_returns_conversation_with_messages()
    {
        $user = User::factory()->create();
        $conversation = Conversation::factory()->for($user)->create();
        $conversation->messages()->createMany([
            ['role' => 'user', 'content' => 'Message 1', 'model' => 'gpt-4'],
            ['role' => 'assistant', 'content' => 'Réponse 1', 'model' => 'gpt-4'],
        ]);

        $response = $this->actingAs($user)->getJson("/api/conversations/{$conversation->id}");

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'messages');
    }
}
