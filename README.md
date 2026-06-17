# SaveurIA — Assistant IA Culinaire

SaveurIA est une application web de chat IA multi-modèles, présentée comme un assistant culinaire expert. Construite avec **Laravel 12** et **Vue 3 + Inertia.js**, elle permet aux utilisateurs de gérer des conversations persistantes avec différents modèles LLM (GPT-4o, Gemini, Claude) via **OpenRouter.ai**.

**Status** : ✅ Production-ready (Coolify + PostgreSQL)  
**Dernière mise à jour** : 2026-06-17

---

## 🚀 Démarrage rapide

### Prérequis
- PHP >= 8.2
- Node.js >= 18
- MySQL (dev et rod)
- Composer 2.2+, npm 10+

### Installation locale

```bash
# 1. Cloner le projet
git clone <repo> mini-chatgpt
cd mini-chatgpt

# 2. Installer les dépendances
composer install
npm install

# 3. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 4. Créer la base de données
php artisan migrate:fresh --seed

# 5. Lancer le serveur
npm run dev        # Terminal 1 - Frontend (Vite)
php artisan serve  # Terminal 2 - Backend
```

Accès : **http://localhost:8000**

---

## 📋 Stack technique

| Composant | Technologie | Version |
|-----------|-------------|---------|
| **Backend** | Laravel | 12.x |
| **Frontend** | Vue 3 + Inertia.js | 3.3 / 2.0 |
| **Build** | Vite | 7.x |
| **Styling** | Tailwind CSS | 3.4 |
| **Components** | shadcn-vue | 2.7+ |
| **Auth** | Jetstream + Fortify + Sanctum | 5.5 / 4.0 |
| **Database** | MySQL (dev), PostgreSQL (prod) | — |
| **LLM Gateway** | OpenRouter.ai | API REST |
| **Container** | Docker | Multi-stage |

---

## 🎯 Fonctionnalités principales

### Authentification
- ✅ Inscription + vérification email obligatoire
- ✅ 2FA (TOTP - Fortify)
- ✅ Changement d'email sécurisé (token + rate-limit)
- ✅ Profil utilisateur (photo, instructions personnalisées)

### Conversations IA
- ✅ Deux modes : **Chat** (avec historique) et **Ask** (question directe)
- ✅ Sélection du modèle LLM par message
- ✅ Streaming temps réel (SSE)
- ✅ Génération automatique du titre par IA
- ✅ Soft deletes (corbeille)
- ✅ Export (JSON / Markdown)
- ✅ Recherche dans messages

### Suivi & Statistiques
- ✅ Comptage tokens (input + output)
- ✅ Calcul coût USD en temps réel
- ✅ Dashboard avec stats mensuelles
- ✅ Historique conversations

### Interface
- ✅ Thème clair/sombre
- ✅ Rendu Markdown + coloration syntaxique
- ✅ Notifications toast
- ✅ Responsive design
- ✅ Localisation française complète

---

## 🏗️ Architecture

```
┌─────────────────────────────────────────────┐
│         Navigateur (Vue 3 + Inertia)        │
└────────────────┬────────────────────────────┘
                 │ SPA-like (pas de refresh)
┌────────────────▼────────────────────────────┐
│      Laravel 12 — Backend                   │
├─────────────────────────────────────────────┤
│ Routes (web.php, api.php)                   │
│ ├── Auth & Profile                          │
│ ├── Chat (ask, conversations, messages)     │
│ └── Settings                                │
│                                             │
│ Controllers → Services → Models → DB        │
│ ├── ChatService (OpenRouter client)         │
│ ├── Observers (stats auto-update)           │
│ └── Policies (authorization)                │
└─────────────────┬──────────────────────────┘
                  │
         ┌────────┴─────────┐
         │                  │
    ┌────▼────┐      ┌──────▼──────┐
    │ MySQL   │      │ OpenRouter  │
    │dev prod │      │ (LLM API)   │
    └─────────┘      └─────────────┘
```

**Modèles clés :**
- `User` : entité centrale avec 2FA, changement email sécurisé
- `Conversation` : conversations avec soft deletes
- `Message` : messages avec tokens et coûts
- `LlmModel` : référentiel des modèles disponibles
- `CustomInstruction` : system prompt personnalisé (1-to-1 user)
- `UserStats` : statistiques mensuelles/cumulatives

[Voir le détail complet : `docs/ARCHITECTURE.md`]

---

## 🔐 Sécurité

- **Authentification** : Fortify + Sanctum tokens
- **Autorisation** : Policy stricte (ownership user_id)
- **Email change** : Token SHA-256, rate-limiting 5min, expiration 7j
- **2FA** : TOTP (Time-based OTP)
- **CSRF** : Protection middleware Laravel
- **SQL** : Prepared statements (Eloquent)

