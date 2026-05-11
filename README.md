# 🤖 Mini-ChatGPT

Une application ChatGPT-like fonctionnelle et moderne, permettant d'interagir avec plusieurs modèles d'IA en temps réel avec historique persistant et personnalisation.

## 📋 Vue d'ensemble

**Mini-ChatGPT** est une application web complète développée pour démontrer une architecture solide et scalable. L'application offre une interface intuitive pour discuter avec plusieurs LLMs (Gemini, GPT-4o, Claude) avec streaming en temps réel, historique auto-sauvegardé et instructions personnalisées.

**Projet**: Examen SGBD - Juin 2026  
**Stack**: Laravel 12 + Vue.js 3 + Inertia.js + TailwindCSS 4

---

## 🚀 Fonctionnalités

### ✅ Authentification & Sécurité
- **🔐 Authentification sécurisée** : Jetstream Breeze avec support JWT
- **✉️ Vérification d'email obligatoire** : MustVerifyEmail pour tous les nouveaux comptes
- **🔑 Réinitialisation de mot de passe** : Lien temporaire par email (60 minutes)
- **🔓 Mot de passe oublié** : Récupération sécurisée avec email
- **🛡️ Authentification 2FA** : TOTP (Time-based One-Time Password)
- **📧 Notifications email** : Complètement traduites en français

### ✅ Fonctionnalités Principales (MVP)
- **🔄 Sélecteur de modèles** : Basculer entre Gemini, GPT-4o et Claude
- **💾 Historique persistant** : Auto-sauvegarde avec titres générés par IA
- **⚡ Streaming en temps réel** : Affichage token-par-token via WebSockets/SSE
- **⚙️ Instructions personnalisées** : Configurer le comportement de l'IA avec notifications

### 🌟 Notifications & UX
- **🔔 Notifications Toastr** : Feedback visuel professionnel sur toutes les actions
- **🌐 Localisation française** : Interface, emails et messages 100% en français
- **⚡ Notifications intelligentes** : Status de profil, mot de passe, settings

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
APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr

# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mini_chatgpt
DB_USERNAME=root
DB_PASSWORD=root

# Configuration Email (Gmail SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@gmail.com
MAIL_PASSWORD=votre-mot-de-passe-app-specifique
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=votre-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# LLM APIs
OPENAI_API_KEY=sk-...
GEMINI_API_KEY=AIza...
ANTHROPIC_API_KEY=sk-ant-...
OPENROUTER_API_KEY=sk-or-v1-...

# WebSockets (optionnel)
BROADCAST_DRIVER=null
QUEUE_CONNECTION=sync
```

### Configuration Email (Gmail)

Pour utiliser Gmail SMTP avec l'authentification 2FA :

1. Activer l'authentification 2FA sur votre compte Google
2. Générer un mot de passe d'application spécifique:
   - Aller à https://myaccount.google.com/apppasswords
   - Sélectionner "Mail" et "Windows Computer"
   - Copier le mot de passe généré
3. Mettre à jour `.env`:
   ```env
   MAIL_USERNAME=votre-email@gmail.com
   MAIL_PASSWORD=mot-de-passe-app-specifique
   ```

### Clés API LLM

Pour utiliser les modèles d'IA, vous devez obtenir les clés API:

1. **OpenAI** (GPT-4o): https://platform.openai.com/api-keys
2. **Google Gemini**: https://ai.google.dev
3. **Anthropic Claude**: https://console.anthropic.com

---

## 🔐 Système d'Authentification

### Architecture

L'application utilise **Laravel Jetstream** avec **Fortify** pour une authentification sécurisée et complète :

- **Registration** : Inscription avec email et mot de passe
- **Email Verification** : Vérification obligatoire avant accès
- **Password Reset** : Réinitialisation sécurisée par email (60 min)
- **Two-Factor Authentication** : TOTP pour protection supplémentaire
- **Session Management** : Gestion des sessions navigateur
- **Profile Management** : Modification du profil et mot de passe

### Flux d'authentification

```
1. Inscription → Email de vérification
2. Clic sur lien → Compte activé
3. Connexion → 2FA optionnel
4. Authentifié → Accès à l'app
```

### Sécurité

- ✅ Mots de passe hashés (bcrypt)
- ✅ CSRF protection
- ✅ Tokens temporaires pour réinitialisation
- ✅ Sessions sécurisées
- ✅ Authentification 2FA (TOTP)
- ✅ Notifications email configurables

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

### Tester l'authentification

```bash
# 1. Créer un compte via /register
# 2. Vérifier l'email de confirmation (affichage en log si MAIL_DRIVER=log)
# 3. Cliquer sur le lien de vérification
# 4. Se connecter et utiliser l'application

