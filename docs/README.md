# 📚 Documentation Mini-ChatGPT

Index complet de la documentation du projet.

---

## 📋 Contenu

### 📑 [Requirements](./requirements/)
Spécifications et scope du projet.

- **[PRD_FR.md](./requirements/PRD_FR.md)** - Document de Spécifications Produit
  - Vue d'ensemble du projet
  - Stack technologique obligatoire
  - Fonctionnalités obligatoires et optionnelles
  - Critères de succès et grille d'évaluation

- **[MVP_SCOPE_FR.md](./requirements/MVP_SCOPE_FR.md)** - Scope du MVP
  - 6 phases de développement
  - Livrables par phase
  - Critères d'acceptation
  - Timeline et jalons clés

---

### 🏗️ [Architecture](./architecture/)
Architecture système et schémas.

- **[DATABASE_DIAGRAM_UPDATED.puml](./architecture/DATABASE_DIAGRAM_UPDATED.puml)** - Diagramme ER
  - Schéma de la base de données
  - Relations entre les tables
  - Entités principales (Users, Conversations, Messages, etc.)

---

### 🧪 Tests & Logs
Tests automatisés et système de logs.

- **[CHECKLIST_TESTS_LOGS.md](./CHECKLIST_TESTS_LOGS.md)** - Checklist développement
  - Étapes numérotées avec checkboxes
  - Tests unitaires et feature
  - Configuration des logs
  - Vérification finale

- **[TESTS_LOGS_PLAN.md](./TESTS_LOGS_PLAN.md)** - Plan détaillé d'implémentation
  - Code d'exemple complet pour chaque test
  - Stratégies de mock (Http::fake, Mockery)
  - Fichiers à créer/modifier avec détails
  - Métriques de succès

---

### 🎨 [Styling](./styling/)
Documentation du système de design et du thème.

- **[THEME_TWEAKCN.md](./styling/THEME_TWEAKCN.md)** - Système de thème Tweakcn
  - Vue d'ensemble du système CSS personnalisé
  - Catalogue complet des variables CSS
  - Utilisation dans les composants Vue
  - Guide de personnalisation et mode sombre
  - Bonnes pratiques et troubleshooting

---

### 📖 [Guides](./guides/)
Guides pratiques et documentation technique.

*(À compléter lors du développement)*

- Installation et setup
- API Reference
- Guide de développement
- Déploiement

---

## 🚀 Quick Links

- **[README Principal](../README.md)** - Guide d'installation et utilisation
- **[Development Progress](./DEVELOPMENT_PROGRESS.md)** - Suivi du développement en temps réel
- **[Repository GitHub](https://github.com/Adri-2310/mini-chatgpt)**
- **Branches**: `main` (production), `dev` (développement)

---

## 📅 Phases du Projet

| Phase | Titre | Status |
|-------|-------|--------|
| 1 | Fondations (BD + Auth) | ✅ Complétée |
| 2 | Chat Simple (Ask) | ✅ Complétée |
| 3 | Conversations avec Historique | ✅ Complétée |
| 4 | Instructions Personnalisées | ✅ Complétée |
| 5 | Streaming Temps Réel | ✅ Complétée |
| 6 | Seeders + Navigation | ✅ Complétée |
| 7 | **Tests & Logs (Nouveau)** | 🟡 En planification |
| 8 | Polissage + Tests Manuels | ⏳ À faire |
| 9 | Fonctionnalités Avancées | ⏳ Optionnel |

---

## 📝 Notes

- Tous les documents sont en **français** sauf mention contraire
- Les diagrammes sont en format **PlantUML** (`.puml`)
- Voir le **README.md** racine pour les instructions de setup

