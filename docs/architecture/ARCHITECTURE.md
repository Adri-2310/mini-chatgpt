# Architecture — SaveurIA

Document technique détaillé : architecture, patterns, stack, et points d'attention.

**Généré le** : 2026-06-17

---

## 1. Vue d'ensemble

SaveurIA est une application de chat IA multi-modèles. Elle expose deux modes :
- **Chat** : conversation persistante avec historique complet
- **Ask** : question directe sans contexte

L'application est containerisée (Docker multi-stage) et prévue pour déploiement Coolify/Render.

---

## 2. Stack technique

| Couche | Technologie | Rôle |
|-------|---|---|
| **Language Backend** | PHP 8.2+ | Serveur |
| **Framework Backend** | Laravel 12 | MVC, ORM, auth, DB |
| **Language Frontend** | JavaScript / TypeScript | Client |
| **Framework Frontend** | Vue 3 + Inertia.js | UI réactive (SPA-like) |
| **Build Frontend** | Vite 7 | Bundle, HMR |
| **Styling** | Tailwind CSS 3 + shadcn-vue | Design system |
| **Authentication** | Jetstream 5.5 + Fortify + Sanctum 4 | Login 2FA, tokens API |
| **Database dev et prod** | MySQL 8+ | Persistance (prod et dev) |
| **HTTP Client** | Guzzle | Requêtes OpenRouter |
| **Streaming** | Server-Sent Events (SSE) | Messages temps réel |
| **LLM Provider** | OpenRouter.ai | Gateway API 3 modèles |
| **Container** | Docker | Deployment |

---

## 3. Architecture applicative (C4 simplifié)

### Composants

```
┌─────────────────────────────────────────────────────┐
│ NAVIGATEUR (Vue 3 + Inertia.js)                    │
│ ├── Pages/                                          │
│ │   ├── Auth/*           (login, register, 2FA)    │
│ │   ├── Chat.vue          (chat avec historique)   │
│ │   ├── Ask.vue           (question directe)       │
│ │   ├── Settings.vue      (instructions perso)     │
│ │   └── Dashboard.vue     (stats)                  │
│ └── Components/           (composants réutilisables)
└────────────────┬──────────────────────────────────┘
                 │ Inertia (JSON + état PHP)
                 │
┌────────────────▼──────────────────────────────────┐
│ LARAVEL 12 — API / Serveur de Rendu               │
│                                                    │
│ Routes                                             │
│ ├── POST /ask                 (question directe)  │
│ ├── POST /ask/stream          (streaming)         │
│ ├── POST /conversations/{id}/messages (msg)      │
│ ├── GET  /conversations       (liste)             │
│ ├── POST /conversations       (créer)             │
│ └── DELETE /conversations/{id} (supprimer)        │
│                                                    │
│ Controllers                                        │
│ ├── AskController                                  │
│ ├── ConversationController                        │
│ ├── MessageController                             │
│ └── SettingsController                            │
│                                                    │
│ Services                                           │
│ └── ChatService              (client OpenRouter) │
│                                                    │
│ Models (Eloquent ORM)                             │
│ ├── User                                           │
│ ├── Conversation                                   │
│ ├── Message                                        │
│ ├── LlmModel                                       │
│ ├── CustomInstruction                             │
│ └── UserStats                                      │
│                                                    │
│ Middleware                                         │
│ ├── auth:sanctum             (API tokens)         │
│ ├── verified                 (email vérifié)      │
│ └── throttle                 (rate limit)         │
│                                                    │
│ Observers                                          │
│ ├── ConversationObserver     (stats auto-update)  │
│ └── MessageObserver          (stats auto-update)  │
│                                                    │
│ Jobs (Queue)                                       │
│ ├── DeleteUnverifiedUsers                         │
│ └── CleanupPendingEmails                          │
│                                                    │
│ Policies (Authorization)                          │
│ └── ConversationPolicy       (ownership check)   │
└────────────┬─────────────────────────────┬────────┘
             │                             │
    ┌────────▼────────┐          ┌────────▼─────────┐
    │ MySQL                      │  OpenRouter.ai   │
    │ DB local/prod              │  LLM Gateway     │
    │                            │  - GPT-4o mini   │
    │ Tables :                   │  - Gemini 2.5    │
    │ - users                    │  - Claude 3.5    │
    │ - conversations            │                  │
    │ - messages                 │  GET /api/models │
    │ - llm_models               │  POST /chat/     │
    │ - custom_instructions      │       completions│
    │ - user_stats               │                  │
    └────────────────┘          └──────────────────┘
```

