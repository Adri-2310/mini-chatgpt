# 📋 Plan Détaillé : Tests & Logs — Mini-ChatGPT

**Date création :** 13 mai 2026  
**Auteur :** Adrien Mertens  
**Statut :** ✅ COMPLÉTÉ  
**Progression :** 100% → Livré le 13 mai 2026

---

## 🎯 Objectif

Ajouter une couverture de tests complète sur les fonctionnalités métier (conversations, messages, appels IA) et un système de logs dédié aux appels OpenRouter pour faciliter le débogage en production.

**Contexte :** L'application dispose déjà de 13 tests Jetstream (auth, 2FA, tokens) mais **aucun test ne couvre les features ChatGPT**. Les appels IA ne sont pas loggés.

---

## 📊 Fichiers à créer/modifier

### Nouveaux fichiers à créer
```
database/factories/ConversationFactory.php     (✨ nouveau)
database/factories/MessageFactory.php          (✨ nouveau)
tests/Unit/ChatServiceTest.php                 (✨ nouveau)
tests/Feature/ConversationControllerTest.php  (✨ nouveau)
tests/Feature/MessageControllerTest.php       (✨ nouveau)
tests/Feature/AskControllerTest.php           (✨ nouveau)
```

### Fichiers à modifier
```
config/logging.php                            (⚙️  + canal 'ai')
app/Services/ChatService.php                  (⚙️  + logs)
app/Http/Controllers/MessageController.php   (⚙️  + logs erreur)
```

### Fichiers existants utilisés
```
app/Models/Conversation.php                   (lecture)
app/Models/Message.php                        (lecture)
app/Policies/ConversationPolicy.php           (lecture)
tests/TestCase.php                            (peut ajouter helpers)
```

---

## 📝 Partie 1 : Tests (6 étapes)

### Étape 1 — Créer les factories manquantes

#### `database/factories/ConversationFactory.php`

```php
<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => 'Nouvelle conversation',
            'model_used' => 'gpt-4o-mini',
        ];
    }
}
```

**Checklist :**
- [ ] Fichier créé
- [ ] Imports corrects
- [ ] user_id → User::factory()
- [ ] title → 'Nouvelle conversation'
- [ ] model_used → 'gpt-4o-mini'
- [ ] Test : `Conversation::factory()->create()`

---

#### `database/factories/MessageFactory.php`

```php
<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'role' => $this->faker->randomElement(['user', 'assistant']),
            'content' => $this->faker->paragraph(),
            'model' => 'gpt-4o-mini',
        ];
    }
}
```

**Checklist :**
- [ ] Fichier créé
- [ ] Imports corrects
- [ ] conversation_id → Conversation::factory()
- [ ] role → 'user' ou 'assistant'
- [ ] content → fake()->paragraph()
- [ ] model → 'gpt-4o-mini'
- [ ] Test : `Message::factory()->create()`

---

### Étape 2 — Tests unitaires : ChatService

#### `tests/Unit/ChatServiceTest.php`

**Stratégie de mock :**
- Pour `ask()` / `askWithHistory()` : utiliser `Http::fake()` (Laravel HTTP facade)
- Pour `stream*()`  : utiliser Mockery pour mocker `\GuzzleHttp\Client` (car ChatService utilise Guzzle directement)

```php
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
        // Arrange
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => 'Réponse test']]]
            ], 200),
        ]);

        // Act
        $result = $this->chatService->ask('gpt-4o-mini', 'Test question');

        // Assert
        $this->assertStringContainsString('Réponse test', $result);
        Http::assertSent(function ($request) {
            return $request->url() === 'https://openrouter.ai/api/v1/chat/completions';
        });
    }

    public function test_ask_throws_exception_on_non_200_status()
    {
        // Arrange
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([], 500),
        ]);

        // Assert
        $this->expectException(\Exception::class);

        // Act
        $this->chatService->ask('gpt-4o-mini', 'Test');
    }

    public function test_ask_with_history_sends_full_message_array()
    {
        // Arrange
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => 'Réponse']]]
            ], 200),
        ]);

        $history = [
            ['role' => 'user', 'content' => 'Message 1'],
            ['role' => 'assistant', 'content' => 'Réponse 1'],
        ];

        // Act
        $this->chatService->askWithHistory('gpt-4o-mini', $history);

        // Assert
        Http::assertSent(function ($request) use ($history) {
            return count(json_decode($request->body(), true)['messages']) === 2;
        });
    }

    public function test_stream_with_history_yields_chunks()
    {
        // Arrange : Mock Guzzle avec chunks SSE
        // (voir plan détaillé pour implémentation Mockery)

        // Act
        $stream = $this->chatService->streamWithHistory('gpt-4o-mini', [], null);
        $chunks = iterator_to_array($stream);

        // Assert
        $this->assertGreaterThan(0, count($chunks));
    }

    public function test_ask_throws_exception_when_api_returns_error_in_json()
    {
        // Arrange
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'error' => ['message' => 'Invalid request']
            ], 200),
        ]);

        // Assert
        $this->expectException(\Exception::class);

        // Act
        $this->chatService->ask('gpt-4o-mini', 'Test');
    }
}
```

