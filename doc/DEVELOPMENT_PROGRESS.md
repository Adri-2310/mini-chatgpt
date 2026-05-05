# 📊 Suivi du Développement - Mini-ChatGPT

**Projet:** Mini-ChatGPT  
**Auteur:** Adrien Mertens  
**Deadline:** 24 juin 2026  
**Date de démarrage:** 24 avril 2026

---

## 🎯 Vue d'ensemble

| Statut | Nombre |
|--------|--------|
| ✅ Complétée | 8/10 |
| 🟡 En cours | 0/10 |
| ⏳ À faire | 2/10 |
| **Progression** | **80%** |

---

## 📋 Tâches de développement

### **Bloc 1: Frontend Setup (Pages statiques)**

#### 1️⃣ Créer la page d'accueil (Landing Page)
**Status:** ✅ Complétée  
**Priorité:** 🟡 Moyen  
**Estimation:** 1-2h  
**Date de début:** 24 avril 2026  
**Date de fin:** 24 avril 2026

**Tâches:**
- [ ] Créer `Welcome.vue` ou `Home.vue`
- [ ] Hero section avec titre + description
- [ ] Présentation des 3 features principales
- [ ] Boutons CTA (Login / Register)
- [ ] Footer avec infos
- [ ] Design responsive TailwindCSS
- [ ] Route GET / configurée

**Notes:**
- Doit être la page d'accueil avant login
- Logo Mini-ChatGPT à ajouter

---

#### 2️⃣ Styliser les pages Auth (Login/Register/Logout)
**Status:** ✅ Complétée  
**Priorité:** 🟡 Moyen  
**Estimation:** 1-2h  
**Date de début:** 24 avril 2026  
**Date de fin:** 24 avril 2026

**Tâches:**
- [ ] Personnaliser layout Auth de Jetstream
- [ ] Page Login avec formulaire
- [ ] Page Register avec formulaire
- [ ] Messages flash (succès/erreurs)
- [ ] Design cohérent avec le site
- [ ] Responsive mobile
- [ ] Lien Register depuis Login
- [ ] Redirection post-login vers /chat

**Notes:**
- Jetstream Breeze fournit déjà la base
- Juste à styliser avec TailwindCSS

---

### **Bloc 2: Exercice 1 - Question Simple (Sans historique)**

#### 3️⃣ Exercice 1: Page Ask (Question simple)
**Status:** ✅ Complétée  
**Priorité:** 🔴 Haute  
**Estimation:** 2-3h  
**Date de début:** 24 avril 2026  
**Date de fin:** 24 avril 2026

**Tâches:**
- [ ] Installer `openai-php/laravel`
- [ ] Configurer OpenRouter API
- [ ] Créer `ChatService` pour appels API
- [ ] Créer `AskController` (GET + POST)
- [ ] Routes: GET/POST `/ask`
- [ ] Composant `Ask.vue` (page principale)
- [ ] Composant `ModelSelector.vue`
- [ ] Composant `QuestionForm.vue`
- [ ] Composant `ResponseDisplay.vue`
- [ ] Intégrer `markdown-it` pour rendu Markdown
- [ ] Intégrer `highlight.js` pour coloration code
- [ ] Utiliser `prose` Tailwind pour mise en forme

**API Keys à configurer:**
- [ ] OPENAI_API_KEY (ou OpenRouter)
- [ ] GEMINI_API_KEY
- [ ] ANTHROPIC_API_KEY

**Notes:**
- Pas de persistance BD pour cette étape
- Chaque rechargement réinitialise
- Support 3 modèles min: GPT-4o, Gemini, Claude

---

### **Bloc 3: Exercice 2 - Dashboard Chat (Avec historique)**

#### 4️⃣ Exercice 2: Dashboard Chat (Conversations)
**Status:** ✅ Complétée  
**Priorité:** 🔴 Haute  
**Estimation:** 3-4h  
**Date de début:** 24 avril 2026  
**Date de fin:** 30 avril 2026