---

## 4. Modèles de données (Eloquent)

### User

```php
User {
    id: bigint [PK]
    name: string
    email: string [UNIQUE]
    email_verified_at: datetime
    password: string (bcrypt)
    pending_email: string [UNIQUE, nullable] — nouvel email en attente
    pending_email_sent_at: datetime [nullable]
    pending_email_token: string [UNIQUE, nullable] — SHA-256 token
    two_factor_secret: text [nullable] — TOTP secret
    two_factor_recovery_codes: text [nullable] — codes de secours
    two_factor_confirmed_at: datetime [nullable]
    remember_token: string [nullable]
    profile_photo_path: varchar(2048) [nullable]
    created_at, updated_at: timestamp
    
    Relations:
    - HasMany Conversation (1-to-N, cascadeOnDelete)
    - HasOne CustomInstruction (1-to-1, cascadeOnDelete)
    - HasOne UserStats (1-to-1, cascadeOnDelete)
    - HasManyThrough Message (via Conversation)
    
    Methods:
    - sendEmailVerificationNotification()
    - sendPasswordResetNotification(token)
    - getDashboardStats(): array
    - recordUsage(tokens, cost) — met à jour stats
}
```

### Conversation

```php
Conversation {
    id: bigint [PK]
    user_id: bigint [FK → users, cascadeOnDelete, indexed auto]
    title: string [nullable] — généré par IA ou utilisateur
    model_used: string — identifiant modèle (ex: "openai/gpt-4o-mini")
    created_at, updated_at: timestamp
    deleted_at: timestamp [nullable] — soft deletes
    
    Relations:
    - BelongsTo User
    - HasMany Message (1-to-N, cascadeOnDelete)
    
    Scopes:
    - whereNotTrashed() — exclure soft-deleted
    - ordered() — tri par created_at DESC
}
```

### Message

```php
Message {
    id: bigint [PK]
    conversation_id: bigint [FK → conversations, cascadeOnDelete, indexed auto]
    llm_model_id: bigint [FK → llm_models, nullOnDelete, indexed auto]
    role: ENUM('user', 'assistant') [NOT indexed - faible sélectivité]
    content: longtext
    model: varchar(255) [nullable] — trace brute du model_id envoyé
    tokens_used: int [unsigned, nullable]
    cost_usd: decimal(8,6) [nullable] — max 99.999999 USD
    created_at, updated_at: timestamp
    
    Relations:
    - BelongsTo Conversation
    - BelongsTo LlmModel (nullable)
    
    Scopes:
    - byRole('user') / byRole('assistant')
    - withCosts()
}
```

### LlmModel

```php
LlmModel {
    id: bigint [PK]
    name: string [UNIQUE] — "GPT-4o mini"
    provider: string [indexed] — "OpenAI" / "Google" / "Anthropic"
    model_id: string [UNIQUE] — "openai/gpt-4o-mini"
    description: text [nullable]
    enabled: boolean [DEFAULT true]
    max_tokens: int [DEFAULT 4096]
    config: json [nullable] — température, top_p, etc.
    created_at, updated_at: timestamp
    
    Relations:
    - HasMany Message
    
    Methods:
    - getEnabled(): Collection [static, cached 1h]
    
    Data (seeded) :
    - openai/gpt-4o-mini
    - google/gemini-2.5-flash
    - anthropic/claude-3.5-haiku
}
```

### CustomInstruction

