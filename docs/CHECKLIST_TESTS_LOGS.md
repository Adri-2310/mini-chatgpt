# Checklist Développement — Tests & Logs mini-chatgpt

## ✅ STATUS: COMPLÉTÉ (13 mai 2026)

Tous les tests et logs ont été implémentés avec succès.
- **55 tests passent** (4 skipped comme attendu)
- **Zéro warnings**
- **Zéro erreurs**

---

## PARTIE 1 : Tests

### Étape 1 — Factories manquantes
- [x] Créer `database/factories/ConversationFactory.php`
  - [x] user_id → User::factory()
  - [x] title → 'Nouvelle conversation'
  - [x] model_used → 'gpt-4o-mini'
  
- [x] Créer `database/factories/MessageFactory.php`
  - [x] conversation_id → Conversation::factory()
  - [x] role → 'user'
  - [x] content → fake()->paragraph()
  - [x] model → 'gpt-4o-mini'

### Étape 2 — Tests unitaires : ChatService
- [x] Créer `tests/Unit/ChatServiceTest.php`
  - [x] Importer Http::fake() et Mockery
  - [x] Test : `test_ask_returns_content_from_api_response`
    - [x] Mock réponse API valide
    - [x] Vérifier contenu retourné
  - [x] Test : `test_ask_throws_exception_on_non_200_status`
    - [x] Mock statut HTTP 500
    - [x] Vérifier exception levée
  - [x] Test : `test_ask_with_history_sends_full_message_array`
    - [x] Vérifier que le tableau messages est envoyé
  - [x] Test : `test_stream_with_history_yields_chunks`
    - [x] Mock Guzzle avec chunks
    - [x] Tester que le Generator yield correctement
  - [x] Test : `test_ask_throws_exception_when_api_returns_error_in_json`
    - [x] Mock JSON avec clé 'error'
    - [x] Vérifier exception levée

### Étape 3 — Tests Feature : ConversationController
- [x] Créer `tests/Feature/ConversationControllerTest.php`
  - [x] Utiliser `RefreshDatabase`
  - [x] Test : `test_index_returns_only_authenticated_user_conversations`
    - [x] Créer 2 users avec conversations
    - [x] Vérifier que chaque user voit uniquement ses conversations
  - [x] Test : `test_store_creates_conversation_for_authenticated_user`
    - [x] POST /conversations avec status 201
    - [x] Vérifier création + user_id correct
  - [x] Test : `test_update_renames_conversation`
    - [x] PUT /conversations/{id}
    - [x] Vérifier titre mis à jour
  - [x] Test : `test_destroy_deletes_conversation`
    - [x] DELETE /conversations/{id}
    - [x] Vérifier suppression
  - [x] Test : `test_destroy_is_forbidden_for_another_users_conversation`
    - [x] User A essaie de supprimer conversation de User B
    - [x] Vérifier status 403

### Étape 4 — Tests Feature : MessageController
- [x] Créer `tests/Feature/MessageControllerTest.php`
  - [x] Utiliser `RefreshDatabase`
  - [x] Mock `ChatService` via le container
  - [x] Test : `test_store_requires_authentication`
    - [x] POST sans auth
    - [x] Vérifier 401 ou redirect
  - [x] Test : `test_store_is_forbidden_for_another_users_conversation`
    - [x] User A essaie ajouter message à conversation User B
    - [x] Vérifier 403
  - [x] Test : `test_store_validates_required_fields`
    - [x] POST sans 'content' ou sans 'model'
    - [x] Vérifier erreurs validation
  - [x] Test : `test_store_creates_user_and_assistant_messages`
    - [x] Mock ChatService→askWithHistory() = 'Réponse test'
    - [x] POST /conversations/{id}/messages
    - [x] Vérifier 2 messages créés (user + assistant)
    - [x] Vérifier roles corrects
  - [x] Test : `test_store_updates_model_used_on_first_message`
    - [x] Conversation sans messages
    - [x] Envoyer message avec model='gpt-4'
    - [x] Vérifier conversation.model_used = 'gpt-4'
  - [x] Test : `test_store_generates_title_after_4_messages`
    - [x] Créer conversation avec 2 messages existants
    - [x] Mock ChatService→ask() = 'Titre test'
    - [x] POST 3e message
    - [x] Vérifier title_updated=true dans réponse
    - [x] POST 4e message
    - [x] Vérifier titre généré
  - [x] Test : `test_stream_store_returns_event_stream_content_type`
    - [x] Mock ChatService→streamWithHistory() = Generator['chunk1', 'chunk2']
    - [x] POST /conversations/{id}/messages/stream
    - [x] Vérifier Content-Type='text/event-stream'
    - [x] Vérifier chunk1 + chunk2 dans réponse