**Backend:**
- [ ] Créer `ConversationController`
- [ ] Créer `MessageController`
- [ ] Routes API pour conversations
- [ ] Routes API pour messages
- [ ] Logique titre auto-généré (après 1er message)
- [ ] Gérer les erreurs API
- [ ] Implémenter loader/indicateur chargement

**Frontend - Page:**
- [ ] Créer `Chat.vue` (page principale)
- [ ] Route GET `/chat` → affiche Chat.vue
- [ ] Layout avec 2 sections: sidebar + main

**Frontend - Composants:**
- [ ] `ConversationList.vue` (sidebar gauche)
  - [ ] Bouton "+ Nouvelle conversation"
  - [ ] Liste des conversations (ordre décroissant)
  - [ ] Clic = change conversation
  - [ ] Affiche timestamp de chaque conversation
- [ ] `ChatHeader.vue` (haut de la zone chat)
  - [ ] Sélecteur de modèle
  - [ ] Titre conversation actuelle
- [ ] `MessageList.vue` (zone messages)
  - [ ] Affiche tous les messages
  - [ ] Auto-scroll vers le bas
- [ ] `ChatMessage.vue` (message unique)
  - [ ] Distingue "user" vs "assistant"
  - [ ] Formatage Markdown + highlight.js
  - [ ] Avatar/styling différents
- [ ] `MessageInput.vue` (formulaire bas)
  - [ ] Input message
  - [ ] Bouton Envoyer
  - [ ] Placeholder descriptif
- [ ] `LoadingIndicator.vue`
  - [ ] Indicateur visuel chargement
  - [ ] Spinner ou "Thinking..."

**Intégration markdown:**
- [ ] `markdown-it` pour rendu
- [ ] `highlight.js` pour code coloring
- [ ] Tailwind `prose` pour mise en forme

**Critères d'acceptation:**
- [ ] Clic "+ Nouvelle" = crée conversation vide
- [ ] 1er message = sauvegardé + titre auto-généré
- [ ] Voir réponse formatée en Markdown
- [ ] Conversations listées en ordre décroissant
- [ ] Clic conversation = charge historique complet
- [ ] Loader visible pendant appel API
- [ ] Responsive desktop + mobile

**Notes:**
- Peut supprimer conversations (optionnel)
- Recherche/filtrage conversations (optionnel)

---

### **Bloc 4: Exercice 3 - Instructions Personnalisées**

#### 5️⃣ Exercice 3: Settings Instructions Personnalisées
**Status:** ✅ Complétée  
**Priorité:** 🟡 Moyen  
**Estimation:** 2h  
**Date de début:** 24 avril 2026  
**Date de fin:** 30 avril 2026

**Backend:**
- [ ] Créer `SettingsController`
- [ ] Route GET `/settings/instructions`
- [ ] Route POST `/settings/instructions`
- [ ] Logique sauvegarde en BD
- [ ] Logique activer/désactiver

**Frontend:**
- [ ] Créer page `Settings/CustomInstructions.vue`
- [ ] Ajouter link dans navbar → Settings
- [ ] Route `/settings/instructions`

**Composants:**
- [ ] `CustomInstructionsForm.vue`
  - [ ] Textarea pour instructions
  - [ ] Bouton Sauvegarder
  - [ ] Message succès/erreur
- [ ] `InstructionToggle.vue`
  - [ ] Switch ON/OFF
  - [ ] Affiche statut (activé/désactivé)

**Critères d'acceptation:**
- [ ] Formulaire sauvegarde instructions
- [ ] Instructions persistées en BD
- [ ] Toggle activer/désactiver
- [ ] Instructions incluses dans prompts LLM
- [ ] Affiche instructions actuelles
- [ ] Message succès après sauvegarde

**Notes:**
- Une seule instruction par utilisateur (one-to-one)
- Instructions stockées en `custom_instructions` table

