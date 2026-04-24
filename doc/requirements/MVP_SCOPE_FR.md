# Scope du MVP et Phases de Développement
## Projet Mini-ChatGPT

---

## Phase 1 : Fondations (Semaines 1-2)
### Objectif
Construire la base de données et l'infrastructure pour supporter l'ensemble du projet.

### Livrables
1. **Schéma Base de Données** ✓
   - Tables: utilisateurs, conversations, messages, modèles, instructions_personnalisées, paramètres_api
   - Migrations Laravel
   - Seeding pour tests

2. **Authentification** ✓
   - Setup Jetstream
   - Routes auth et middleware
   - Gestion des sessions utilisateur

3. **Setup Projet** ✓
   - Laravel 12 + Inertia + Vue.js 3
   - TailwindCSS configuré
   - WebSockets/SSE préparé (Laravel Echo ou Pusher)

### Critères d'acceptation
- [ ] `php artisan migrate` fonctionne sans erreurs
- [ ] Authentification Jetstream opérationnelle (login/register/logout)
- [ ] Layout Inertia + Vue.js 3 en place
- [ ] Connexion à au moins 1 API LLM testée

### Dépendances
- Aucune (phase initiale)

---

## Phase 2 : Gestion des Conversations (Semaines 3-4)
### Objectif
Implémenter l'historique persistant des conversations.

### Fonctionnalités
1. **CRUD Conversations** ✓
   - Créer nouvelle conversation
   - Charger une conversation existante
   - Lister toutes les conversations (barre latérale)
   - Supprimer une conversation

2. **Titres Auto-générés** ✓
   - Après le 1er message utilisateur, générer un titre via LLM
   - Sauvegarder le titre en BD
   - Mettre à jour l'affichage

3. **Persistance des Messages** ✓
   - Stocker messages utilisateur + réponses IA
   - Structure: message_utilisateur → réponse_ia
   - Timestamps et métadonnées

### Critères d'acceptation
- [ ] Page chat avec liste de conversations en barre latérale
- [ ] Créer une conversation → titre auto-généré après 1er message
- [ ] Charger une conversation antérieure restaure l'historique complet
- [ ] Suppression d'une conversation fonctionne
- [ ] Vue responsive (desktop + mobile)

### Dépendances
- Phase 1 complétée

---

## Phase 3 : Sélecteur de Modèle et Intégration LLM (Semaines 5-6)
### Objectif
Permettre à l'utilisateur de choisir entre plusieurs modèles d'IA.

### Fonctionnalités
1. **Interface Sélecteur de Modèle** ✓
   - Menu déroulant/boutons radio dans la conversation
   - Afficher les modèles disponibles
   - Sauvegarder le choix utilisateur

2. **Intégration Multi-LLM** ✓
   - Adapter pattern pour différentes APIs
   - Support: OpenAI (GPT-4o), Google (Gemini), Anthropic (Claude)
   - Gestion des clés API par modèle
   - Error handling + fallback

3. **Gestion des Appels API** ✓
   - Envoyer message au LLM sélectionné
   - Gérer les timeouts et erreurs
   - Logique de retry

### Critères d'acceptation
- [ ] Menu déroulant modèles visible et fonctionnel
- [ ] Intégration avec au moins 2 LLMs (GPT-4o + Gemini minimum)
- [ ] Les messages sont routés au bon LLM selon la sélection
- [ ] Les erreurs API sont gérées correctement
- [ ] Tests des appels API avec mocks

### Dépendances
- Phase 1 et 2 complétées

---

## Phase 4 : Affichage en Temps Réel (Semaines 7-8)
### Objectif
Afficher les réponses token par token en temps réel.

### Fonctionnalités
1. **Setup WebSocket / SSE** ✓
   - Choix: Laravel Echo + WebSockets OU Server-Sent Events
   - Communication bidirectionnelle en temps réel

2. **Implémentation du Streaming** ✓
   - Streaming API LLM (OpenAI streaming, etc.)
   - Relayer les tokens vers le frontend
   - Affichage progressif côté client

3. **États de Chargement et UX** ✓
   - Indicateur de chargement pendant la réponse
   - Typage visuel des messages (utilisateur vs IA)
   - Option pour arrêter la génération

### Critères d'acceptation
- [ ] WebSockets/SSE configuré et testé
- [ ] Les messages s'affichent token par token en direct
- [ ] Barre de chargement visible
- [ ] Interruption de génération possible
- [ ] Responsive et fluide

### Dépendances
- Phase 1, 2 et 3 complétées

---

## Phase 5 : Instructions Personnalisées (Semaines 8-9)
### Objectif
Permettre à l'utilisateur de personnaliser le comportement de l'IA.

### Fonctionnalités
1. **Interface Instructions Personnalisées** ✓
   - Formulaire pour entrer des instructions système
   - Textarea ou éditeur riche
   - Sauvegarde en BD

2. **Gestion des Instructions** ✓
   - Charger les instructions de l'utilisateur
   - Inclure les instructions dans le prompt système
   - Activer/désactiver les instructions
   - Modifier les instructions existantes

3. **Persistance et Application** ✓
   - Stocker les instructions par utilisateur
   - Appliquer à TOUS les messages de l'utilisateur
   - Option pour réinitialiser

