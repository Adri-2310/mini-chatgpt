# 🏗️ Architecture Mini-ChatGPT

Architecture système complète de l'application Mini-ChatGPT.

---

## 📊 Vue d'ensemble

```
┌─────────────────────────────────────────────────────┐
│                Frontend (Vue.js 3)                  │
│  - Interface de chat interactive                    │
│  - Composants réutilisables                         │
│  - Gestion d'état avec Composition API              │
└────────────────┬────────────────────────────────────┘
                 │ (Inertia.js + HTTP/WebSocket)
┌────────────────▼────────────────────────────────────┐
│              Backend (Laravel 12)                    │
│  - API RESTful                                       │
│  - Authentification (Jetstream)                      │
│  - Services LLM (OpenAI, Gemini, Claude)             │
│  - Gestion WebSocket/SSE (Reverb)                    │
└────────────────┬────────────────────────────────────┘
                 │
┌────────────────▼────────────────────────────────────┐
│         Base de Données (PostgreSQL/MySQL)          │
│  - Users, Conversations, Messages                   │
│  - CustomInstructions, LLMModels                    │
└─────────────────────────────────────────────────────┘
                 │
┌────────────────▼────────────────────────────────────┐
│           APIs Externes (LLM Providers)              │
│  - OpenAI (GPT-4o)                                   │
│  - Google (Gemini 2.5 Flash)                        │
│  - Anthropic (Claude)                               │
└─────────────────────────────────────────────────────┘
```

---

## 🏭 Composants Principaux

### Backend (Laravel)

#### Controllers
- `ChatController` - Gestion des conversations
- `MessageController` - Gestion des messages
- `ModelController` - Sélecteur de modèles
- `InstructionController` - Instructions personnalisées

#### Services
- `OpenAIService` - Intégration OpenAI
- `GeminiService` - Intégration Google Gemini
- `ClaudeService` - Intégration Anthropic Claude
- `StreamingService` - Gestion du streaming

#### Models (Eloquent)
- `User` - Utilisateur authentifié
- `Conversation` - Conversation (avec titre auto-généré)
- `Message` - Message (utilisateur ou IA)
- `CustomInstruction` - Instructions personnalisées
- `LLMModel` - Modèle disponible
- `ApiCredential` - Clés API

### Frontend (Vue.js 3)

#### Layouts
- `AppLayout` - Layout principal avec sidebar
- `GuestLayout` - Layout pour auth

#### Pages
- `Chat.vue` - Page principale du chat
- `Settings.vue` - Configuration instructions
- `Profile.vue` - Profil utilisateur

#### Components
- `ChatMessage` - Message dans le chat
- `ModelSelector` - Sélecteur de modèle
- `ConversationList` - Liste des conversations
- `InstructionForm` - Formulaire instructions

---

## 🔄 Flux de Données

### 1. **Envoi d'un message**
```
Utilisateur
    ↓
ChatMessage.vue (composant Vue)
    ↓
POST /api/messages (Laravel)
    ↓
MessageController@store
    ↓
StreamingService (sélection du LLM)
    ↓
API LLM (OpenAI/Gemini/Claude)
    ↓
WebSocket/SSE (streaming token par token)
    ↓
Vue.js (affichage temps réel)
```

### 2. **Historique des conversations**
```
Chargement page
    ↓
GET /api/conversations (Laravel)
    ↓
ConversationController@index
    ↓
Récupération BD
    ↓
ConversationList.vue (affichage)
```

### 3. **Sélection de modèle**
```
ModelSelector.vue (combobox)
    ↓
Sauvegarde user preference
    ↓
POST /api/user/model
    ↓
UserController@updatePreferredModel
    ↓
BD (sauvegarde)
```

---

## 🗄️ Schéma Base de Données

### Tables Principales

#### users
```sql
id, name, email, password, created_at, updated_at
preferred_model_id, custom_instructions (JSON)
```

#### conversations
```sql
id, user_id, title, created_at, updated_at
first_message_preview (pour générer le titre auto)
```

#### messages
```sql
id, conversation_id, role (user|assistant), content, created_at
model_used, tokens_used
```

#### custom_instructions
```sql
id, user_id, instructions (TEXT), enabled, created_at, updated_at
```

#### llm_models
```sql
id, name (gpt-4o, gemini-2.5-flash, claude-3.5), provider, config_json
```

---

## 🔐 Sécurité

