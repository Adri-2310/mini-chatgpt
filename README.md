# 🤖 Mini-ChatGPT

Une application ChatGPT-like fonctionnelle et moderne, permettant d'interagir avec plusieurs modèles d'IA en temps réel avec historique persistant et personnalisation.

## 📋 Vue d'ensemble

**Mini-ChatGPT** est une application web complète développée pour démontrer une architecture solide et scalable. L'application offre une interface intuitive pour discuter avec plusieurs LLMs (Gemini, GPT-4o, Claude) avec streaming en temps réel, historique auto-sauvegardé et instructions personnalisées.

**Projet**: Examen SGBD - Juin 2026  
**Stack**: Laravel 12 + Vue.js 3 + Inertia.js + TailwindCSS 4

---

## 🚀 Fonctionnalités

### ✅ Obligatoires (MVP)
- **🔄 Sélecteur de modèles** : Basculer entre Gemini, GPT-4o et Claude
- **💾 Historique persistant** : Auto-sauvegarde avec titres générés par IA
- **⚡ Streaming en temps réel** : Affichage token-par-token via WebSockets
- **⚙️ Instructions personnalisées** : Configurer le comportement de l'IA

### 🌟 Optionnels (Excellence)
- Mode sombre
- Recherche et filtrage de conversations
- Export en Markdown/PDF
- Partage de liens
- Upload d'images + modèles de vision

---

## 🛠️ Stack Technologique

| Composant | Version | Rôle |
|-----------|---------|------|
| **Backend** | Laravel 12 | API, authentification, gestion données |
| **Frontend** | Vue.js 3 | Interface réactive (Composition API) |
| **Bridge** | Inertia.js | Liaison Laravel ↔ Vue.js |
| **Styling** | TailwindCSS 4 | Design système + responsive |
| **Temps réel** | WebSockets/SSE | Streaming des réponses IA |
| **BD** | PostgreSQL/MySQL | Persistance des données |
| **Auth** | Jetstream Breeze | Authentification sécurisée |

---

## 📦 Installation

### Prérequis
- PHP 8.5+
- Composer 2.9+
- Node.js 24+
- NPM 11+
- PostgreSQL ou MySQL

### Étapes

```bash
# 1. Cloner le repository
git clone https://github.com/ton-username/mini-chatgpt.git
cd mini-chatgpt

# 2. Installer les dépendances PHP
composer install

# 3. Installer les dépendances Node
npm install

# 4. Copier les variables d'environnement
cp .env.example .env

# 5. Générer la clé APP
php artisan key:generate

# 6. Configurer la base de données dans .env
# Exemple pour PostgreSQL:
# DB_CONNECTION=pgsql
# DB_HOST=localhost
# DB_PORT=5432
# DB_DATABASE=mini_chatgpt
# DB_USERNAME=postgres
# DB_PASSWORD=secret

# 7. Créer les tables
php artisan migrate

# 8. Compiler les assets (développement)
npm run dev

# 9. Démarrer le serveur local
php artisan serve
```

L'app sera accessible à: **http://localhost:8000**

---

## ⚙️ Configuration

### Variables d'environnement essentielles (`.env`)

```env
# Application
APP_NAME="Mini-ChatGPT"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de données
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=mini_chatgpt
DB_USERNAME=postgres
DB_PASSWORD=secret

# LLM APIs
OPENAI_API_KEY=sk-...
GEMINI_API_KEY=AIza...
ANTHROPIC_API_KEY=sk-ant-...

# WebSockets (optionnel)
BROADCAST_DRIVER=null
QUEUE_CONNECTION=sync
```

### Clés API LLM

Pour utiliser les modèles d'IA, vous devez obtenir les clés API:

1. **OpenAI** (GPT-4o): https://platform.openai.com/api-keys
2. **Google Gemini**: https://ai.google.dev
3. **Anthropic Claude**: https://console.anthropic.com

---

## 🎮 Utilisation

### Développement

```bash
# Terminal 1: Serveur PHP
php artisan serve

# Terminal 2: Compilation frontend
npm run dev

# Terminal 3 (optionnel): Mode Reverb WebSockets
php artisan reverb:start
```

### Production

```bash
# Build des assets
npm run build

# Optimiser l'app
php artisan optimize
php artisan config:cache
php artisan view:cache

# Déployer sur Render/Railway
git push origin main
```

---

## 📁 Architecture

```
mini-chatgpt/
├── app/                 # Code métier Laravel
│   ├── Models/         # Models (User, Conversation, Message)
│   ├── Http/           # Controllers, Requests, Resources
│   └── Services/       # Services LLM (OpenAI, Gemini, Claude)
├── resources/js/       # Code Vue.js 3
│   ├── Components/     # Composants réutilisables
│   ├── Layouts/        # Layouts (App, Guest)
│   └── Pages/          # Pages (Chat, Settings)
├── database/
│   ├── migrations/     # Migrations BD
│   └── seeders/        # Données de test
├── config/             # Configuration Laravel
├── routes/             # Routes API et web
└── public/             # Assets compilés (CSS, JS)
```

---

## 🧪 Tests

```bash
# Tests unitaires
php artisan test

# Tests avec coverage
php artisan test --coverage

# Tests E2E (optionnel - Dusk)
php artisan dusk
```

---

## 🚀 Déploiement

### Render.com

1. Créer un compte sur https://render.com
2. Connecter le repository GitHub
3. Configurer les variables d'environnement
4. Déployer automatiquement à chaque `git push main`

Docs: https://render.com/docs/deploy-laravel

### Railway.app

1. Créer un compte sur https://railway.app
2. Importer le projet depuis GitHub
3. Ajouter PostgreSQL comme BD
4. Configurer et déployer

---

## 📊 Schéma Base de Données

```sql
-- Tables principales
users                    -- Utilisateurs authentifiés
conversations           -- Conversations de l'utilisateur
messages                -- Messages (utilisateur + IA)
custom_instructions     -- Instructions personnalisées
llm_models             -- Modèles disponibles
api_credentials        -- Clés API par modèle
```

---

## 🐛 Troubleshooting

### Erreur: "Class 'finfo' not found"
→ Activer l'extension `fileinfo` dans `php.ini`: décommenter `;extension=fileinfo`

### Erreur: "CORS error"
→ Vérifier `config/cors.php` et ajouter le domaine frontend

### WebSocket ne fonctionne pas
→ Utiliser SSE à la place: `BROADCAST_DRIVER=null`

---

## 📝 Licence

MIT - Projet étudiant

---

## 👨‍💻 Auteur

**Adrien Mertens**  
Étudiant - Examen SGBD 2026

---

## 📞 Support

Pour les issues et questions, consultez:
- 📖 Documentation: `/docs`
- 🐛 Issues: GitHub Issues
- 💬 Discussions: GitHub Discussions

---

## 🔗 Ressources

- [Laravel 12 Docs](https://laravel.com/docs/12.x)
- [Vue.js 3 Guide](https://vuejs.org/guide/)
- [Inertia.js Docs](https://inertiajs.com)
- [TailwindCSS Docs](https://tailwindcss.com/docs)