---

## ⚙️ Configuration

### Variables d'environnement

```env
# Application
APP_NAME="SaveurIA"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=fr

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=db_saveuria_dev
DB_USERNAME=root
DB_PASSWORD=root

# LLM (OpenRouter)
OPENROUTER_API_KEY=sk-or-v1-...

# Mail
MAIL_MAILER=log
MAIL_FROM_ADDRESS=hello@example.com
```

### Configuration fichiers

- `config/ai_models.php` : Modèles LLM, prix, comportements
- `config/saveurial.php` : Identité assistant, system prompt
- `config/logging.php` : Canal `ai` pour logs appels IA

---

## 📦 Dépendances principales

**Backend (PHP)**
```
laravel/framework ^12.0
laravel/jetstream ^5.5
laravel/fortify
laravel/sanctum ^4.0
guzzlehttp/guzzle
```

**Frontend (JavaScript)**
```
vue ^3.3
@inertiajs/vue3 ^2.0
tailwindcss ^3.4
shadcn-vue 2.7+
```

[Versions exactes : `composer.json`, `package.json`]

---

## 🧪 Tests

```bash
# Lancer les tests
php artisan test

# Avec couverture de code
php artisan test --coverage

# Tests spécifiques
php artisan test --filter ChatService
```

**Couverture** : Tests Feature complets sur flux critiques (auth, conversations, messages, ask).

---

## 🚢 Déploiement

### Production (Docker)

```bash
# Build
docker build -t saveuria:latest .

# Run
docker run -p 80:80 \
  -e APP_ENV=production \
  -e OPENROUTER_API_KEY=sk-or-v1-... \
  saveuria:latest
```

**Build multi-stage** :
1. Composer dependencies (vendors optimisés)
2. Vite build (assets compilés)
3. Runtime (PHP 8.4 + Nginx + Supervisor)

**Services :** PostgreSQL + Redis (recommandé pour cache/queue)

---

## ⚡ Points clés

### Points forts ✅
- Architecture propre (Controller / Service / Observer)
- Streaming SSE natif (pas de polling)
- Sécurité email robuste
- Migrations documentées en français
- Docker production-ready
- Tests Feature complets

### À améliorer ⚠️
- Pagination conversations (charger toutes actuellement)
- Fenêtre contexte LLM (limiter tokens envoyés)

[Voir détails : `docs/ARCHITECTURE.md` § Faiblesses]

---

## 📚 Documentation

- **[ARCHITECTURE.md](docs/architecture/ARCHITECTURE.md)** — Architecture technique détaillée, patterns, faiblesses
- **[DATABASE_SCHEMA.sql](docs/DATABASE_SCHEMA.sql)** — Schéma complet avec commentaires
- **[DIAGRAM_CLASSES.puml](docs/DIAGRAM_CLASSES.puml)** — Diagramme UML (classes + relations)

---

## 🔄 Versioning migrations

Toutes les migrations utilisent le préfixe `2024_01_01_00000X_*` pour garantir un ordre d'exécution déterministe.

**Actuels** : 7 migrations consolidées (une par table)
```
2024_01_01_000000 — Infrastructure Laravel (sessions, cache, jobs)
2024_01_01_000001 — Users
2024_01_01_000002 — Conversations
2024_01_01_000003 — LLM Models (+ seed 3 modèles)
2024_01_01_000004 — Messages
2024_01_01_000005 — Custom Instructions
2024_01_01_000006 — User Stats
```

---

## 📊 Statistiques du projet

| Métrique | Valeur |
|----------|--------|
| **Fichiers PHP** | ~80 (Controllers, Models, Services, etc.) |
| **Fichiers Vue** | ~40 (Pages, Components, Layouts) |
| **Tests** | 14+ fichiers Feature, 1 Unit |
| **Migrations** | 7 (consolidées) |
| **Seeders** | 4 (User, Conversation, Message, CustomInstruction) |
| **Routes web** | 20+ endpoints |
| **Routes API** | 15+ endpoints (Sanctum) |

---

## 🔗 Ressources

- [Laravel 12 Documentation](https://laravel.com/docs/12.x)
- [Vue.js 3 Guide](https://vuejs.org/guide/)
- [Inertia.js](https://inertiajs.com/)
- [OpenRouter API](https://openrouter.ai/docs)
- [TailwindCSS 3](https://tailwindcss.com/docs/v3)

---

## 👨‍💻 Auteur

**Adrien Mertens**  
Projet d'examen SGBD — Année 2026

---

## 📄 Licence

Tous droits réservés. Usage personnel.