```php
CustomInstruction {
    id: bigint [PK]
    user_id: bigint [FK → users, UNIQUE, cascadeOnDelete, indexed auto]
    instructions: longtext [nullable] — system prompt libre
    enabled: boolean [DEFAULT true]
    created_at, updated_at: timestamp
    
    Relations:
    - BelongsTo User
    
    Auto-created: à l'inscription de l'utilisateur via User::boot()
}
```

### UserStats

```php
UserStats {
    id: bigint [PK]
    user_id: bigint [FK → users, UNIQUE, cascadeOnDelete]
    total_messages: int [unsigned, DEFAULT 0] — cumulatif
    total_conversations: int [unsigned, DEFAULT 0] — cumulatif
    total_tokens: bigint [unsigned, DEFAULT 0] — peut dépasser 4M
    monthly_cost: decimal(10,6) [DEFAULT 0.00] — max 9999.999999 USD
    monthly_messages: int [unsigned, DEFAULT 0] — du mois courant
    last_activity_at: datetime [nullable]
    stats_computed_at: timestamp [DEFAULT CURRENT_TIMESTAMP]
    created_at, updated_at: timestamp
    
    Relations:
    - BelongsTo User
    
    Updated by: Observers (ConversationObserver, MessageObserver)
    
    Note: `total_cost` a été volontairement supprimé (dérive à la demande).
}
```

---

## 5. Flows principaux

### Flow 1 : Envoyer un message avec streaming

```
Client (Vue)
    │
    ├─→ POST /conversations/{id}/messages/stream
    │   └─→ ConversationPolicy::update() [vérifier ownership]
    │       └─→ MessageController::storeStream()
    │           └─→ ChatService::streamWithHistory()
    │               └─→ Guzzle HTTP request (OpenRouter)
    │                   ├─→ Récupère historique complet (prepareMessageContext)
    │                   ├─→ Construit payload OpenRouter
    │                   └─→ SSE stream (Server-Sent Events)
    │
    └─ Réception headers SSE + [DONE] token
        ├─ MessageObserver::created() déclenché
        │   └─ UserStats::updateStats() (tokens + cost)
        └─ Notification toast succès
```

### Flow 2 : Créer une conversation avec titre auto-généré

```
Client (Vue)
    │
    ├─→ POST /conversations
    │   └─→ ConversationController::store()
    │       └─→ Conversation::create()
    │           └─→ ConversationObserver::created()
    │               └─→ UserStats::updateTotal()
    │
    ├─→ POST /conversations/{id}/messages (premier message)
    │   └─→ [Flow ci-dessus] + ChatService::generateTitle()
    │       └─→ Appel IA (GPT-4o mini fixe, 30 tokens max)
    │           └─→ Conversation::update(['title' => '...'])
    │
    └─ Client reçoit conversation avec titre
```

### Flow 3 : Changement d'email sécurisé

```
Client (Vue)
    │
    ├─→ PUT /settings/email
    │   └─→ User::update(['pending_email' => 'new@example.com'])
    │       └─→ Notification email (lien avec token)
    │           Token = SHA-256(new_email + secret + timestamp)
    │
    ├─→ Email click : GET /email/verify-change/{token}
    │   └─→ Token valide && !expiré (7j) ?
    │       └─→ User::update(['email' => pending_email, 'pending_email' => NULL])
    │           └─→ Notification confirmation
    │               └─→ Redirect login
    │
    └─ CleanupPendingEmails job (daily) : purge tokens > 7j
```

---

## 6. Services et Traits

### ChatService

```php
// app/Services/ChatService.php
class ChatService {
    // Interface publique
    public function ask(string $prompt, ?string $modelId = null): string
    public function askWithHistory(Conversation $conv, string $msg, ?string $modelId = null): string
    public function stream(string $prompt, ?string $modelId = null)
    public function streamWithHistory(Conversation $conv, string $msg, ?string $modelId = null)
    
    // Internal
    private function prepareMessageContext(Conversation $conv): array
    private function buildOpenRouterPayload(...): array
    private function generateTitle(Conversation $conv, string $context): string
    private function calculateCost(int $tokens_in, int $tokens_out): float
}
```