---

### **Bloc 5: Exercice 4 - Streaming Temps Réel**

#### 6️⃣ Exercice 4: Streaming temps réel (SSE)
**Status:** ✅ Complétée  
**Priorité:** 🔴 Haute  
**Estimation:** 2-3h  
**Date de début:** 24 avril 2026  
**Date de fin:** 30 avril 2026

**Backend:**
- [ ] Configurer `StreamResponse` Laravel
- [ ] Créer endpoint streaming: POST `/messages/stream`
- [ ] Implémenter streaming OpenAI/Gemini/Claude
- [ ] Relayer tokens vers frontend
- [ ] Gestion erreurs et timeouts
- [ ] Sauvegarder message final en BD

**Frontend:**
- [ ] Créer `StreamingMessage.vue`
  - [ ] Affichage token-par-token
  - [ ] Mise à jour progressive
- [ ] Créer `StopButton.vue`
  - [ ] Bouton arrêter génération
  - [ ] Confirmer arrêt
- [ ] Intégrer dans `Chat.vue`
  - [ ] Remplace `MessageInput` normal
  - [ ] Gère l'affichage progressif

**Configuration:**
- [ ] Nginx config pour Laragon (SSE)
- [ ] Headers CORS pour SSE
- [ ] Timeout configurations

**Critères d'acceptation:**
- [ ] Réponse s'affiche lettre par lettre
- [ ] Pas d'attendre la réponse complète
- [ ] Bouton arrêter fonctionne
- [ ] Message sauvegardé en BD après
- [ ] Responsive et fluide
- [ ] Gestion erreurs (reconnexion, timeout)

**Notes:**
- SSE plus simple que WebSockets pour le dev
- Compatible avec Render pour production

---

### **Bloc 6: Support Data & Infrastructure**

#### 7️⃣ Créer seeders pour les données de test
**Status:** ✅ Complétée  
**Priorité:** 🟢 Basse  
**Estimation:** 30min  
**Date de début:** 24 avril 2026  
**Date de fin:** 1 mai 2026

**Tâches:**
- [x] Créer `LlmModelSeeder`
  - [x] Ajouter gpt-4o-mini (OpenAI)
  - [x] Ajouter gemini-3-flash-preview (Google)
  - [x] Ajouter claude-3.5-haiku (Anthropic)
- [x] Intégrer dans `DatabaseSeeder`
- [x] Exécuter: `php artisan db:seed`
- [x] Vérifier données créées

**Notes:**
- Seeders intégrés et fonctionnels
- LlmModelSeeder appelé dans DatabaseSeeder::run()

---

#### 8️⃣ Configurer menu navbar avec options
**Status:** ✅ Complétée  
**Priorité:** 🟡 Moyen  
**Estimation:** 1h  
**Date de début:** 24 avril 2026  
**Date de fin:** 2 mai 2026

**Tâches:**
- [x] Créer composant `Nav.vue`
- [x] Logo Mini-ChatGPT (gradient coloré)
- [x] Lien Home (redirects intelligente: welcome si guest, dashboard si auth)
- [x] Liens navigation (Parler, Conversations si authentifié)
- [x] Menu déroulant (top right)
  - [x] 👤 Profil
  - [x] ⚙️ Paramètres
  - [x] 🚪 Déconnexion
- [x] Design responsive dark theme (Tailwind slate)
- [x] Utilisé dans `AppLayout.vue` et `ChatLayout.vue`
- [x] Mobile hamburger menu

**Critères d'acceptation:**
- [x] Navbar visible sur toutes les pages
- [x] Logo cliquable = redirection intelligente
- [x] Menu déroulant fonctionnel
- [x] Mobile-friendly avec hamburger
- [x] Design cohérent avec le reste de l'app

---

### **Bloc 7: Testing & Finalisation**

