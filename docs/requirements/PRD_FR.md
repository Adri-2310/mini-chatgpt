# Document de Spécifications Produit (PRD)
## Mini-ChatGPT - Examen Projet SGBD

**Auteur**: Étudiant  
**Date**: Avril 2026  
**Deadline**: 24 juin 2025, 23h59  
**Soutenance orale**: 26 juin 2025

---

## 1. Vue d'ensemble du projet

### Objectif
Développer une application ChatGPT-like complète et fonctionnelle qui respecte TOUTES les exigences listées. L'application doit fournir une interface utilisateur professionnelle pour interagir avec plusieurs modèles d'IA (LLM) en temps réel.

### Valeur proposée
- Interface intuitive et réactive pour discuter avec plusieurs LLMs
- Historique des conversations persistant et auto-organisé
- Personnalisation du comportement de l'IA via instructions personnalisées
- Expérience utilisateur fluide avec affichage en temps réel (streaming)

---

## 2. Stack technologique obligatoire

| Composant | Version | Rôle |
|-----------|---------|------|
| **Backend** | Laravel 12 + Jetstream | API, authentification, gestion données |
| **Frontend** | Vue.js 3 (Composition API) | Interface utilisateur réactive, gestion d'état |
| **Bridge** | Inertia.js | Liaison Laravel ↔ Vue.js |
| **Styling** | TailwindCSS 3 | Système de design et design responsive |
| **Temps réel** | WebSockets / SSE | Réponses en temps réel (streaming) |

⚠️ **Critique**: Vue.js Composition API est obligatoire. L'utilisation de l'Options API entraîne l'échec automatique.

---

## 3. Fonctionnalités obligatoires (Critères d'élimination)

### 3.1 Sélecteur de modèles
**Description**: Capacité à basculer entre plusieurs LLMs dans l'interface.

**Modèles supportés**:
- Gemini 2.5 Flash (Google)
- GPT-4o (OpenAI)
- Claude (Anthropic)
- Optionnel: Autres modèles disponibles

**Critères d'acceptation**:
- [ ] Menu déroulant/sélecteur pour choisir le modèle
- [ ] Sauvegarde du choix utilisateur
- [ ] Intégration API fonctionnelle avec au moins 2 modèles
- [ ] Gestion des erreurs API

### 3.2 Historique des conversations (Auto-sauvegardé)
**Description**: Persistance automatique des conversations avec titres générés par IA.

**Critères d'acceptation**:
- [ ] Chaque conversation est sauvegardée en base de données après le 1er message
- [ ] Titre auto-généré par LLM basé sur les premiers messages
- [ ] Liste des conversations visible dans la barre latérale
- [ ] Chargement d'une conversation antérieure restaure l'historique complet
- [ ] Suppression de conversation possible
- [ ] Pagination/recherche si nombreuses conversations

### 3.3 Affichage en temps réel (Streaming token par token)
**Description**: Les réponses de l'IA s'affichent progressivement, token par token.

**Critères d'acceptation**:
- [ ] Utilisation de WebSockets OU Server-Sent Events (SSE)
- [ ] Affichage progressif des tokens côté client
- [ ] Barre de chargement/indication visuelle du chargement
- [ ] Gestion des interruptions utilisateur (arrêt de génération)

### 3.4 Instructions personnalisées
**Description**: Interface pour configurer le comportement et contexte de l'IA.

**Critères d'acceptation**:
- [ ] Formulaire permettant de définir des instructions système
- [ ] Instructions sauvegardées par utilisateur
- [ ] Instructions incluses dans les prompts envoyés à l'IA
- [ ] Option pour activer/désactiver les instructions
- [ ] Export/Import possible

---

## 4. Fonctionnalités optionnelles (Niveau Excellence)

| Fonctionnalité | Complexité | Points bonus |
|---------|-----------|--------------|
| Outils LLM (calculatrice, recherche web, analyse de documents) | Élevée | ⭐⭐⭐ |
| Upload d'images et modèles de vision | Moyenne | ⭐⭐ |
| Multi-utilisateurs (authentification complète) | Moyenne | ⭐⭐ |
| Génération d'images (DALL-E, Stable Diffusion) | Élevée | ⭐⭐⭐ |
| Synthèse vocale et reconnaissance vocale | Élevée | ⭐⭐⭐ |
| Tests automatisés Laravel Dusk | Moyenne | ⭐⭐ |

---

## 5. Contraintes techniques