### CalculateCosts (Trait)

```php
// app/Traits/CalculateCosts.php
trait CalculateCosts {
    // Calcule coût USD basé sur tokens
    public function calculateCost(string $modelId, int $input_tokens, int $output_tokens): float
    
    // Lit config/ai_models.php pour pricing
}
```

### Observers

```php
// app/Observers/ConversationObserver.php
public function created(Conversation $conversation)
    → UserStats::increment('total_conversations')

public function deleted(Conversation $conversation)
    → [soft delete, rien]

public function forceDeleted(Conversation $conversation)
    → UserStats::decrement('total_conversations')

// app/Observers/MessageObserver.php
public function created(Message $message)
    → UserStats::updateStats($message->tokens_used, $message->cost_usd)
```

---

## 7. Routes API

### Public
```
GET  /                          Welcome page
GET  /about                     About page
POST /register                  Inscription
POST /login                     Connexion
GET  /password/reset            Demande réinit password
POST /password/reset            Confirmer réinit
```

### Authentifiés (auth:sanctum + verified)
```
GET  /dashboard                 Dashboard (stats)
GET  /chat                      Interface Chat
POST /ask                       Question directe (non-stream)
POST /ask/stream                Question directe (SSE)

GET    /conversations           Liste (PAGINER ?)
POST   /conversations           Créer
GET    /conversations/{id}      Détail
PUT    /conversations/{id}      Modifier (title)
DELETE /conversations/{id}      Soft delete

GET    /conversations/{id}/stats         Stats tokens/coûts
GET    /conversations/{id}/search?q=...  Recherche messages
GET    /conversations/{id}/export?fmt=json|markdown   Export

POST /conversations/{id}/messages          Envoyer (non-stream)
POST /conversations/{id}/messages/stream   Envoyer (SSE)

GET  /settings                  Settings page
PUT  /settings/instructions     Mise à jour instructions
PUT  /settings/email            Demande changement email

GET  /email/verify/{id}/{hash}              Vérification inscription
GET  /email/verify-change/{token}           Confirmation changement email
POST /email/pending-email-send              Renvoi lien changement
```

---

## 8. Configuration

### config/ai_models.php

```php
return [
    'default' => 'openai/gpt-4o-mini',
    'models' => [
        'openai/gpt-4o-mini' => [
            'name' => 'GPT-4o mini',
            'input_price_per_million' => 0.15,
            'output_price_per_million' => 0.60,
            'max_tokens' => 4096,
        ],
        // ... autres modèles
    ],
];
```

### config/saveurial.php

```php
return [
    'name' => 'SaveurIA',
    'system_prompt' => 'Tu es SaveurIA, un assistant culinaire expert...',
    'default_model' => 'openai/gpt-4o-mini',
    'title_generation' => [
        'model' => 'openai/gpt-4o-mini', // fixe
        'max_tokens' => 30,
        'prompt' => 'Génère un titre court...',
    ],
];
```

---

## 9. Points forts

✅ **Architecture claire** : séparation Controller → Service → Model  
✅ **Streaming SSE** : implémentation correcte du protocole HTTP  
✅ **Sécurité email** : token SHA-256, unicité en DB, rate-limit  
✅ **Observers idiomatiques** : stats auto-mises à jour sans logique dispersée  
✅ **Autorisation stricte** : Policy ownership sur toutes les routes  
✅ **LLM abstrait** : OpenRouter permet switch GPT ↔ Gemini ↔ Claude  
✅ **Migrations documentées** : commentaires détaillés en français  
✅ **Docker production** : multi-stage, Supervisor, optimisé  

---

## 10. Ressources

- Laravel Docs : https://laravel.com/docs/12.x
- Inertia.js : https://inertiajs.com/
- OpenRouter : https://openrouter.ai/docs
- SSE Protocol : https://html.spec.whatwg.org/multipage/server-sent-events.html

---

**Généré le** : 2026-06-17  
**Auteur** : Adrien Mertens
