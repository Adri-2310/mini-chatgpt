# Rapport Mini ChatGPT
## Projet de Développement SGBD

---

**Auteur** : Adrien Mertens  
**Date de remise** : [À remplir]  
**Deadline** : 20 juin 2026, 23h59  

---

## 1. Résumé Exécutif

[À compléter : 2-3 paragraphes présentant le projet, ses objectifs, et la thématisation choisie]

### 1.1 Thématisation de l'Application

**Thème choisi** : [À remplir : PsyBot, CoachFit, StudyBuddy, Custom, etc.]

**Description** : [À remplir : Explication du public cible, identité cohérente, ton adapté, branding]

---

## 2. Fonctionnalités Implémentées

### 2.1 Critères Obligatoires (Éliminatoires)

#### ✅ Sélecteur de Modèles
- **Status** : Implémenté
- **Description** : [À compléter avec détails]
- **Localisation** : `resources/js/Components/ModelSelector.vue`
- **Points techniques** : [À remplir]

#### ✅ Historique des Conversations
- **Status** : Implémenté
- **Description** : [À compléter : sauvegarde auto, titre généré, édition]
- **Localisation** : `database/migrations/`, `resources/js/Components/ConversationList.vue`, `app/Models/Conversation.php`
- **Points techniques** : [À remplir]

#### ✅ Streaming de Réponses (SSE)
- **Status** : Implémenté
- **Description** : [À compléter : SSE, tokens en temps réel, affichage streaming]
- **Localisation** : `app/Services/ChatService.php`, `app/Http/Controllers/MessageController.php`, `resources/js/Pages/Chat.vue`
- **Points techniques** : [À remplir]

#### ✅ Instructions Personnalisées
- **Status** : Implémenté
- **Description** : [À compléter : system prompt, configuration utilisateur]
- **Localisation** : `resources/js/Pages/Settings.vue`, `app/Http/Controllers/SettingsController.php`, `database/migrations/`
- **Points techniques** : [À remplir]

#### ✅ Composition API Vue 3 (CRITIQUE)
- **Status** : Validé 100%
- **Description** : Tous les fichiers `.vue` utilisent `<script setup>` (Composition API)
- **Justification** : [À compléter si nécessaire]
- **Fichiers** : [À lister si pertinent]

---

### 2.2 Fonctionnalités Supplémentaires (Bonus)

| Fonctionnalité | Statut | Points |
|---|---|---|
| Arbre de conversations avec fork | ❌ Non | 0 |
| Suivi tokens/coûts analytiques | ✅ Oui | +0.5 |
| Tags/Dossiers | ❌ Non | 0 |
| Partage conversations | ❌ Non | 0 |
| Upload/Analyse images | ❌ Non | 0 |
| Mode thinking | ❌ Non | 0 |
| Recherche full-text | ⚠️ Partiel | +0.25 |
| Voice I/O | ❌ Non | 0 |
| Mode clair/sombre toggle | ❌ Non | 0 |
| Multilingue | ⚠️ Partiel | +0.25 |
| Export PDF/MD/JSON | ❌ Non | 0 |

**Total bonus estimé** : +1 pt

---

## 3. Architecture Technique

### 3.1 Stack Utilisé

| Technologie | Version | Remarques |
|---|---|---|
| **Laravel** | 12.x | Framework backend principal |
| **Inertia.js** | ^2.0 | Adapter Laravel-Vue |
| **Vue.js** | ^3.3.13 | Framework frontend (Composition API) |
| **TailwindCSS** | ^3.4.0 | Styling utilitaire |
| **shadcn-vue** | ❌ ABSENT | ⚠️ À adresser |
| **PHP** | ^8.2 | Langage backend |
| **MySQL** | 8.0+ | Base de données |
| **@laravel/stream-vue** | ^0.3.13 | Streaming SSE |

### 3.2 Architecture Application

```
app/
├── Http/Controllers/
│   ├── ConversationController.php
│   ├── MessageController.php
│   ├── SettingsController.php
│   └── AskController.php
├── Models/
│   ├── User.php
│   ├── Conversation.php
│   ├── Message.php
│   ├── CustomInstruction.php
│   └── LlmModel.php
├── Services/
│   └── ChatService.php (streaming, ask)
├── Policies/
│   └── ConversationPolicy.php (autorisation ownership)
└── ...

resources/js/
├── Pages/
│   ├── Chat.vue (interface principale)
│   ├── Settings.vue (instructions perso)
│   └── Ask.vue (question simple)
├── Components/
│   ├── ConversationList.vue
│   ├── MessageList.vue
│   ├── ModelSelector.vue
│   ├── ChatHeader.vue
│   └── MessageInput.vue
└── Composables/
    └── useMarkdown.js
```

### 3.3 Base de Données

[À compléter : diagramme ER ou description des tables]

**Tables principales** :
- `users` (authentification, Jetstream)
- `conversations` (id, user_id, title, model_used, timestamps)
- `messages` (id, conversation_id, role, content, tokens_used, timestamps)
- `custom_instructions` (id, user_id, instructions, enabled)
- `llm_models` (id, name, provider, model_id, description, config)

**Contraintes d'intégrité** :
- Foreign keys avec `cascadeOnDelete()`
- `user_id` UNIQUE sur `custom_instructions`
- Indexes sur `user_id`, `conversation_id`, `provider`

---

## 4. Sécurité et Autorisation

### 4.1 Policies d'Autorisation

**ConversationPolicy** :
- `view()` : vérifier `user_id`
- `update()` : vérifier `user_id`
- `delete()` : vérifier `user_id`
- `addMessage()` : vérifier `user_id`