### Critères d'acceptation
- [ ] Page de paramètres pour instructions personnalisées
- [ ] Instructions sauvegardées en BD
- [ ] Instructions appliquées aux requêtes LLM
- [ ] Affichage des instructions actuelles
- [ ] Option activer/désactiver

### Dépendances
- Phase 1, 2 et 3 complétées

---

## Phase 6 : Polissage et Tests (Semaines 9-10)
### Objectif
Finaliser, tester et préparer le rapport.

### Tâches
1. **Tests** ✓
   - Tests unitaires (Models, Services)
   - Tests fonctionnels (Conversations, Auth)
   - Optionnel: Laravel Dusk (tests browser automatisés)

2. **Polissage UI/UX** ✓
   - Audit design responsive (mobile first)
   - Accessibilité (bases WCAG)
   - Messages d'erreur clairs
   - États de chargement cohérents

3. **Documentation** ✓
   - README complet avec instructions de setup
   - Documentation API
   - Documentation schéma base de données
   - Commentaires de code pour logique complexe

4. **Rapport PDF** ✓
   - Aperçu architecture
   - Diagramme base de données (ER)
   - Captures d'écran des fonctionnalités
   - Résultats des tests
   - Difficultés et solutions

5. **Performance et Sécurité** ✓
   - Gestion des clés API (variables d'environnement)
   - Validation des entrées
   - Configuration CORS
   - Rate limiting (optionnel)

### Critères d'acceptation
- [ ] Couverture de tests > 50%
- [ ] Pas d'erreurs/avertissements console
- [ ] Responsive mobile
- [ ] Rapport PDF complet
- [ ] README GitHub complet
- [ ] .env.example fourni
- [ ] Instructions de setup claires

### Dépendances
- Toutes les phases précédentes

---

## Phase 7 (Bonus) : Fonctionnalités Avancées
### Pour atteindre le niveau Excellence

**Priorité 1** (facile + valeur):
- [ ] Mode sombre
- [ ] Recherche/filtrage conversations
- [ ] Export conversation (markdown, PDF)
- [ ] Partage de liens de conversation

**Priorité 2** (moyen):
- [ ] Upload d'images et intégration modèles de vision
- [ ] Collaboration multi-utilisateurs sur conversations
- [ ] Tagging/catégorisation conversations
- [ ] Analytics (statistiques d'utilisation)

**Priorité 3** (complexe):
- [ ] Utilisation d'outils (calculatrice, recherche web, analyse documents)
- [ ] Génération d'images (DALL-E, Stable Diffusion)
- [ ] Entrée/sortie vocale (Whisper, TTS)
- [ ] Tests automatisés Laravel Dusk

---

## Résumé Timeline

```
Sem 1-2:  Phase 1 ■■■■■
Sem 3-4:  Phase 2 ■■■■■
Sem 5-6:  Phase 3 ■■■■■
Sem 7-8:  Phase 4 ■■■■■ Phase 5 ■■■
Sem 9-10: Phase 5 ■■ Phase 6 ■■■■■■
Sem 10:   Bonus ■ (si temps disponible)

JALONS CLÉS:
✓ Sem 2: Base de données et Auth complétées
✓ Sem 4: Historique conversations opérationnel
✓ Sem 6: Sélecteur modèles + 1 LLM intégré
✓ Sem 8: Streaming + Instructions personnalisées OK
✓ Sem 10: 4 fonctionnalités + rapport prêts
```

---

## Atténuation des Risques

| Risque | Probabilité | Impact | Mitigation |
|--------|------------|--------|-----------|
| Problèmes API LLM | Moyen | Élevé | Test en début, fallbacks, APIs mockées |
| Complexité WebSocket | Moyen | Moyen | Laravel Echo bien documenté |
| Courbe apprentissage Vue.js | Faible | Moyen | Exemples Composition API + docs |
| Gestion du temps | Élevé | Élevé | Discipline stricte phase, abandon features bonus si nécessaire |
| Problèmes design BD | Faible | Moyen | Review schéma en début, design review |

---

## Checklist Go/No-Go

### Phase 1 Complétée?
- [ ] Schéma base de données créé et testé
- [ ] Auth fonctionnelle (login/logout/register)
- [ ] Boilerplate Laravel + Vue.js 3 prêt
- [ ] Au moins 1 clé API LLM configurée

### Phase 2 Complétée?
- [ ] CRUD conversations complètement fonctionnel
- [ ] Titres auto-générés opérationnels
- [ ] Messages persistés correctement
- [ ] Barre latérale affiche toutes les conversations

### Phase 3 Complétée?
- [ ] Sélecteur modèles visible et fonctionnel
- [ ] Au moins 2 LLMs intégrés
- [ ] Changement de modèle fonctionne correctement

### Phase 4 Complétée?
- [ ] WebSockets/SSE configuré
- [ ] Streaming visible dans le chat
- [ ] États de chargement opérationnels

### Phase 5 Complétée?
- [ ] Formulaire instructions personnalisées construit
- [ ] Instructions persistées
- [ ] Instructions appliquées aux prompts

### Phase 6 Prête pour soumission?
- [ ] Tests écrits (> 50% coverage)
- [ ] Pas d'erreurs console
- [ ] Responsive mobile
- [ ] Rapport PDF complet
- [ ] GitHub public et documenté
- [ ] .env.example fourni
