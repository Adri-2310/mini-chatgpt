# 📝 RÉSUMÉ RAPIDE — Corrections à faire

## Statut Actuel : 11-13/20 → Potentiel : 16-17/20

**UPDATE** : shadcn-vue n'est PAS urgent (prof confirmé)

---

## 🎯 5 CORRECTIONS MAJEURES

### 1️⃣ THÉMATISATION (-6 à -8 pts)
**Gagner +8 points en 2h**

```plaintext
AVANT : "Mini-ChatGPT" - outil générique
APRÈS : "StudyBuddy" - assistant d'études pour étudiants
```

**À faire** :
- [ ] Renommer nav : "🤖 Mini-ChatGPT" → "📚 StudyBuddy"
- [ ] Réécrire Welcome.vue : accroche académique
- [ ] Adapter Dashboard.vue : "assistant polyvalent" → "assistant d'études"
- [ ] Adapter Settings.vue : prompt défaut académique
- [ ] Changer couleurs : bleu/violet → vert/bleu

**Fichiers** : Nav.vue, Welcome.vue, Dashboard.vue, Settings.vue, .env

---

### 2️⃣ shadcn-vue (-2 à -3 pts)
**Gagner +3 points en 1.5h**

```bash
npm install radix-vue class-variance-authority clsx tailwind-merge
```

**À faire** :
- [ ] Créer `components/ui/Button.vue`
- [ ] Créer `components/ui/Input.vue`
- [ ] Créer `components/ui/Dialog.vue`
- [ ] Créer `lib/utils.ts` avec fonction `cn()`
- [ ] Remplacer 3+ composants Jetstream

**Fichiers** : components/ui/*, lib/utils.ts, Pages/*.vue

---

### 3️⃣ Base de Données (-2 pts)
**Gagner +2 points en 45 min**

#### 3a. Supprimer `first_message` orpheline
```php
php artisan make:migration remove_first_message_from_conversations_table
php artisan migrate
```

#### 3b. Utiliser `LlmModel` depuis BD
```php
// AVANT
'models' => config('ai_models.available')

// APRÈS
'models' => LlmModel::where('enabled', true)
    ->select(['id', 'name', 'provider'])
    ->get()
```

#### 3c. Supprimer suppression redondante
```php
// Retirer cette ligne (cascade FK s'en charge)
$conversation->messages()->delete();
```

**Fichiers** : ConversationController.php, AskController.php, nouvelle migration

---

### 4️⃣ Génération Titre (-0.5 pt)
**Gagner +0.5 points en 30 min**

**Changement** :
```php
// AVANT : if ($messageCount >= 4)
// APRÈS : if ($messageCount >= 2)
```

**Fichiers** : MessageController.php (2 endroits : `store()` et `streamStore()`)

---

### 5️⃣ XSS Titre (-0.5 pt)
**Gagner +0.5 points en 10 min**

```vue
<!-- AVANT -->
<h1 v-html="renderTitle(props.conversationTitle)"></h1>

<!-- APRÈS -->
<h1>{{ props.conversationTitle }}</h1>
```

---

**Fichiers** : ChatHeader.vue

---

## ⏱️ TIMELINE

| Jour | Tâche | Durée | Cumul |
|------|-------|-------|-------|
| 1-2 | Thématisation | 2h | 2h |
| 2-3 | shadcn-vue | 1.5h | 3.5h |
| 3 | BD clean | 45 min | 4h15 |
| 4 | Titre + XSS | 40 min | 5h |
| 5 | Tests | 1h | 6h |

**Total travail** : ~6-7 heures

---

## 📊 RÉSULTAT FINAL ESTIMÉ

### Avant
```
✅ Composition API :    100%
✅ Sélecteur modèles :  100%
✅ Historique :         100%
✅ Streaming :          100%
✅ Instructions :       100%
❌ Thématisation :      0%
⚠️  BD cohérence :      40%
─────────────────────
Score : 11-13/20 (55-65%)
```

### Après corrections
```
✅ Composition API :    100%
✅ Sélecteur modèles :  100%
✅ Historique :         100%
✅ Streaming :          100%
✅ Instructions :       100%
✅ Thématisation :      100%
✅ BD cohérence :       95%
─────────────────────
Score : 16-17/20 (80-85%)
```

---

## 🚀 COMMANDES RAPIDES

```bash
# Créer migration
php artisan make:migration remove_first_message_from_conversations_table

# Exécuter migration
php artisan migrate

# Dev local
npm run dev & php artisan serve

# Build
npm run build

# Tests
php artisan test
```

---

## 📌 NE PAS OUBLIER

1. **Commits Git** : Commit chaque correction séparément
   ```bash
   git add .
   git commit -m "Thématiser l'app en StudyBuddy"
   git commit -m "Nettoyer BD et utiliser ORM Eloquent"
   ```

2. **Tester** : Avant chaque commit, vérifier `npm run dev`

3. **RAPPORT.md** : Remplir avec les justifications de chaque changement

4. **Défense orale** : Préparer réponses sur `first_message`, `LlmModel`, `cascadeOnDelete`

---

## ✅ VICTORY CHECKLIST

- [ ] App s'appelle "StudyBuddy" partout
- [ ] Migration `first_message` exécutée
- [ ] Controllers utilisent `LlmModel::where('enabled')`
- [ ] `$conversation->messages()->delete()` supprimé
- [ ] Titre généré après 2 messages
- [ ] `npm run build` réussit
- [ ] `php artisan test` passe
- [ ] Commits pushés sur GitHub
- [ ] Prêt pour défense orale

---

**Créé le** : 1er juin 2026  
**Deadline** : 20 juin 2026  
**Temps disponible** : 19 jours  
**Temps de travail réel** : 4.5 heures  

**Tu peux le faire ! 💪**