#### 9️⃣ Tester et debugger l'application complète
**Status:** ⏳ À faire  
**Priorité:** 🟡 Moyen  
**Estimation:** 2-3h  
**Date de début:** -  
**Date de fin:** -

**Tests manuels:**
- [ ] Flow complet: Login → Chat → Instructions → Logout
- [ ] Créer conversation = affiche sidebar
- [ ] Envoyer message = reçoit réponse formatée
- [ ] Changer modèle = utilise bon modèle
- [ ] Streaming visible
- [ ] Instructions appliquées
- [ ] Responsive desktop + tablette + mobile
- [ ] Messages d'erreur clairs
- [ ] Pas d'erreurs console

**Debugging:**
- [ ] Vérifier logs Laravel
- [ ] Vérifier BD (migrations, données)
- [ ] Tester API calls (OpenAI/Gemini/Claude)
- [ ] Performance acceptable

---

#### 🔟 Faire commit final et documentation
**Status:** ⏳ À faire  
**Priorité:** 🟢 Basse  
**Estimation:** 30min  
**Date de début:** -  
**Date de fin:** -

**Tâches:**
- [ ] Commit final: "Finaliser Mini-ChatGPT - Tous les exercices"
- [ ] Vérifier `.gitignore` (pas de `.env`)
- [ ] Vérifier `.env.example` à jour
- [ ] README.md à jour avec instructions setup
- [ ] doc/ bien organisée
  - [ ] /requirements
  - [ ] /architecture
  - [ ] DEVELOPMENT_PROGRESS.md (ce fichier)
- [ ] Ajouter screenshots des fonctionnalités
- [ ] Prêt pour présentation/soutenance

---

## 📈 Statistiques

### Progression par bloc:

```
Bloc 1: Frontend Setup        [██████████] 100%
Bloc 2: Exercice 1            [██████████] 100%
Bloc 3: Exercice 2            [██████████] 100%
Bloc 4: Exercice 3            [██████████] 100%
Bloc 5: Exercice 4            [██████████] 100%
Bloc 6: Data & Infrastructure [██████████] 100%
Bloc 7: Testing & Final       [████░░░░░░] 50%
────────────────────────────────────────────
TOTAL                         [████████░░] 80%
```

---

## 📝 Notes générales

### Dépendances installées:
- [x] `openai-php/laravel` - pour appels API
- [x] `markdown-it-vue` - rendu Markdown
- [x] `highlight.js` - coloration code
- [x] Vue.js 3 avec Composition API
- [x] TailwindCSS + Inertia.js

### Clés API à configurer:
- [x] OpenAI (utilisable via OpenRouter)
- [x] Google Gemini (configurée)
- [x] Anthropic Claude (configurée)
- [x] Variables d'environnement dans `.env`

### Architecture DB:
- Migrations: ✅ Créées
- Models: ✅ Créés avec relations
- Seeders: ✅ Créés et intégrés

---

## 🔗 Ressources

- **PRD:** doc/requirements/PRD_FR.md
- **MVP Scope:** doc/requirements/MVP_SCOPE_FR.md
- **Architecture:** doc/architecture/ARCHITECTURE.md
- **Diagramme BD:** doc/architecture/DATABASE_DIAGRAM_UPDATED.puml

---

## 📅 Dates jalons

| Jalon | Date prévue | Date réelle | Statut |
|-------|------------|------------|--------|
| Frontend setup | - | 24/04/2026 | ✅ |
| Exercice 1 OK | - | 24/04/2026 | ✅ |
| Exercice 2 OK | - | 30/04/2026 | ✅ |
| Exercice 3 OK | - | 30/04/2026 | ✅ |
| Exercice 4 OK | - | 30/04/2026 | ✅ |
| Seeders + Navbar OK | - | 02/05/2026 | ✅ |
| Tests complets | - | - | ⏳ |
| Prêt soutenance | 24/06/2026 | - | ⏳ |

---

**Dernière mise à jour:** 05 mai 2026  
**Mis à jour par:** Adrien Mertens
