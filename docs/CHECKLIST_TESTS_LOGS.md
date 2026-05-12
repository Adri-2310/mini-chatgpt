# Checklist Développement — Tests & Logs mini-chatgpt

## PARTIE 1 : Tests

### Étape 1 — Factories manquantes
- [ ] Créer `database/factories/ConversationFactory.php`
  - [ ] user_id → User::factory()
  - [ ] title → 'Nouvelle conversation'
  - [ ] model_used → 'gpt-4o-mini'
  
- [ ] Créer `database/factories/MessageFactory.php`
  - [ ] conversation_id → Conversation::factory()
  - [ ] role → 'user'
  - [ ] content → fake()->paragraph()
  - [ ] model → 'gpt-4o-mini'

### Étape 2 — Tests unitaires : ChatService
- [ ] Créer `tests/Unit/ChatServiceTest.php`
  - [ ] Importer Http::fake() et Mockery
  - [ ] Test : `test_ask_returns_content_from_api_response`
    - [ ] Mock réponse API valide
    - [ ] Vérifier contenu retourné
  - [ ] Test : `test_ask_throws_exception_on_non_200_status`
    - [ ] Mock statut HTTP 500
    - [ ] Vérifier exception levée
  - [ ] Test : `test_ask_with_history_sends_full_message_array`
    - [ ] Vérifier que le tableau messages est envoyé
  - [ ] Test : `test_stream_with_history_yields_chunks`
    - [ ] Mock Guzzle avec chunks
    - [ ] Tester que le Generator yield correctement
  - [ ] Test : `test_ask_throws_exception_when_api_returns_error_in_json`
    - [ ] Mock JSON avec clé 'error'
    - [ ] Vérifier exception levée

### Étape 3 — Tests Feature : ConversationController
- [ ] Créer `tests/Feature/ConversationControllerTest.php`
  - [ ] Utiliser `RefreshDatabase`
  - [ ] Test : `test_index_returns_only_authenticated_user_conversations`
    - [ ] Créer 2 users avec conversations
    - [ ] Vérifier que chaque user voit uniquement ses conversations
  - [ ] Test : `test_store_creates_conversation_for_authenticated_user`
    - [ ] POST /conversations
    - [ ] Vérifier création + user_id correct
  - [ ] Test : `test_update_renames_conversation`
    - [ ] PUT /conversations/{id}
    - [ ] Vérifier titre mis à jour
  - [ ] Test : `test_destroy_deletes_conversation`
    - [ ] DELETE /conversations/{id}
    - [ ] Vérifier suppression
  - [ ] Test : `test_destroy_is_forbidden_for_another_users_conversation`
    - [ ] User A essaie de supprimer conversation de User B
    - [ ] Vérifier status 403

### Étape 4 — Tests Feature : MessageController
- [ ] Créer `tests/Feature/MessageControllerTest.php`
  - [ ] Utiliser `RefreshDatabase`
  - [ ] Mock `ChatService` via le container
  - [ ] Test : `test_store_requires_authentication`
    - [ ] POST sans auth
    - [ ] Vérifier 401 ou redirect
  - [ ] Test : `test_store_is_forbidden_for_another_users_conversation`
    - [ ] User A essaie ajouter message à conversation User B
    - [ ] Vérifier 403
  - [ ] Test : `test_store_validates_required_fields`
    - [ ] POST sans 'content' ou sans 'model'
    - [ ] Vérifier erreurs validation
  - [ ] Test : `test_store_creates_user_and_assistant_messages`
    - [ ] Mock ChatService→askWithHistory() = 'Réponse test'
    - [ ] POST /conversations/{id}/messages
    - [ ] Vérifier 2 messages créés (user + assistant)
    - [ ] Vérifier roles corrects
  - [ ] Test : `test_store_updates_model_used_on_first_message`
    - [ ] Conversation sans messages
    - [ ] Envoyer message avec model='gpt-4'
    - [ ] Vérifier conversation.model_used = 'gpt-4'
  - [ ] Test : `test_store_generates_title_after_4_messages`
    - [ ] Créer conversation avec 2 messages existants
    - [ ] Mock ChatService→ask() = 'Titre test'
    - [ ] POST 3e message
    - [ ] Vérifier title_updated=true dans réponse
    - [ ] POST 4e message
    - [ ] Vérifier titre généré
  - [ ] Test : `test_stream_store_returns_event_stream_content_type`
    - [ ] Mock ChatService→streamWithHistory() = Generator['chunk1', 'chunk2']
    - [ ] POST /conversations/{id}/messages/stream
    - [ ] Vérifier Content-Type='text/event-stream'
    - [ ] Vérifier chunk1 + chunk2 dans réponse