### Performance
- Temps de réponse < 200ms pour les requêtes sans streaming
- Interface réactive et fluide (60fps si possible)
- Support du mode hors ligne pour historique local

### Sécurité
- Authentification JWT ou sessions Laravel sécurisées
- Stockage sécurisé des clés API (variables d'environnement)
- Validation des entrées côté serveur
- CORS correctement configuré

### Compatibilité
- Support des navigateurs modernes (Chrome, Firefox, Safari, Edge - 2 dernières versions)
- Design responsive (mobile, tablette, ordinateur)

### Scalabilité
- Structure modulaire pour ajouter facilement de nouveaux LLMs
- Système de journalisation et monitoring basique

---

## 6. Architecture globale

```
┌─────────────────────────────────────────────────────────┐
│                    Frontend (Vue.js 3)                  │
│  - Interface de chat                                     │
│  - Sélecteur de modèle                                   │
│  - Barre latérale historique conversations              │
│  - Paramètres instructions personnalisées                │
└────────────────┬────────────────────────────────────────┘
                 │ (Inertia.js + WebSockets/SSE)
┌────────────────▼────────────────────────────────────────┐
│                  Backend (Laravel 12)                    │
│  - Authentification (Jetstream)                          │
│  - Routes API                                            │
│  - Couche d'intégration LLM                              │
│  - Gestion de la base de données                         │
│  - Broadcasting temps réel (WebSockets)                  │
└────────────────┬────────────────────────────────────────┘
                 │
┌────────────────▼────────────────────────────────────────┐
│              Base de données (PostgreSQL/MySQL)          │
│  - Utilisateurs, Conversations, Messages                │
│  - Instructions personnalisées, Modèles, Paramètres      │
└─────────────────────────────────────────────────────────┘
                 │
┌────────────────▼────────────────────────────────────────┐
│          APIs externes LLM                               │
│  - OpenAI (GPT-4o), Google (Gemini), Anthropic (Claude) │
└─────────────────────────────────────────────────────────┘
```

---

## 7. Critères de succès

### Phase 1 - MVP (Obligatoire)
- [x] 4 fonctionnalités obligatoires complètement opérationnelles
- [x] Code propre et maintenable (best practices Laravel + Vue)
- [x] Schéma de base de données normalisé
- [x] Rapport PDF avec diagramme ER et captures d'écran

### Phase 2 - Excellence
- [x] Au moins 2 fonctionnalités optionnelles implémentées
- [x] Tests automatisés (Dusk)
- [x] Documentation API complète
- [x] Présentation orale convaincante

---

## 8. Livrables

### Code
- **Repository**: GitHub public avec README complet
- **Structure**: PSR-12 (PHP), standards Vue.js, structure Laravel standard
- **Documentation**: Commentaires de code uniquement pour logique complexe

### Rapport
**Format**: PDF professionnel, 15-20 pages
**Contenu**:
1. Introduction et contexte
2. Architecture (diagramme ER complet, design logiciel)
3. Fonctionnalités implémentées (captures d'écran + explications)
4. Tests et validation
5. Difficultés rencontrées et solutions apportées
6. Utilisation d'outils IA (ChatGPT, Claude, etc.)
7. Conclusion et améliorations futures

### Environnement de développement
- `.env.example` avec variables API
- `docker-compose.yml` ou instructions de setup simple
- Données de test pour tests

---

## 9. Planification (Phases prévisionnelles)

| Semaine | Tâches | Jalons |
|---------|--------|--------|
| 1-2 | Architecture BD, setup Laravel/Vue | Schéma BD validé |
| 3-4 | Auth + CRUD conversations + historique | Historique fonctionnel |
| 5-6 | Sélecteur modèle + intégration 1 LLM | 1 LLM opérationnel |
| 7-8 | Streaming + instructions personnalisées | 4 fonctionnalités OK |
| 9-10 | Polissage + tests + rapport | Soutenance prête |

---

## 10. Grille d'évaluation

### Résultats d'apprentissage (tous doivent passer)
1. **Conception BD**: Schéma normalisé, approprié aux fonctionnalités
2. **Architecture logicielle**: Design patterns, modularité
3. **Implémentation complète**: Les 4 fonctionnalités obligatoires opérationnelles

### Critères de notation (100 points total, minimum 50 points pour passer)
- Fonctionnalités obligatoires: ~60 points
- Qualité du code et architecture: ~20 points
- Rapport et documentation: ~10 points
- Fonctionnalités optionnelles + excellence: ~10 points bonus