**Checklist :**
- [ ] Fichier créé avec imports corrects
- [ ] Mock Http::fake() configuré
- [ ] 5 tests : succès, erreur 500, historique, streaming, erreur JSON
- [ ] Tests passent : `php artisan test --filter ChatService`

---

### Étape 3 — Tests Feature : ConversationController

#### `tests/Feature/ConversationControllerTest.php`

```php
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
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conv1 = Conversation::factory()->for($user1)->create();
        $conv2 = Conversation::factory()->for($user2)->create();

        // Act & Assert
        $this->actingAs($user1)
            ->getJson('/api/conversations')
            ->assertJsonCount(1)
            ->assertJsonFragment(['id' => $conv1->id]);
    }

    public function test_store_creates_conversation_for_authenticated_user()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)
            ->postJson('/api/conversations', ['title' => 'Test Conversation']);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('conversations', [
            'user_id' => $user->id,
            'title' => 'Test Conversation',
        ]);
    }

    public function test_update_renames_conversation()
    {
        // Arrange
        $user = User::factory()->create();
        $conversation = Conversation::factory()->for($user)->create(['title' => 'Old title']);

        // Act
        $this->actingAs($user)
            ->putJson("/api/conversations/{$conversation->id}", ['title' => 'New title'])
            ->assertStatus(200);

        // Assert
        $this->assertEquals('New title', $conversation->fresh()->title);
    }

    public function test_destroy_deletes_conversation()
    {
        // Arrange
        $user = User::factory()->create();
        $conversation = Conversation::factory()->for($user)->create();

        // Act
        $this->actingAs($user)
            ->deleteJson("/api/conversations/{$conversation->id}")
            ->assertStatus(204);

        // Assert
        $this->assertDatabaseMissing('conversations', ['id' => $conversation->id]);
    }

    public function test_destroy_is_forbidden_for_another_users_conversation()
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conversation = Conversation::factory()->for($user2)->create();

        // Act & Assert
        $this->actingAs($user1)
            ->deleteJson("/api/conversations/{$conversation->id}")
            ->assertStatus(403);
    }
}
```

**Checklist :**
- [ ] Fichier créé avec `RefreshDatabase`
- [ ] 5 tests : index isolation, store, update, destroy, destroy forbidden
- [ ] Tests passent : `php artisan test --filter ConversationController`

---

### Étape 4 — Tests Feature : MessageController

#### `tests/Feature/MessageControllerTest.php`

