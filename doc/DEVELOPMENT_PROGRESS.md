# 📊 Suivi du Développement - Mini-ChatGPT

**Projet:** Mini-ChatGPT  
**Auteur:** Adrien Mertens  
**Deadline:** 24 juin 2026  
**Date de démarrage:** 24 avril 2026

---

## 🎯 Vue d'ensemble

| Statut | Nombre |
|--------|--------|
| ✅ Complétée | 0/10 |
| 🟡 En cours | 0/10 |
| ⏳ À faire | 10/10 |
| **Progression** | **0%** |

---

## 📋 Tâches de développement

### **Bloc 1: Frontend Setup (Pages statiques)**

#### 1️⃣ Créer la page d'accueil (Landing Page)
**Status:** ⏳ À faire  
**Priorité:** 🟡 Moyen  
**Estimation:** 1-2h  
**Date de début:** -  
**Date de fin:** -

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
**Status:** ⏳ À faire  
**Priorité:** 🟡 Moyen  
**Estimation:** 1-2h  
**Date de début:** -  
**Date de fin:** -

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
**Status:** ⏳ À faire  
**Priorité:** 🔴 Haute  
**Estimation:** 2-3h  
**Date de début:** -  
**Date de fin:** -

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
**Status:** ⏳ À faire  
**Priorité:** 🔴 Haute  
**Estimation:** 3-4h  
**Date de début:** -  
**Date de fin:** -

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
**Status:** ⏳ À faire  
**Priorité:** 🟡 Moyen  
**Estimation:** 2h  
**Date de début:** -  
**Date de fin:** -

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
**Status:** ⏳ À faire  
**Priorité:** 🔴 Haute  
**Estimation:** 2-3h  
**Date de début:** -  
**Date de fin:** -

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
**Status:** ⏳ À faire  
**Priorité:** 🟢 Basse  
**Estimation:** 30min  
**Date de début:** -  
**Date de fin:** -

**Tâches:**
- [ ] Créer `LlmModelSeeder`
  - [ ] Ajouter gpt-4o (OpenAI)
  - [ ] Ajouter gemini-2.5-flash (Google)
  - [ ] Ajouter claude-3.5-sonnet (Anthropic)
- [ ] Créer `UserSeeder` (optionnel)
  - [ ] Utilisateurs de test
- [ ] Exécuter: `php artisan db:seed`
- [ ] Vérifier données créées

**Notes:**
- Seeders pour faciliter tests
- À exécuter après migrations

---

#### 8️⃣ Configurer menu navbar avec options
**Status:** ⏳ À faire  
**Priorité:** 🟡 Moyen  
**Estimation:** 1h  
**Date de début:** -  
**Date de fin:** -

**Tâches:**
- [ ] Créer composant `Navbar.vue` ou utiliser `AppLayout`
- [ ] Logo Mini-ChatGPT
- [ ] Lien Home (landing page)
- [ ] Lien Dashboard (si logged in)
- [ ] Menu déroulant (top right)
  - [ ] 👤 Profil
  - [ ] ⚙️ Settings Instructions
  - [ ] 🎨 Thème (optionnel)
  - [ ] 🚪 Déconnexion
- [ ] Design responsive
- [ ] Utilisé dans `AppLayout.vue` (toutes les pages)

**Critères d'acceptation:**
- [ ] Navbar visible sur toutes les pages auth
- [ ] Logo cliquable = redirection home
- [ ] Menu déroulant fonctionnel
- [ ] Mobile-friendly
- [ ] Design cohérent

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
Bloc 1: Frontend Setup        [░░░░░░░░░░] 0%
Bloc 2: Exercice 1            [░░░░░░░░░░] 0%
Bloc 3: Exercice 2            [░░░░░░░░░░] 0%
Bloc 4: Exercice 3            [░░░░░░░░░░] 0%
Bloc 5: Exercice 4            [░░░░░░░░░░] 0%
Bloc 6: Data & Infrastructure [░░░░░░░░░░] 0%
Bloc 7: Testing & Final       [░░░░░░░░░░] 0%
────────────────────────────────────────────
TOTAL                         [░░░░░░░░░░] 0%
```

---

## 📝 Notes générales

### Dépendances à installer:
- [ ] `openai-php/laravel` - pour appels API
- [ ] `markdown-it-vue` - rendu Markdown
- [ ] `highlight.js` - coloration code
- [ ] `laravel/stream-vue` - streaming SSE (optionnel)

### Clés API à obtenir:
- [ ] OpenAI (GPT-4o)
- [ ] Google Gemini
- [ ] Anthropic Claude
- [ ] Configurer dans `.env`

### Architecture DB:
- Migrations: ✅ Créées
- Models: ✅ Créés avec relations
- Seeders: ⏳ À créer

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
| Frontend setup | - | - | ⏳ |
| Exercice 1 OK | - | - | ⏳ |
| Exercice 2 OK | - | - | ⏳ |
| Exercice 3 OK | - | - | ⏳ |
| Exercice 4 OK | - | - | ⏳ |
| Tests complets | - | - | ⏳ |
| Prêt soutenance | 24/06/2026 | - | ⏳ |

---

**Dernière mise à jour:** 24 avril 2026  
**Mis à jour par:** Adrien Mertens