**Implémentation** : Utilisée dans chaque controller avec `$this->authorize()`

### 4.2 Protection CSRF

- Tous les formulaires incluent `X-CSRF-TOKEN`
- Requêtes AJAX envoient le token via header

### 4.3 Authentification

- Laravel Jetstream + Sanctum
- Email verification obligatoire
- Two-Factor Authentication optionnel

---

## 5. Historique Git

**Nombre total de commits** : 74

### 5.1 Analyse des Commits

[À compléter : résumé des phases d'implémentation]

**Phases** :
1. Initialisation (migrations, modèles) — ~10 commits
2. Architecture (controllers, services) — ~15 commits
3. Frontend (Chat.vue, composants) — ~20 commits
4. Features (streaming, titre auto, tokens) — ~15 commits
5. Polish (tests, notifications, corrections) — ~14 commits

### 5.2 Messages de Commits

[À compléter : évaluation de la qualité des messages]

- ✅ En français, cohérents
- ✅ Descriptifs et informatifs
- ✅ Progression logique visible
- ⚠️ Quelques commits trop granulaires le même jour

---

## 6. Tests

**Framework** : Laravel Feature + Unit Tests  
**Total** : ~20 tests

**Couverture** :
- [ ] Authentication flows
- [ ] ConversationController endpoints
- [ ] MessageController (non-streaming)
- [ ] ChatService (mocking API)
- [ ] Authorization policies
- [ ] Settings/Custom instructions

**Points faibles** :
- ⚠️ Pas de tests E2E (Dusk)
- ⚠️ Pas de tests streaming réel

---

## 7. Choix Techniques et Justifications

### 7.1 Streaming SSE vs WebSockets

[À compléter]

**Choix** : SSE  
**Justifications** :
- Unidirectionnel suffisant (serveur → client)
- Plus simple à implémenter que WebSockets
- Compatible avec Inertia/Laravel native (@laravel/stream-vue)
- Pas besoin de reconnexion complexe

### 7.2 ChatService avec Guzzle

[À compléter]

**Justification** : Guzzle permet le streaming réel (`stream: true`), lecture progressive du buffer SSE via `$body->read(1024)`, meilleure performance que requests bloquantes.

### 7.3 Composition API Vue 3

[À compléter]

**Avantages** :
- Meilleur tree-shaking et bundle size
- Réutilisabilité via composables
- Meilleur TypeScript support
- Logique métier groupée

---

## 8. Points à Améliorer

### 8.1 Critiques (avant la remise)

- [ ] **shadcn-vue absent** : Composant du cahier des charges
  - Décision : Justifier dans le rapport pourquoi composants Jetstream + Tailwind suffisent
  
- [ ] **Colonne orpheline `first_message`** : Jamais utilisée
  - Action : Supprimer la colonne et la migration correspondante

- [ ] **Incoherence `package.json`** : `@tailwindcss/vite: ^4` avec `tailwindcss: ^3`
  - Action : Fixer — choisir v3 ou v4 partout

- [ ] **Thématisation manquante** : Application pas spécialisée pour un public
  - Action : Ajouter branding, ton adapté, instructions pré-configurées

### 8.2 Souhaitable (pour les points bonus)

- [ ] Toggle mode clair/sombre dynamique
- [ ] Recherche full-text dans les messages (pas juste les titres)
- [ ] Arbre de conversations (fork/branches)
- [ ] Suivi tokens/coûts analytiques
- [ ] Tests E2E avec Dusk

### 8.3 Clarifications pour la Défense

- [ ] Expliquer pourquoi `first_message` orpheline et comment la supprimer
- [ ] Justifier l'absence de shadcn-vue
- [ ] Préparer réponses théoriques : ORM, migrations, WebSockets vs SSE, streaming
- [ ] Documenter choix techniques dans le rapport

---

## 9. Livrables Prévus

### 9.1 Code Source

- [x] Repository GitHub public
- [x] 74 commits avec historique propre
- [ ] Code finalisé et testé localement

**Checklist avant remise** :
- [ ] `npm run build` réussit
- [ ] Tous les tests passent
- [ ] Pas de console.error/warning
- [ ] App fonctionnelle sur `npm run dev`

### 9.2 Rapport PDF

- [ ] Template rempli (fourni par le prof)
- [ ] Schéma BD avec relations
- [ ] Architecture technique documentée
- [ ] Justifications des choix techniques
- [ ] Limitations et améliorations futures

### 9.3 Cahier Métacognitif PDF

- [ ] Processus de développement
- [ ] Utilisation de l'IA (quand, comment, pourquoi)
- [ ] Problèmes rencontrés et solutions
- [ ] Apprentissages clés

---

## 10. Signatures et Déclaration

Je certifie que :
- ✅ J'ai respecté la politique IA du cours
- ✅ Le code est mon travail ou correctement attribué
- ✅ Les tests sont fonctionnels
- ✅ La BD est bien structurée

**Signature** : _______________________  
**Date** : _______________________

---

## Annexes

### A. URLs Importantes

- **Cours** : https://cours.tsix.be/cours/projet-de-developpement-sgbd/mini-chatgpt/
- **Consignes** : `/consignes-examen.html`
- **Repository** : [Lien GitHub]
- **Deadline** : 20 juin 2026, 23h59

### B. Ressources Utilisées

[À compléter : OpenRouter, docs Laravel, docs Vue, etc.]

### C. Commandes Utiles

```bash
# Dev
npm run dev
php artisan serve

# Build
npm run build

# Tests
php artisan test

# DB
php artisan migrate:fresh --seed

# Logs IA
tail -f storage/logs/ai.log
```

---

**Fin du rapport**