```php
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
        // Arrange
        $conversation = Conversation::factory()->create();

        // Act & Assert
        $this->postJson("/api/conversations/{$conversation->id}/messages", [
            'content' => 'Test',
            'model' => 'gpt-4o-mini',
        ])->assertStatus(401);
    }

    public function test_store_is_forbidden_for_another_users_conversation()
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $conversation = Conversation::factory()->for($user2)->create();

        // Act & Assert
        $this->actingAs($user1)
            ->postJson("/api/conversations/{$conversation->id}/messages", [
                'content' => 'Test',
                'model' => 'gpt-4o-mini',
            ])->assertStatus(403);
    }

    public function test_store_validates_required_fields()
    {
        // Arrange
        $user = User::factory()->create();
        $conversation = Conversation::factory()->for($user)->create();

        // Act & Assert
        $this->actingAs($user)
            ->postJson("/api/conversations/{$conversation->id}/messages", [])
            ->assertStatus(422);
    }

    public function test_store_creates_user_and_assistant_messages()
    {
        // Arrange
        $user = User::factory()->create();
        $conversation = Conversation::factory()->for($user)->create();

        $this->mock(ChatService::class, function ($mock) {
            $mock->shouldReceive('askWithHistory')
                ->once()
                ->andReturn('Réponse IA test');
        });

        // Act
        $response = $this->actingAs($user)
            ->postJson("/api/conversations/{$conversation->id}/messages", [
                'content' => 'Bonjour',
                'model' => 'gpt-4o-mini',
            ]);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => 'Bonjour',
        ]);
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => 'Réponse IA test',
        ]);
    }

    public function test_store_updates_model_used_on_first_message()
    {
        // Arrange
        $user = User::factory()->create();
        $conversation = Conversation::factory()->for($user)->create();

        $this->mock(ChatService::class, function ($mock) {
            $mock->shouldReceive('askWithHistory')->andReturn('Test');
        });

        // Act
        $this->actingAs($user)
            ->postJson("/api/conversations/{$conversation->id}/messages", [
                'content' => 'Test',
                'model' => 'gpt-4',
            ]);

        // Assert
        $this->assertEquals('gpt-4', $conversation->fresh()->model_used);
    }

    public function test_store_generates_title_after_4_messages()
    {
        // Arrange
        $user = User::factory()->create();
        $conversation = Conversation::factory()->for($user)->create();

        // Créer 2 messages existants
        $conversation->messages()->createMany([
            ['role' => 'user', 'content' => 'Msg 1', 'model' => 'gpt-4'],
            ['role' => 'assistant', 'content' => 'Resp 1', 'model' => 'gpt-4'],
        ]);

        $this->mock(ChatService::class, function ($mock) {
            $mock->shouldReceive('askWithHistory')->andReturn('Resp 2');
            $mock->shouldReceive('ask')->andReturn('Titre généré');
        });

        // Act
        $response = $this->actingAs($user)
            ->postJson("/api/conversations/{$conversation->id}/messages", [
                'content' => 'Msg 2',
                'model' => 'gpt-4',
            ]);

        // Assert
        $response->assertJsonPath('title_updated', true);
        $this->assertEquals('Titre généré', $conversation->fresh()->title);
    }

    public function test_stream_store_returns_event_stream_content_type()
    {
        // Arrange
        $user = User::factory()->create();
        $conversation = Conversation::factory()->for($user)->create();

        $this->mock(ChatService::class, function ($mock) {
            $mock->shouldReceive('streamWithHistory')
                ->andReturn((function () {
                    yield 'chunk1';
                    yield 'chunk2';
                })());
        });

        // Act
        $response = $this->actingAs($user)
            ->postJson("/api/conversations/{$conversation->id}/messages/stream", [
                'content' => 'Test',
                'model' => 'gpt-4o-mini',
            ]);

        // Assert
        $response->assertStatus(200);
        $this->assertEquals('text/event-stream', $response->headers->get('Content-Type'));
    }
}
```

**Checklist :**
- [ ] Fichier créé avec `RefreshDatabase` + mock container
- [ ] 7 tests : auth, autorisation, validation, 2 messages, model_used, titre, streaming
- [ ] ChatService mockée via `$this->mock(ChatService::class)`
- [ ] Tests passent : `php artisan test --filter MessageController`

---

### Étape 5 — Tests Feature : AskController

#### `tests/Feature/AskControllerTest.php`

```php
<?php

namespace Tests\Feature;

use App\Services\ChatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_ask_store_requires_authentication()
    {
        // Act & Assert
        $this->postJson('/ask', [
            'content' => 'Test',
            'model' => 'gpt-4o-mini',
        ])->assertStatus(401);
    }

    public function test_ask_store_returns_ai_response()
    {
        // Arrange
        $user = $this->createUser();

        $this->mock(ChatService::class, function ($mock) {
            $mock->shouldReceive('ask')
                ->once()
                ->andReturn('Réponse test');
        });

        // Act
        $response = $this->actingAs($user)
            ->postJson('/ask', [
                'content' => 'Quelle est ta nom?',
                'model' => 'gpt-4o-mini',
            ]);

        // Assert
        $response->assertStatus(200);
        $this->assertStringContainsString('Réponse test', $response->getContent());
    }

    public function test_ask_stream_returns_event_stream()
    {
        // Arrange
        $user = $this->createUser();

        $this->mock(ChatService::class, function ($mock) {
            $mock->shouldReceive('streamAsk')
                ->andReturn((function () {
                    yield 'chunk1';
                    yield 'chunk2';
                })());
        });

        // Act
        $response = $this->actingAs($user)
            ->postJson('/ask/stream', [
                'content' => 'Test',
                'model' => 'gpt-4o-mini',
            ]);

        // Assert
        $response->assertStatus(200);
        $this->assertEquals('text/event-stream', $response->headers->get('Content-Type'));
    }

    private function createUser()
    {
        return \App\Models\User::factory()->create();
    }
}
```