- **Authentication**: Laravel Sanctum + Sessions sécurisées
- **API Keys**: Stockées en variables d'environnement (`.env`)
- **CORS**: Configuré pour frontend uniquement
- **CSRF**: Protection CSRF sur toutes les routes POST
- **Rate Limiting**: Limiter les appels LLM par utilisateur
- **Input Validation**: Validation côté serveur de tous les inputs

---

## ⚡ Performance

- **Frontend**: Vue.js 3 avec Composition API (réactif)
- **Backend**: Laravel avec caching des conversations
- **DB**: Indexing sur user_id, conversation_id
- **Streaming**: WebSocket pour temps réel sans latence
- **Assets**: TailwindCSS minifié, Vite bundling

---

## 🔌 Technologies Stack

| Couche | Technologie | Version |
|--------|-------------|---------|
| Backend | Laravel | 12.x |
| Frontend | Vue.js | 3.5+ |
| Bridge | Inertia.js | 2.0+ |
| Styling | TailwindCSS 4 + Tweakcn | 4.x + CSS Variables |
| Build | Vite | 7.x |
| Database | PostgreSQL/MySQL | 14+/8+ |
| Realtime | Laravel Reverb | Latest |
| Auth | Jetstream + Sanctum | Latest |

---

## 📦 Déploiement

### Render.com
```
Frontend: Serveur Node.js (Vite dev server)
Backend: Serveur Node.js (PHP avec Render runtime)
Database: PostgreSQL managed
```

### Environnement Local
```
Backend: php artisan serve (port 8000)
Frontend: npm run dev (Vite HMR)
Database: PostgreSQL local
```

---

## 🎨 Système de Design (Tweakcn)

### Vue d'ensemble

Le système de design Tweakcn est un ensemble de variables CSS personnalisées inspiré de shadcn/ui :

- **Palette de couleurs** : Clair et sombre avec contraste WCAG AA
- **Variables CSS** : 40+ tokens pour colors, typography, shadows
- **Mode sombre natif** : Classe `.dark` appliquée à `<html>`
- **Intégration Tailwind** : Toutes les variables utilisables comme classes Tailwind

### Architecture

```
resources/css/
├── theme.css              # Variables CSS (~85 variables)
├── app.css                # @tailwind directives
└── theme integration:
    └── tailwind.config.js  # Mappage variables → classes Tailwind
```

### Variables principales

**Couleurs**
- `--background`, `--foreground` : Fond et texte principaux
- `--primary`, `--primary-foreground` : Boutons CTA
- `--secondary`, `--accent`, `--destructive` : États différents
- `--card`, `--input`, `--border` : Composants UI
- `--sidebar-*` : Éléments sidebar (optionnel)
- `--chart-1` à `--chart-5` : Graphiques

**Typographie**
- `--font-sans`, `--font-serif`, `--font-mono` : Familles de polices
- `--radius` : Rayon de bordure global (0.75rem)

**Ombres**
- `--shadow-2xs` à `--shadow-md` : Niveaux d'ombre

### Mode sombre

Le mode sombre utilise le modèle `darkMode: 'class'` de TailwindCSS. La classe `.dark` est appliquée à l'élément `<html>` pour activer automatiquement toutes les variables CSS du mode sombre.

### Utilisation

Tous les composants Vue utilisent les classes Tailwind générées à partir des variables CSS tweakcn.

### Personnalisation

Pour personnaliser le thème :

1. **Modifier les couleurs** → Éditer `:root` et `.dark` dans `resources/css/theme.css`
2. **Ajouter des variables** → Ajouter aussi dans `tailwind.config.js`
3. **Créer un thème alternatif** → Ajouter une classe CSS au `<html>`

### Bonnes pratiques

✅ Utiliser les classes Tailwind (cohérence, maintenance)
✅ Tester en mode clair et sombre
✅ Respecter le contraste WCAG AA
✅ Ajouter des transitions de couleur sur les changements de thème

❌ Éviter les couleurs en dur (#rgb)
❌ Créer des couleurs hors du système tweakcn
❌ Oublier le dark mode lors du développement

**Documentation détaillée** : [`docs/styling/THEME_TWEAKCN.md`](../styling/THEME_TWEAKCN.md)

---

## 🧪 Tests

- **Unit Tests**: Tests des Services LLM
- **Feature Tests**: Tests des Controllers
- **E2E Tests**: Dusk (optionnel)
- **Coverage**: Minimum 50%

---