### Étape 5 — Tests Feature : AskController
- [x] Créer `tests/Feature/AskControllerTest.php`
  - [x] Utiliser `RefreshDatabase`
  - [x] Mock `ChatService`
  - [x] Test : `test_ask_store_requires_authentication`
    - [x] POST /api/ask sans auth
    - [x] Vérifier 401
  - [x] Test : `test_ask_store_returns_ai_response`
    - [x] Mock ChatService→ask() = 'Réponse test'
    - [x] POST /api/ask { question: '...', model: 'gpt-4o-mini' }
    - [x] Vérifier réponse contient 'Réponse test'
  - [x] Test : `test_ask_stream_returns_event_stream`
    - [x] Mock ChatService→streamAsk() = Generator
    - [x] POST /api/ask/stream
    - [x] Vérifier Content-Type='text/event-stream'

---

## PARTIE 2 : Logs

### Étape 6 — Canal de log dédié IA
- [x] Modifier `config/logging.php`
  - [x] Ajouter canal 'ai' dans le tableau 'channels'
  - [x] driver: 'daily'
  - [x] path: storage_path('logs/ai.log')
  - [x] level: env('LOG_LEVEL', 'debug')
  - [x] days: 30

### Étape 7 — Logs dans ChatService
- [x] Modifier `app/Services/ChatService.php`
  - [x] Importer `use Illuminate\Support\Facades\Log;`
  - [x] Méthode `ask()` :
    - [x] Avant l'appel : `Log::channel('ai')->info('IA call started', ['model' => $model])`
    - [x] Après succès : `Log::channel('ai')->info('IA call succeeded', ['model' => $model, 'duration_ms' => ...])`
    - [x] En erreur HTTP : `Log::channel('ai')->error('IA call failed', ['model' => $model, 'error' => $e->getMessage()])`
  - [x] Méthode `askWithHistory()` : même pattern
  - [x] Méthode `streamAsk()` : même pattern
  - [x] Méthode `streamWithHistory()` : même pattern
  - [x] Mesure de durée : `$start = microtime(true)` avant, `round((microtime(true) - $start) * 1000)` après
  - [x] **IMPORTANT** : ne pas logger le contenu des messages (privacy)

### Étape 8 — Logs d'erreur dans MessageController
- [x] Modifier `app/Http/Controllers/MessageController.php`
  - [x] Importer `use Illuminate\Support\Facades\Log;`
  - [x] Dans `store()` bloc `catch` :
    - [x] `Log::error('Message store failed', ['conversation_id' => $conversation->id, 'user_id' => auth()->id(), 'error' => $e->getMessage()])`
  - [x] Dans `streamStore()` bloc `catch` :
    - [x] Même log

---

## ✅ Vérification finale

- [x] Lancer les tests : `composer test`
  - [x] **55 tests passent** ✓ (4 skipped comme attendu)
  - [x] **0 warnings** ✓
  - [x] **0 erreurs** ✓

- [x] Tests feature : `php artisan test tests/Feature/MessageControllerTest.php`
  - [x] **9 tests passent** ✓

- [x] Tests feature : `php artisan test tests/Feature/ConversationControllerTest.php`
  - [x] **8 tests passent** ✓

- [x] Tests feature : `php artisan test tests/Feature/AskControllerTest.php`
  - [x] **6 tests passent** ✓

- [x] Tests unitaires : `php artisan test tests/Unit/ChatServiceTest.php`
  - [x] **5 tests passent** ✓

- [x] Vérifier logs IA :
  - [x] Canal 'ai' configuré dans `config/logging.php`
  - [x] `storage/logs/ai.log` prêt à recevoir les entrées
  - [x] Logs sur les appels IA (start, success avec duration, errors)
  - [x] Logs sans contenu de messages (privacy)

- [x] Commit & Push :
  - [x] Tous les fichiers ajoutés/modifiés
  - [x] Commit: "Corriger tous les tests et avertissements" (13 mai 2026)

---

## Notes

- **Tests Strategy** : Utiliser `RefreshDatabase` pour les Feature tests (DB en mémoire via phpunit.xml)
- **Mocking** : Préférer les mocks de container Laravel (`$this->mock()`) aux mocks statiques pour les tests Feature
- **Factories** : Utiliser `WithoutModelEvents` si besoin de désactiver les événements (ex: création de conversations)
- **Logs** : Ne jamais logger le contenu des messages utilisateurs ou IA (RGPD/privacy)