# Pour tester la réinitialisation de mot de passe:
# 1. Aller à /forgot-password
# 2. Entrer l'email
# 3. Cliquer sur le lien du mail
# 4. Définir un nouveau mot de passe
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
├── app/
│   ├── Models/
│   │   ├── User.php                    # User avec MustVerifyEmail
│   │   ├── Conversation.php
│   │   ├── Message.php
│   │   └── CustomInstruction.php
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   │   └── HandleInertiaRequests.php
│   │   └── Requests/
│   ├── Notifications/
│   │   ├── VerifyEmailNotification.php
│   │   └── ResetPasswordNotification.php
│   └── Services/
├── resources/
│   ├── js/
│   │   ├── Components/
│   │   │   ├── ToastNotification.vue   # Notifications Toastr
│   │   │   └── ...
│   │   ├── Layouts/
│   │   │   └── AppLayout.vue           # Layout principal avec notifications
│   │   ├── Pages/
│   │   │   ├── Auth/
│   │   │   │   ├── Login.vue
│   │   │   │   ├── Register.vue
│   │   │   │   ├── ForgotPassword.vue
│   │   │   │   ├── ResetPassword.vue
│   │   │   │   └── VerifyEmail.vue
│   │   │   ├── Profile/
│   │   │   ├── Settings.vue
│   │   │   └── Chat.vue
│   │   ├── plugins/
│   │   │   └── toastr.js              # Plugin Toastr
│   │   └── app.js
│   └── lang/fr/
│       ├── passwords.php               # Messages de mot de passe
│       └── jetstream.php               # Messages d'authentification
├── database/
│   ├── migrations/
│   └── factories/
├── routes/
│   ├── api.php
│   └── web.php
└── config/
    ├── app.php
    └── fortify.php
```

### Composants clés

- **ToastNotification.vue** : Affiche les notifications Toastr avec traduction FR
- **HandleInertiaRequests.php** : Partage les messages flash avec les pages Vue
- **VerifyEmailNotification.php** : Email de vérification en français
- **ResetPasswordNotification.php** : Email de réinitialisation en français

---

## 🔔 Système de Notifications

### Toastr

L'application utilise **Toastr.js** pour les notifications utilisateur :

```javascript
// Exemple: Affichage automatique après sauvegarde profil
showToast('Profil mis à jour avec succès', 'success')
```

**Caractéristiques:**
- ✅ Animations fluides (slideDown/slideUp)
- ✅ Barre de progression
- ✅ Bouton de fermeture
- ✅ Auto-fermeture après 5 secondes
- ✅ Position en haut à droite

**Pages avec notifications:**
- Authentification (Login, Register, ForgotPassword, ResetPassword)
- Profil (UpdateProfileInformation, UpdatePassword)
- Settings (CustomInstructions)
- Dashboard et Chat

### Traductions

Toutes les notifications sont **100% en français** :

| Clé | Français |
|-----|----------|
| `profile-information-updated` | Vos informations de profil ont été mises à jour avec succès. |
| `password-updated` | Votre mot de passe a été mis à jour avec succès. |
| `passwords.sent` | Lien de réinitialisation envoyé par email |
| `passwords.reset` | Mot de passe réinitialisé avec succès |

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

## 🌐 Localisation

L'application est **entièrement localisée en français** :

- Interface utilisateur (Vue.js)
- Messages d'authentification (Fortify)
- Notifications par email (VerifyEmail, ResetPassword)
- Messages système (Toastr notifications)
- Paramètres de langue: `APP_LOCALE=fr`

---

## 👨‍💻 Auteur

**Adrien Mertens**  
Étudiant - Examen SGBD 2026

**Développé avec:**
- Laravel 12
- Vue.js 3
- Inertia.js
- TailwindCSS 4
- Toastr.js

**Dernière mise à jour:** Mai 2026

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