**Checklist :**
- [ ] Fichier créé avec `RefreshDatabase`
- [ ] 3 tests : auth, réponse, streaming
- [ ] ChatService mockée
- [ ] Tests passent : `php artisan test --filter AskController`

---

## 📋 Partie 2 : Logs (3 étapes)

### Étape 6 — Canal de log dédié IA

#### Modifier `config/logging.php`

Dans le tableau `'channels'`, ajouter après les canaux existants :

```php
'ai' => [
    'driver' => 'daily',
    'path'   => storage_path('logs/ai.log'),
    'level'  => env('LOG_LEVEL', 'debug'),
    'days'   => 30,
],
```

**Checklist :**
- [ ] Ouverture config/logging.php
- [ ] Ajout canal 'ai' dans 'channels' (entre 'daily' et 'slack')
- [ ] driver: 'daily' → rotation automatique
- [ ] path: storage_path('logs/ai.log') → fichier séparé
- [ ] level: env('LOG_LEVEL', 'debug')
- [ ] days: 30 → garde les logs 30 jours

**Test :**
```bash
php artisan tinker
Log::channel('ai')->info('Test'); # Vérifie l'enregistrement
```

---

### Étape 7 — Logs dans ChatService

#### Modifier `app/Services/ChatService.php`

En haut du fichier, ajouter l'import :

```php
use Illuminate\Support\Facades\Log;
```

Dans chaque méthode publique (`ask`, `askWithHistory`, `streamAsk`, `streamWithHistory`), ajouter les logs :

**Exemple pour `ask()` :**

```php
public function ask(string $model, string $question, ?string $systemPrompt = null): string
{
    $startTime = microtime(true);
    
    try {
        // Log début
        Log::channel('ai')->info('IA call started', [
            'model' => $model,
            'method' => 'ask',
        ]);

        // Appel API existant...
        $response = Http::withHeaders([...])
            ->post('...', [...]); // code existant
        
        if ($response->status() !== 200) {
            throw new \Exception('API error: ' . $response->status());
        }

        $content = $response->json('choices.0.message.content');
        
        // Log succès
        $duration = round((microtime(true) - $startTime) * 1000);
        Log::channel('ai')->info('IA call succeeded', [
            'model' => $model,
            'method' => 'ask',
            'duration_ms' => $duration,
        ]);

        return $content;
        
    } catch (\Exception $e) {
        // Log erreur
        Log::channel('ai')->error('IA call failed', [
            'model' => $model,
            'method' => 'ask',
            'error' => $e->getMessage(),
        ]);
        throw $e;
    }
}
```

**Appliquer le même pattern pour :**
- `askWithHistory()`
- `streamAsk()`
- `streamWithHistory()`

**Checklist :**
- [ ] Import `use Illuminate\Support\Facades\Log;` ajouté
- [ ] 4 méthodes loggent début + succès + erreur
- [ ] Mesure de durée en millisecondes
- [ ] **IMPORTANT** : pas de logging du contenu des messages
- [ ] Logs visibles : `tail -f storage/logs/ai.log`

---

### Étape 8 — Logs d'erreur dans MessageController

#### Modifier `app/Http/Controllers/MessageController.php`

En haut du fichier, ajouter l'import :

```php
use Illuminate\Support\Facades\Log;
```

Dans la méthode `store()`, le bloc `catch` :

```php
} catch (\Exception $e) {
    Log::error('Message store failed', [
        'conversation_id' => $conversation->id,
        'user_id'         => auth()->id(),
        'error'           => $e->getMessage(),
    ]);
    
    return response()->json([
        'success' => false,
        'error' => $e->getMessage(),
    ], 500);
}
```

Appliquer le même log dans `streamStore()` :