### Étape 5 — Tests Feature : AskController
- [ ] Créer `tests/Feature/AskControllerTest.php`
  - [ ] Utiliser `RefreshDatabase`
  - [ ] Mock `ChatService`
  - [ ] Test : `test_ask_store_requires_authentication`
    - [ ] POST /ask sans auth
    - [ ] Vérifier 401
  - [ ] Test : `test_ask_store_returns_ai_response`
    - [ ] Mock ChatService→ask() = 'Réponse test'
    - [ ] POST /ask { content: '...', model: 'gpt-4' }
    - [ ] Vérifier réponse contient 'Réponse test'
  - [ ] Test : `test_ask_stream_returns_event_stream`
    - [ ] Mock ChatService→streamAsk() = Generator
    - [ ] POST /ask/stream
    - [ ] Vérifier Content-Type='text/event-stream'

---

## PARTIE 2 : Logs

### Étape 6 — Canal de log dédié IA
- [ ] Modifier `config/logging.php`
  - [ ] Ajouter canal 'ai' dans le tableau 'channels'
  - [ ] driver: 'daily'
  - [ ] path: storage_path('logs/ai.log')
  - [ ] level: env('LOG_LEVEL', 'debug')
  - [ ] days: 30

### Étape 7 — Logs dans ChatService
- [ ] Modifier `app/Services/ChatService.php`
  - [ ] Importer `use Illuminate\Support\Facades\Log;`
  - [ ] Méthode `ask()` :
    - [ ] Avant l'appel : `Log::channel('ai')->info('IA call started', ['model' => $model])`
    - [ ] Après succès : `Log::channel('ai')->info('IA call succeeded', ['model' => $model, 'duration_ms' => ...])`
    - [ ] En erreur HTTP : `Log::channel('ai')->error('IA call failed', ['model' => $model, 'error' => $e->getMessage()])`
  - [ ] Méthode `askWithHistory()` : même pattern
  - [ ] Méthode `streamAsk()` : même pattern
  - [ ] Méthode `streamWithHistory()` : même pattern
  - [ ] Mesure de durée : `$start = microtime(true)` avant, `round((microtime(true) - $start) * 1000)` après
  - [ ] **IMPORTANT** : ne pas logger le contenu des messages (privacy)

### Étape 8 — Logs d'erreur dans MessageController
- [ ] Modifier `app/Http/Controllers/MessageController.php`
  - [ ] Importer `use Illuminate\Support\Facades\Log;`
  - [ ] Dans `store()` bloc `catch` :
    - [ ] `Log::error('Message store failed', ['conversation_id' => $conversation->id, 'user_id' => auth()->id(), 'error' => $e->getMessage()])`
  - [ ] Dans `streamStore()` bloc `catch` :
    - [ ] Même log

---

## Vérification finale

- [ ] Lancer les tests : `composer test`
  - [ ] Tous les tests passent ✓
  - [ ] Couverture ≥ 80% pour les fichiers modifiés

- [ ] Tests feature : `php artisan test --filter MessageController`
  - [ ] 7 tests passent ✓

- [ ] Tests feature : `php artisan test --filter ConversationController`
  - [ ] 5 tests passent ✓

- [ ] Tests unitaires : `php artisan test --filter ChatService`
  - [ ] 5 tests passent ✓

- [ ] Vérifier logs IA :
  - [ ] Envoyer un message via `/chat` (API)
  - [ ] Vérifier que `storage/logs/ai.log` contient les entrées
  - [ ] `tail -f storage/logs/ai.log` montre les appels IA en temps réel

- [ ] Commit & Push :
  - [ ] Tous les fichiers ajoutés/modifiés
  - [ ] Message commit : "Ajouter tests complets et logs système pour ChatService et MessageController"

---

## Notes

- **Tests Strategy** : Utiliser `RefreshDatabase` pour les Feature tests (DB en mémoire via phpunit.xml)
- **Mocking** : Préférer les mocks de container Laravel (`$this->mock()`) aux mocks statiques pour les tests Feature
- **Factories** : Utiliser `WithoutModelEvents` si besoin de désactiver les événements (ex: création de conversations)
- **Logs** : Ne jamais logger le contenu des messages utilisateurs ou IA (RGPD/privacy)
