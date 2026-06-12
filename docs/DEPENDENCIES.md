# 📦 Documentation des Dépendances

Ce document détaille toutes les dépendances du projet SaveurIA avec leur utilité et version.

**Dernière mise à jour:** Juin 2026  
**Statut:** ✅ Toutes les dépendances sont utilisées (sauf `shadcn-vue` à supprimer)

---

## 📋 Table des matières

1. [Frontend (NPM)](#frontend-npm)
2. [Backend (Composer)](#backend-composer)
3. [Composants Vue créés](#composants-vue-créés)
4. [Traits PHP créés](#traits-php-créés)
5. [Dépendances à supprimer](#dépendances-à-supprimer)
6. [Commandes utiles](#commandes-utiles)

---

## Frontend (NPM)

### 🔧 DevDependencies (Build & Development)

| Paquet | Version | Utilité | Utilisé pour |
|--------|---------|---------|-------------|
| **@inertiajs/vue3** | ^2.0 | Bridge Laravel ↔ Vue.js | Inertia page rendering, props passing |
| **@tailwindcss/forms** | ^0.5.7 | Plugin TailwindCSS pour inputs | Styling des inputs, selects, textareas |
| **@tailwindcss/typography** | ^0.5.10 | Plugin TailwindCSS pour texte riche | Markdown rendering, rich text |
| **@tailwindcss/vite** | ^4.0.0 | TailwindCSS 4 avec Vite | CSS compilation moderne |
| **@vitejs/plugin-vue** | ^6.0.4 | Plugin Vue pour Vite | Compilation des fichiers .vue |
| **autoprefixer** | ^10.4.16 | Préfixes CSS automatiques | PostCSS - Compatibilité navigateurs |
| **concurrently** | ^9.0.1 | Exécute plusieurs npm scripts | `npm run dev` (server + vite en parallèle) |
| **laravel-vite-plugin** | ^2.0.0 | Intégration Laravel + Vite | Asset versioning, HMR setup |
| **postcss** | ^8.4.32 | Processeur CSS | TailwindCSS, autoprefixer |
| **tailwindcss** | ^3.4.0 | Framework CSS utility-first | Design system complet de l'app |
| **vite** | ^7.0.7 | Bundler & dev server | Bundling JS/CSS, hot reload |
| **vue** | ^3.3.13 | Framework Vue.js 3 | Composants reactifs (Composition API) |

---

### 💾 Dependencies (Runtime - Production)

| Paquet | Version | Utilité | Utilisé pour |
|--------|---------|---------|-------------|
| **@laravel/stream-vue** | ^0.3.13 | Streaming SSE pour Vue.js | Streaming responses chat en temps réel |
| **highlight.js** | ^11.11.1 | Syntax highlighting | Colorer les blocs de code en réponses IA |
| **markdown-it** | ^14.1.1 | Markdown parser & renderer | Afficher les réponses IA formatées |
| **toastr** | ^2.1.4 | Notifications toast | Messages success/error dans l'app |

---

## Backend (Composer)

### 🔐 Require (Production - Essential)

| Paquet | Version | Utilité | Utilisé pour |
|--------|---------|---------|-------------|
| **laravel/framework** | ^12.0 | Framework backend principal | Routing, controllers, models, migrations |
| **inertiajs/inertia-laravel** | ^2.0 | Package Inertia pour Laravel | Rendering pages Vue depuis Laravel |
| **laravel/jetstream** | ^5.5 | Auth + Profile + 2FA | Authentification complète, profil user |
| **laravel/sanctum** | ^4.0 | API tokens + Session auth | CSRF tokens, session security |
| **laravel/tinker** | ^2.10.1 | REPL interactif | Console PHP interactive (`php artisan tinker`) |
| **tightenco/ziggy** | ^2.0 | Routes frontend typées | Accès aux routes Laravel en JavaScript |

---

### 🧪 Require-Dev (Development Only)

| Paquet | Version | Utilité | Utilisé pour |
|--------|---------|---------|-------------|
| **phpunit/phpunit** | ^11.5.50 | Framework de tests | Unit & Feature tests (`php artisan test`) |
| **fakerphp/faker** | ^1.23 | Données fake | Database factories, test data generation |
| **mockery/mockery** | ^1.6 | Mocking en tests | Mock objects, spy assertions |
| **laravel/pail** | ^1.2.2 | Logs temps réel | Afficher les logs en direct (`php artisan pail`) |
| **laravel/pint** | ^1.24 | Code formatter (PSR-12) | Formater le code automatiquement |
| **nunomaduro/collision** | ^8.6 | Pretty error display | Affichage amélioré des erreurs |
| **laravel/sail** | ^1.41 | Docker containers | Environment Docker optionnel |

---

## 🎨 Composants Vue créés

| Composant | Purpose | Fonctionnalité |
|-----------|---------|-----------------|
| **ConversationStats.vue** | Affichage des statistiques | Total messages, tokens, coûts USD par conversation |
| **SearchMessages.vue** | Recherche full-text | Chercher messages dans une conversation |
| **ExportButtons.vue** | Exports | Télécharger conversation en MD ou JSON |

---

## 🔧 Traits PHP créés

| Trait | Location | Utilité |
|-------|----------|---------|
| **CalculateCosts** | `app/Traits/CalculateCosts.php` | Calculer coûts basés sur tokens + tarifs par modèle |

**Méthode principale:**
- `calculateCostByTokens(model, tokensUsed)` - Calcule coût USD
- `getConversationStats(conversation)` - Retourne stats complètes (messages, tokens, coûts)

---

## ❌ Dépendances à supprimer

### 🗑️ shadcn-vue (^2.7.4)

**Status:** Dead dependency - **À SUPPRIMER**

**Raison:** 
- Aucun import dans le code (`shadcn-vue` n'est jamais utilisé)
- Le projet utilise **Tweakcn** (système CSS variables custom) + TailwindCSS à la place
- C'est une relique d'une version précédente

**Action:**
```bash
npm uninstall shadcn-vue
git add package.json package-lock.json
git commit -m "Supprimer dépendance shadcn-vue non-utilisée"
```

---

## 📊 Statistiques des dépendances

### NPM

```
Total devDependencies: 12
├── ✅ Utilisées: 11
└── ❌ Dead: 1 (shadcn-vue)

Total dependencies: 4
└── ✅ Utilisées: 4
```

### Composer

```
Total require: 6
└── ✅ Utilisées: 6

Total require-dev: 7
└── ✅ Utilisées: 7
```

---

## 🔄 Commandes utiles

### Installation

```bash
# Installer toutes les dépendances
npm install
composer install

# Installer une seule dépendance NPM
npm install package-name --save

# Installer une seule dépendance Composer
composer require vendor/package
```

### Développement

```bash
# Lancer dev server + Vite
npm run dev

# Ou avec tous les services (Laravel)
composer run dev

# Formater le code
composer run pint

# Voir les logs en temps réel
php artisan pail
```

### Tests

```bash
# Lancer les tests
php artisan test

# Tests spécifiques
php artisan test --filter MessageController

# Avec couverture
php artisan test --coverage
```

### Maintenance

```bash
# Mettre à jour les dépendances
npm update
composer update

# Vérifier les dépendances outdated
npm outdated
composer outdated

# Audit de sécurité
npm audit
composer audit
```

---

## 🎯 Résumé par catégorie

### ⚡ Streaming & Real-time
- `@laravel/stream-vue` - SSE streaming chat

### 🎨 Styling & Design
- `tailwindcss` - Framework CSS
- `@tailwindcss/forms` - Styling inputs
- `@tailwindcss/typography` - Texte riche
- `autoprefixer` - CSS compatibility

### 📝 Content Rendering
- `markdown-it` - Markdown parsing
- `highlight.js` - Code highlighting

### 🔔 UX/Notifications
- `toastr` - Toast notifications

### 🏗️ Build & Bundling
- `vite` - Bundler moderne
- `@vitejs/plugin-vue` - Vue support
- `laravel-vite-plugin` - Laravel integration
- `postcss` - CSS processing

### 🔗 Framework Integration
- `@inertiajs/vue3` - Laravel ↔ Vue bridge
- `inertiajs/inertia-laravel` - Backend package
- `tightenco/ziggy` - Frontend routes
- `laravel/sanctum` - Auth & CSRF

### 🔐 Authentication
- `laravel/jetstream` - Auth complete system

### 🧪 Testing & Quality
- `phpunit/phpunit` - Tests
- `fakerphp/faker` - Fake data
- `mockery/mockery` - Mocking
- `laravel/pint` - Code formatting
- `nunomaduro/collision` - Error display

### 📊 Development Tools
- `laravel/pail` - Logs viewing
- `laravel/tinker` - REPL
- `concurrently` - Run multiple scripts
- `laravel/sail` - Docker (optional)

---

## 📚 Ressources

- [NPM Registry](https://www.npmjs.com/)
- [Composer Repository](https://packagist.org/)
- [Laravel Packages](https://laravel.com/docs/packages)
- [Vue.js Ecosystem](https://vuejs.org/guide/extras/ways-of-using-vue.html)
- [TailwindCSS Docs](https://tailwindcss.com/docs)

---

**Statut de maintenance:** ✅ Toutes les dépendances à jour (Juin 2026)
