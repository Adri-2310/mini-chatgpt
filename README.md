# 🌶️ Saveur IA

Une application web moderne pour interagir avec plusieurs modèles d'IA (GPT-4o, Gemini, Claude) avec historique persistant et streaming en temps réel.

**Stack**: Laravel 12 + Vue.js 3 + Inertia.js + TailwindCSS 3  
**Status**: ✅ Production (Coolify + MySQL)

---

## 🚀 Fonctionnalités principales

- ✅ **Sélecteur de modèles** : Basculer entre GPT-4o, Gemini et Claude en temps réel
- ✅ **Streaming SSE** : Affichage token-par-token des réponses
- ✅ **Historique persistant** : Conversations auto-sauvegardées avec titres générés par IA
- ✅ **Instructions personnalisées** : Configurer le comportement de l'IA par utilisateur
- ✅ **Authentification sécurisée** : Jetstream + Fortify avec 2FA (TOTP)
- ✅ **Mode sombre** : Thème clair/sombre avec variables CSS
- ✅ **Localisation française** : Interface et emails 100% en français

---

## 🛠️ Stack technologique

| Composant | Version | Rôle |
|-----------|---------|------|
| **Backend** | Laravel 12 | API, authentification, gestion données |
| **Frontend** | Vue.js 3 | Interface réactive (Composition API) |
| **Bridge** | Inertia.js | Liaison Laravel ↔ Vue.js |
| **Components** | shadcn/vue | Composants UI premium |
| **Styling** | TailwindCSS 3 | Design système |
| **Auth** | Jetstream + Fortify | Authentification sécurisée |
| **Temps réel** | SSE | Streaming des réponses IA |
| **BD** | MySQL | Persistance des données |
| **Déploiement** | Coolify | Container orchestration |

---

## 📋 Prérequis

- **PHP 8.4+**
- **Composer 2.2+**
- **Node.js 20+ et npm 10+**
- **MySQL 8.0+**

---

## 🔧 Installation

```bash
# 1. Cloner le repository
git clone https://github.com/ton-username/mini-chatgpt.git
cd mini-chatgpt

# 2. Installer les dépendances
composer install
npm install

# 3. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 4. Configurer la base de données dans .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=mini_chatgpt
# DB_USERNAME=root
# DB_PASSWORD=root

# 5. Créer les tables
php artisan migrate

# 6. Démarrer en développement
npm run dev  # Terminal 1 - Frontend (Vite)
php artisan serve  # Terminal 2 - Backend
```

L'app sera accessible à: **http://localhost:8000**

---

## ⚙️ Configuration

### Variables d'environnement essentielles

```env
# Application
APP_NAME="SaveurIA"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr

# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mini_chatgpt
DB_USERNAME=root
DB_PASSWORD=root

# LLM API (OpenRouter)
# Obtenir la clé: https://openrouter.ai
OPENROUTER_API_KEY=sk-or-v1-...

# Email (optionnel)
MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@example.com"
```

### Clé API OpenRouter

Les 3 modèles (GPT-4o, Gemini, Claude) fonctionnent via **OpenRouter** :

1. Créer un compte : https://openrouter.ai
2. Générer une clé API
3. Ajouter `OPENROUTER_API_KEY` au `.env`

---

## 🔐 Authentification

L'app utilise **Laravel Jetstream** avec **Fortify** :

- **Inscription/Connexion** : Email et mot de passe
- **Vérification d'email** : Obligatoire avant accès
- **Réinitialisation de mot de passe** : Lien temporaire (60 min)
- **2FA (TOTP)** : Time-based One-Time Password
- **Gestion des sessions** : Sécurisée et persistante

---

## 🎮 Utilisation

### Développement

```bash
# Terminal 1: Frontend (Vite)
npm run dev

# Terminal 2: Backend (Laravel)
php artisan serve

# Terminal 3 (optionnel): Afficher les logs
php artisan pail
```

### Production (Coolify)

```bash
# Build
npm run build

# Optimiser
php artisan optimize
php artisan config:cache

# Déployer via git push
git push origin main
```

---

## 📊 Architecture

```
mini-chatgpt/
├── app/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Conversation.php
│   │   ├── Message.php
│   │   ├── CustomInstruction.php
│   │   └── LlmModel.php
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   └── Requests/
│   └── Services/
│       └── ChatService.php
├── resources/
│   ├── js/
│   │   ├── Components/
│   │   ├── Layouts/AppLayout.vue
│   │   ├── Pages/
│   │   │   ├── Auth/
│   │   │   ├── Chat.vue
│   │   │   └── Settings.vue
│   │   └── app.js
│   ├── css/
│   │   └── app.css
│   └── lang/fr/
├── database/
│   ├── migrations/
│   └── factories/
├── routes/
│   ├── web.php
│   └── api.php
└── config/
    ├── ai_models.php
    └── jetstream.php
```

### Modèles de données

| Table | Description |
|-------|-------------|
| `users` | Utilisateurs avec authentification |
| `conversations` | Conversations de chat |
| `messages` | Messages avec FK vers LlmModel |
| `custom_instructions` | Instructions personnalisées par user |
| `llm_models` | Modèles IA disponibles |

---

## 🧪 Tests

```bash
# Lancer tous les tests
php artisan test

# Tests spécifiques
php artisan test --filter MessageController
php artisan test --filter ChatService

# Avec couverture
php artisan test --coverage
```

---

## 🎨 Thème (TailwindCSS 3)

Le système de couleurs utilise des variables CSS personnalisées :

```css
--background, --foreground, --card, --primary,
--secondary, --accent, --destructive, --border, --ring
```

**Mode sombre** : Activé via classe `.dark` sur `<html>`

Voir `resources/css/theme.css` pour les variables complètes.

---

## 🚀 Déploiement (Coolify)

### Configuration Coolify

```dockerfile
# Dockerfile minimal
FROM php:8.4-fpm
RUN docker-php-ext-install pdo_mysql
WORKDIR /app
COPY . .
RUN composer install --no-dev
RUN npm ci && npm run build
```

### Variables d'environnement Coolify

```
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql
DB_HOST=db-container
OPENROUTER_API_KEY=sk-or-v1-...
```

### Déployer

```bash
git push origin main
# Coolify rebuild automatiquement
```

---

## 🐛 Troubleshooting

| Erreur | Solution |
|--------|----------|
| "SQLSTATE[HY000]" | Vérifier la connexion MySQL dans `.env` |
| "OPENROUTER_API_KEY not found" | Ajouter la clé dans `.env` |
| "SSE timeout" | Augmenter `max_execution_time` en PHP |
| "Mode sombre ne marche pas" | Vérifier que `theme.css` est importé |

---

## 📝 Licence

MIT - Projet étudiant (Examen SGBD 2026)

---

## 👨‍💻 Auteur

**Adrien Mertens** | Étudiant - Examen SGBD 2026

**Dernière mise à jour:** 16 Juin 2026

---

## 🔗 Ressources

- [Laravel 12 Docs](https://laravel.com/docs/12.x)
- [Vue.js 3 Guide](https://vuejs.org/guide/)
- [OpenRouter API](https://openrouter.ai/docs)
- [TailwindCSS Docs](https://tailwindcss.com/docs/v3)