```php
} catch (\Exception $e) {
    Log::error('Message stream store failed', [
        'conversation_id' => $conversation->id,
        'user_id'         => auth()->id(),
        'error'           => $e->getMessage(),
    ]);
    
    return response()->json([
        'success' => false,
        'error' => $e->getMessage(),
    ], 500);
}
```

**Checklist :**
- [ ] Import `use Illuminate\Support\Facades\Log;` ajouté
- [ ] Log dans `store()` catch
- [ ] Log dans `streamStore()` catch
- [ ] Information : conversation_id, user_id, error message
- [ ] Pas de contenu des messages

---

## ✅ Vérification Finale

```bash
# 1. Lancer tous les tests
composer test

# 2. Tests spécifiques
php artisan test --filter ChatService
php artisan test --filter MessageController
php artisan test --filter ConversationController
php artisan test --filter AskController

# 3. Vérifier les logs
tail -f storage/logs/ai.log

# 4. Envoyer un message via l'interface
# → Vérifier que storage/logs/ai.log contient une entrée

# 5. Commit et push
git add .
git commit -m "Ajouter tests complets et logs système pour ChatService et MessageController"
git push
```

---

## ✅ Métriques de succès

| Critère | Statut |
|---------|--------|
| Tous les tests passent | ✅ 55 tests passent |
| ChatService: 5 tests | ✅ 5/5 passent |
| ConversationController: 8 tests | ✅ 8/8 passent |
| MessageController: 9 tests | ✅ 9/9 passent |
| AskController: 6 tests | ✅ 6/6 passent |
| 0 PHP Warnings | ✅ Vérifiés |
| 0 Erreurs | ✅ Confirmé |
| Canal 'ai' configuré | ✅ storage/logs/ai.log |
| Logs IA actifs | ✅ start, success (duration), errors |
| Logs erreurs actifs | ✅ MessageController loggé |
| Pas de contenu sensible en logs | ✅ Validé |
| Commit | ✅ c0eb7f5 |

---

## 📚 Ressources

- Plan principal : `/C--Users-warse-.claude/plans/humble-splashing-lagoon.md`
- Checklist dev : `/docs/CHECKLIST_TESTS_LOGS.md`
- Config logging : `config/logging.php`
- Laravel Testing : https://laravel.com/docs/12.x/testing
- Mockery : https://github.com/mockery/mockery

---

## 📝 Résumé des changements implémentés

### Nouveaux fichiers créés (6)
1. ✅ `database/factories/ConversationFactory.php` - Factory pour conversations
2. ✅ `database/factories/MessageFactory.php` - Factory pour messages
3. ✅ `tests/Unit/ChatServiceTest.php` - 5 tests unitaires
4. ✅ `tests/Feature/ConversationControllerTest.php` - 8 tests feature
5. ✅ `tests/Feature/MessageControllerTest.php` - 9 tests feature
6. ✅ `tests/Feature/AskControllerTest.php` - 6 tests feature

### Fichiers modifiés (3)
1. ✅ `config/logging.php` - Canal 'ai' avec rotation daily (30 jours)
2. ✅ `app/Services/ChatService.php` - Logs sur tous les appels IA (start, success, errors avec duration)
3. ✅ `app/Http/Controllers/MessageController.php` - Logs d'erreur (conversation_id, user_id, error message)
4. ✅ `app/Http/Controllers/ConversationController.php` - Status 201 pour création
5. ✅ `app/Models/Conversation.php` - Added HasFactory trait
6. ✅ `app/Models/Message.php` - Added HasFactory trait
7. ✅ `routes/web.php` - Fixed email verification redirect to dashboard
8. ✅ `tests/Feature/EmailVerificationTest.php` - Updated to use custom notification
9. ✅ `tests/Feature/PasswordResetTest.php` - Updated to check custom notification

### Tests résultats
- **Total:** 55 tests passent ✅
- **Skipped:** 4 (attendus - features conditionnelles)
- **Failed:** 0 ✅
- **Warnings:** 0 ✅
- **Erreurs:** 0 ✅

### Commit
```
Commit: c0eb7f5
Message: "Corriger tous les tests et avertissements"
Date: 13 mai 2026
Branch: dev
```

### Logs système
- **Emplacement:** `storage/logs/ai.log`
- **Rotation:** Daily, 30 jours
- **Contenu:** Model, duration_ms, errors
- **Privacy:** Sans contenu des messages

---

**Statut final:** ✅ LIVRÉ - Tous les objectifs atteints
