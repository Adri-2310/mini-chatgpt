# 📋 RAPPORT D'ACTION — Mini ChatGPT
## Ce qu'il faut corriger avant le 20 juin 2026

**Statut actuel** : 11-13/20  
**Potentiel après corrections** : 16-17/20  
**Deadline** : 20 juin 2026, 23h59  
**Jours restants** : 19 jours  
**Temps réel requis** : 4-5 heures seulement

---

## 🚨 RÉSUMÉ DES CORRECTIONS PRIORITAIRES

| Problème | Gravité | Impact | Effort | Délai |
|----------|---------|--------|--------|-------|
| **1. Thématisation absente** | CRITIQUE | -8 pts | 2h | JOUR 1-2 |
| **2. `first_message` orpheline** | Important | -1 pt | 15 min | JOUR 3 |
| **3. `llm_models` non utilisé** | Important | -1 pt | 30 min | JOUR 3 |
| **4. Suppression BD redondante** | Important | -1 pt | 10 min | JOUR 3 |
| **5. Titre généré trop tard** | Souhaitable | -0.5 pt | 30 min | JOUR 3 |

**TOTAL POTENTIEL DE GAIN : ~11.5 points**

**NOTE** : ~~shadcn-vue~~ PAS URGENT (prof confirmé)

---

## 📌 PRIORITÉ 1 — THÉMATISATION (CRITIQUE)
### ⏱️ Délai : 2-3 heures | JOUR 1-2

**Pourquoi c'est critique** :
- Le prof demande explicitement : "Application doit cibler un public spécifique avec identité cohérente"
- C'est la première chose qu'il voit en accédant à l'app
- Actuellement : application générique "Mini-ChatGPT" sans âme
- Perte d'impact : -30 à -40% de la note

### Choix du thème

Choisir UN thème parmi :
- **StudyBuddy** — Assistant pédagogique pour étudiants (recommandé pour un cours)
- **PsyBot** — Coach en bien-être/psychologie
- **CoachFit** — Coach fitness/santé
- **DevMentor** — Mentor en développement (pertinent pour ce cours)
- **WriterMuse** — Assistant création littéraire
- **CareerCoach** — Conseiller en carrière
- **Custom** — Ton propre concept

**Recommandation : StudyBuddy** (thème académique cohérent)

---

### TÂCHE 1.1 : Renommer l'application partout

**Fichiers à modifier** :

#### 1. `.env`
```bash
# AVANT
APP_NAME=Laravel
APP_URL=http://localhost:8000

# APRÈS
APP_NAME=StudyBuddy
APP_URL=http://localhost:8000
```

#### 2. `resources/js/Components/Nav.vue`
```vue
<!-- AVANT -->
<span class="text-xl font-bold text-white">🤖 Mini-ChatGPT</span>

<!-- APRÈS -->
<span class="text-xl font-bold text-white">📚 StudyBuddy</span>
```

#### 3. `resources/js/Pages/Welcome.vue` (REWRITE complet)
```vue
<!-- AVANT : Hero générique -->
<h1 class="text-5xl md:text-6xl font-bold leading-tight">
  Parlez avec
  <span class="block bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
    GPT-4, Gemini et Claude
  </span>
</h1>

<!-- APRÈS : Spécialisé StudyBuddy -->
<h1 class="text-5xl md:text-6xl font-bold leading-tight">
  Apprenez mieux avec
  <span class="block bg-gradient-to-r from-green-400 to-blue-500 bg-clip-text text-transparent">
    StudyBuddy
  </span>
</h1>

<p class="text-lg md:text-xl text-slate-300 max-w-2xl mx-auto">
  Votre assistant d'études personnalisé. Comprenez vos cours, préparez vos examens,
  et progressez avec l'aide d'une IA adaptée à votre parcours académique.
</p>
```

#### 4. `resources/js/Pages/Dashboard.vue`
```vue
<!-- AVANT -->
<p class="text-slate-300 text-lg max-w-2xl mx-auto">
    Explorez les capacités de l'IA avec notre assistant polyvalent. Posez une question ou lancez une conversation.
</p>

<!-- APRÈS -->
<p class="text-slate-300 text-lg max-w-2xl mx-auto">
    Besoin d'aide pour vos études ? Demandez à StudyBuddy ! Explications, résumés, préparation d'examen...
</p>

<!-- Aussi adapter les card titles -->
<h2 class="text-xl font-bold text-white mb-2">Poser une Question</h2>
<p class="text-slate-300 mb-4">Demandez de l'aide sur un concept ou un devoir.</p>

<h2 class="text-xl font-bold text-white mb-2">Mes Cours</h2>
<p class="text-slate-300 mb-4">Continuez vos sessions d'étude enregistrées.</p>

<h2 class="text-xl font-bold text-white mb-2">Mes Paramètres</h2>
<p class="text-slate-300 mb-4">Configurer StudyBuddy pour votre style d'apprentissage.</p>
```

#### 5. `resources/js/Pages/Settings.vue`
```vue
<!-- AVANT -->
<template #title>
    Instructions Personnalisées
</template>
<template #description>
    Configurez vos instructions personnalisées qui seront automatiquement appliquées à toutes vos conversations.
</template>

<!-- APRÈS -->
<template #title>
    Personnalisez StudyBuddy
</template>
<template #description>
    Définissez comment StudyBuddy doit vous aider. Spécifiez votre niveau, vos matières, votre style d'apprentissage préféré.
</template>

<!-- Placeholder textarea adapté -->
<textarea
    placeholder="Ex: Je suis en L2 Informatique. Explique-moi les concepts en français, donne des exemples concrets, aide-moi à préparer mes partiels."
>
```

#### 6. `config/app.php` (si utilise ce fichier)
```php
'name' => env('APP_NAME', 'StudyBuddy'),
```

---

---

### TÂCHE 1.2 : Adapter le contenu et le ton

#### Dans `Welcome.vue`, améliorer la section Features :
```vue
<div class="grid md:grid-cols-3 gap-8">
  <!-- Feature 1 -->
  <div class="bg-slate-700/30 border border-slate-600 rounded-xl p-8">
    <div class="text-4xl mb-4">📖</div>
    <h3 class="text-2xl font-bold mb-4">Explications Claires</h3>
    <p class="text-slate-400">
      StudyBuddy explique les concepts complexes simplement, avec des exemples concrets adaptés à votre niveau.
    </p>
  </div>

  <!-- Feature 2 -->
  <div class="bg-slate-700/30 border border-slate-600 rounded-xl p-8">
    <div class="text-4xl mb-4">✍️</div>
    <h3 class="text-2xl font-bold mb-4">Préparation d'Examen</h3>
    <p class="text-slate-400">
      Générez des questions type, des résumés, et testez vos connaissances avant le grand jour.
    </p>
  </div>

  <!-- Feature 3 -->
  <div class="bg-slate-700/30 border border-slate-600 rounded-xl p-8">
    <div class="text-4xl mb-4">💬</div>
    <h3 class="text-2xl font-bold mb-4">Conversations Persistantes</h3>
    <p class="text-slate-400">
      Gardez l'historique de vos sessions d'étude et continuez vos révisions à tout moment.
    </p>
  </div>
</div>
```

#### Changer les couleurs de marque pour StudyBuddy :
```css
/* Avant : bleu/violet générique */
from-blue-400 to-purple-500
border-blue-400

/* Après : vert/bleu académique */
from-green-400 to-blue-500
border-green-400
```

---

### TÂCHE 1.3 : Configurer le System Prompt par défaut

#### Dans `resources/js/Pages/Settings.vue`, changer le placeholder :

```vue
<textarea
  id="instructions"
  v-model="form.instructions"
  maxlength="2000"
  rows="8"
  placeholder="Ex: Je suis en L2 Informatique. Explique les concepts en français simple, donne des exemples concrets, aide à la préparation d'examen. Résume quand c'est long."
></textarea>
```

#### Ou créer un seeder pour le prompt par défaut

Si tu veux qu'un utilisateur NEW ait déjà un prompt pré-rempli :

```php
// Dans MigrateDefaultCustomInstructions ou un seeder
$user->customInstruction()->create([
    'instructions' => 'Tu es StudyBuddy, un assistant d\'études personnalisé...',
    'enabled' => true,
]);
```

---

---

### ✅ CHECKLIST THÉMATISATION

- [ ] Renommé partout : "Mini-ChatGPT" → "StudyBuddy"
- [ ] Welcome.vue réécrite avec accroche thématisée
- [ ] Dashboard.vue adapté au thème
- [ ] Settings.vue textes adaptés
- [ ] Couleurs de marque changées (bleu/vert cohérent)
- [ ] Emojis adaptés (📚 pour StudyBuddy, pas 🤖)
- [ ] Testé localement : `npm run dev` + visite pages

**Temps estimé : 1.5-2h**

---

## 📌 PRIORITÉ 2 — shadcn-vue (CRITIQUE)
### ⏱️ Délai : 1-2 heures | JOUR 2-3

**Pourquoi c'est critique** :
- Stack officielle impose `TailwindCSS + shadcn-vue`
- Actuellement 0 shadcn-vue, composants Jetstream manuels
- Perte : -2 à -3 pts

**Approche** : Ne pas tout migrer (trop long). Migrer 4-5 composants clés pour montrer la maîtrise.

---

### TÂCHE 2.1 : Installer shadcn-vue

```bash
npm install radix-vue class-variance-authority clsx tailwind-merge
```

---

### TÂCHE 2.2 : Créer les composants shadcn-vue

Créer dans `resources/js/Components/ui/` :

#### `Button.vue` (remplace PrimaryButton.vue)
```vue
<script setup>
import { computed } from 'vue'
import { cva } from 'class-variance-authority'
import { cn } from '@/lib/utils' // Créer ce fichier

const buttonVariants = cva(
  'inline-flex items-center justify-center rounded-md font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed',
  {
    variants: {
      variant: {
        default: 'bg-blue-600 text-white hover:bg-blue-700 focus-visible:ring-blue-500',
        destructive: 'bg-red-600 text-white hover:bg-red-700 focus-visible:ring-red-500',
        secondary: 'bg-slate-700 text-white hover:bg-slate-600 focus-visible:ring-slate-500',
      },
      size: {
        default: 'h-10 px-4 py-2 text-sm',
        sm: 'h-9 px-3 text-xs',
        lg: 'h-11 px-8 text-lg',
      },
    },
    defaultVariants: {
      variant: 'default',
      size: 'default',
    },
  }
)

const props = defineProps({
  variant: String,
  size: String,
  asChild: Boolean,
})

const buttonClass = computed(() => buttonVariants({ variant: props.variant, size: props.size }))
</script>

<template>
  <button :class="buttonClass"><slot /></button>
</template>
```

#### `Input.vue` (remplace TextInput.vue)
```vue
<script setup>
import { cn } from '@/lib/utils'

defineProps({
  class: String,
})
</script>

<template>
  <input
    :class="cn(
      'flex h-10 w-full rounded-md border border-slate-600 bg-slate-700 px-3 py-2 text-sm text-white placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 transition disabled:opacity-50 disabled:cursor-not-allowed',
      $attrs.class
    )"
  />
</template>
```

#### `Dialog.vue` (remplace Modal.vue)
```vue
<script setup>
import { computed } from 'vue'

const props = defineProps({
  open: Boolean,
})

const emit = defineEmits(['update:open'])
</script>

<template>
  <div v-if="open" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-slate-800 border border-slate-700 rounded-lg p-6 max-w-sm mx-4 space-y-4">
      <slot />
    </div>
  </div>
</template>
```

#### Créer `lib/utils.ts` pour `cn()` helper
```typescript
// resources/js/lib/utils.ts
export function cn(...inputs: any[]) {
  return inputs.filter(Boolean).join(' ')
}
```

---

---

### TÂCHE 2.3 : Remplacer les composants

#### Dans `resources/js/Pages/Settings.vue` :
```vue
<!-- AVANT -->
<PrimaryButton @click="submit">Enregistrer</PrimaryButton>

<!-- APRÈS -->
<Button variant="default" @click="submit">Enregistrer</Button>
```

#### Dans `resources/js/Components/ConversationList.vue` :
```vue
<!-- AVANT : Modal manuel -->
<div v-if="showEditModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
  <div class="bg-slate-800 border border-slate-700 rounded-lg p-6 max-w-sm mx-4 space-y-4">
    <!-- ... -->
  </div>
</div>

<!-- APRÈS : Dialog component -->
<Dialog :open="showEditModal" @update:open="showEditModal = $event">
  <!-- ... -->
</Dialog>
```

---

---

### ✅ CHECKLIST shadcn-vue

- [ ] `npm install radix-vue` réussi
- [ ] `resources/js/lib/utils.ts` créé
- [ ] `components/ui/Button.vue` créé et testé
- [ ] `components/ui/Input.vue` créé et testé
- [ ] `components/ui/Dialog.vue` créé et testé
- [ ] Au minimum 3 composants remplacés dans l'app
- [ ] App fonctionne : `npm run dev`
- [ ] Build réussit : `npm run build`

**Temps estimé : 1.5h**

---

**Pourquoi c'est important** :
- BD actuellement incohérente (colonnes orphelines, sources dupliquées)
- Montre la maîtrise d'Eloquent ORM et des relations
- Perte : -3 pts si non corrigé

---

### TÂCHE 3.1 : Supprimer `first_message` orpheline

Cette colonne n'est jamais utilisée. À supprimer.

#### Créer une nouvelle migration
```bash
php artisan make:migration remove_first_message_from_conversations_table
```

#### Contenu de la migration
```php
// database/migrations/2026_06_01_xxxxxx_remove_first_message_from_conversations_table.php

public function up(): void
{
    Schema::table('conversations', function (Blueprint $table) {
        $table->dropColumn('first_message');
    });
}

public function down(): void
{
    Schema::table('conversations', function (Blueprint $table) {
        $table->text('first_message')->nullable();
    });
}
```

---

#### Mettre à jour le modèle Conversation
```php
// app/Models/Conversation.php
protected $fillable = ['user_id', 'title', 'model_used'];  // Retirer 'first_message'
```

---

#### Exécuter la migration
```bash
php artisan migrate
```

---

### TÂCHE 3.2 : Utiliser `LlmModel` depuis la BD (au lieu de config hardcодé)

Le sélecteur de modèles doit venir de la BD, pas d'un fichier config.

#### Modifier `ConversationController::chat()`
```php
// AVANT
return Inertia::render('Chat', [
    'conversations' => $conversations,
    'models' => config('ai_models.available'),  // ❌ Hardcодé
]);

// APRÈS
return Inertia::render('Chat', [
    'conversations' => $conversations,
    'models' => LlmModel::where('enabled', true)
        ->select(['id', 'name', 'provider'])
        ->get(),  // ✅ Depuis la BD
]);
```

#### Même modification dans `AskController::index()`
```php
public function index()
{
    return Inertia::render('Ask', [
        'models' => LlmModel::where('enabled', true)
            ->select(['id', 'name', 'provider'])
            ->get(),
    ]);
}
```

---

#### Vérifier que `LlmModel` est importé
```php
use App\Models\LlmModel;
```

#### Optionnel : Garder le config pour un default unique
```php
// config/ai_models.php
return [
    'default' => 'openai/gpt-4o-mini',  // Sert juste de reference
];
```

---

### TÂCHE 3.3 : Supprimer la suppression redondante

La foreign key avec `cascadeOnDelete()` s'occupe de supprimer les messages.

#### Modifier `ConversationController::destroy()`
```php
// AVANT
public function destroy(Conversation $conversation)
{
    try {
        $this->authorize('delete', $conversation);
    } catch (AuthorizationException) {
        return response()->json(['error' => 'Non autorisé'], 403);
    }

    $conversation->messages()->delete();  // ❌ Redondant
    $conversation->delete();

    return response()->json(['success' => true]);
}

// APRÈS
public function destroy(Conversation $conversation)
{
    try {
        $this->authorize('delete', $conversation);
    } catch (AuthorizationException) {
        return response()->json(['error' => 'Non autorisé'], 403);
    }

    $conversation->delete();  // ✅ Cascade FK s'occupe des messages

    return response()->json(['success' => true]);
}
```

---

---

### ✅ CHECKLIST BD

- [ ] Nouvelle migration `remove_first_message` créée
- [ ] Migration exécutée : `php artisan migrate`
- [ ] Modèle `Conversation::$fillable` mis à jour
- [ ] `ConversationController::chat()` utilise `LlmModel::where('enabled')`
- [ ] `AskController::index()` utilise `LlmModel::where('enabled')`
- [ ] `ConversationController::destroy()` ne supprime plus manuellement les messages
- [ ] Tests passent : `php artisan test`

**Temps estimé : 45 min**

---

## 📌 BONUS — AMÉLIORER GÉNÉRATION TITRE (SOUHAITABLE)
### ⏱️ Délai : 30 minutes | JOUR 4

**Actuel** : Titre généré après 4 messages (trop tardif)  
**Cible** : Titre généré après 2 messages (1 échange complet)

---

### TÂCHE 4.1 : Modifier le seuil dans `MessageController`

#### Dans `store()` (non-streaming)
```php
// AVANT
if ($messageCount >= 4) {

// APRÈS
if ($messageCount >= 2) {
```

#### Dans `streamStore()` (streaming)
```php
// AVANT
if ($messageCount >= 4) {

// APRÈS
if ($messageCount >= 2) {
```

---

### TÂCHE 4.2 : Envoyer le titre immédiatement après streaming

Actuellement, le titre n'est généré qu'après et pas visible jusqu'au rechargement. Améliorer en l'envoyant via SSE.

#### Dans `streamStore()`, après le `[DONE]`
```php
// Après "data: [DONE]\n\n", envoyer le titre généré
if (!$conversation->title || $conversation->title === 'Nouvelle conversation') {
    $messageCount = $conversation->messages()->count();
    if ($messageCount >= 2) {
        $title = $this->generateConversationTitle($conversation, $model);
        $conversation->update(['title' => $title]);
        
        // Envoyer le titre au frontend
        echo "data: " . json_encode(['type' => 'title', 'title' => $title]) . "\n\n";
        if (ob_get_level() > 0) ob_flush();
        flush();
    }
}

echo "data: [DONE]\n\n";
```

#### Frontend : Traiter l'event titre dans `Chat.vue`
```javascript
onData: (rawData) => {
    // ... code existant ...
    
    if (data.type === 'title') {
        // Mettre à jour le titre localement
        const index = conversations.value.findIndex(c => c.id === activeConversationId.value);
        if (index > -1) {
            conversations.value[index].title = data.title;
        }
    }
}
```

---

---

### ✅ CHECKLIST TITRE

- [ ] Seuil changé à `>= 2` dans `store()`
- [ ] Seuil changé à `>= 2` dans `streamStore()`
- [ ] Event SSE titre ajouté après `[DONE]`
- [ ] Frontend traite l'event titre
- [ ] Testé : créer une conversation, voir titre après 2 messages

**Temps estimé : 30 min**

---

## 📌 BONUS 2 — CORRIGER XSS POTENTIEL
### ⏱️ Délai : 10 minutes | JOUR 4

**Problème** : Le titre est rendu en Markdown avec `v-html`, risque minime mais mauvaise pratique.

### TÂCHE 5.1 : Supprimer le rendu Markdown du titre

#### Dans `ChatHeader.vue`
```vue
<!-- AVANT -->
<h1 class="text-xl font-semibold text-white truncate" v-html="renderTitle(props.conversationTitle)">
</h1>

<!-- APRÈS -->
<h1 class="text-xl font-semibold text-white truncate">
    {{ props.conversationTitle }}
</h1>
```

#### Supprimer l'import inutile
```vue
<!-- AVANT -->
const { render } = useMarkdown();
const renderTitle = (title) => render(title);

<!-- APRÈS -->
// Rien, non utilisé
```

---

---

### ✅ CHECKLIST XSS

- [ ] Titre rendu en plain text ({{ }}) au lieu de HTML
- [ ] Fonction `renderTitle` supprimée
- [ ] Testé : les titres s'affichent bien sans HTML

**Temps estimé : 10 min**

---

## 🧪 PHASE TEST & VALIDATION
### ⏱️ Délai : 1 heure | JOUR 5

### TÂCHE TEST 1 : Tests locaux

```bash
# 1. Effacer la BD et recommencer
php artisan migrate:fresh --seed

# 2. Dev server
npm run dev
php artisan serve

# 3. Visiter les pages
- http://localhost:8000 (Welcome) — Vérifie StudyBuddy partout
- http://localhost:8000/register (Register) — Vérifie design shadcn-vue
- http://localhost:8000/dashboard (Dashboard) — Textes adaptés
- http://localhost:8000/chat (Chat) — Modèles viennent de BD
- http://localhost:8000/settings (Settings) — Prompt adapté

# 4. Tests
php artisan test

# 5. Build
npm run build
```

### TÂCHE TEST 2 : Checklist manuelle

- [ ] Tous les textes mentionnent "StudyBuddy" (pas "Mini-ChatGPT")
- [ ] Couleurs de marque cohérentes (vert/bleu pour StudyBuddy)
- [ ] Emojis adaptés (📚 vs 🤖)
- [ ] Créer une conversation → titre après 2 messages
- [ ] Supprimer une conversation → les messages disparaissent en cascade (pas d'erreur)
- [ ] Changer de modèle → sélecteur affiche les modèles depuis la BD
- [ ] Éditer instructions → le système prompt est appliqué
- [ ] `npm run build` réussit sans erreur
- [ ] `php artisan test` passe tous les tests

---

## 📊 TIMELINE RECOMMANDÉE

```
JOUR 1  (2h)  : Thématisation (Welcome, Dashboard, Nav, Settings)
JOUR 2  (2h)  : shadcn-vue (install, Button, Input, Dialog)
JOUR 3  (1h)  : BD clean (first_message, LlmModel, cascade)
JOUR 4  (1h)  : Titre + XSS
JOUR 5  (1h)  : Tests & validation
─────────────────────
TOTAL   : ~7h de travail

Restant : 13 jours pour :
- Remplir le RAPPORT.md
- Préparer la défense orale
- Rédiger le cahier métacognitif
```

---

## 🎯 RÉSULTATS ATTENDUS

### Avant corrections
```
Composition API :    ✅
Sélecteur modèles :  ✅
Historique :         ✅
Streaming SSE :      ✅
Instructions :       ✅
─────────────────────
Thématisation :      ❌ -8 pts
shadcn-vue :         ❌ -3 pts
BD clean :           ⚠️  -2 pts
─────────────────────
TOTAL :              11-13/20  (55-65%)
```

### Après corrections
```
Composition API :    ✅ +0
Sélecteur modèles :  ✅ +0
Historique :         ✅ +0
Streaming SSE :      ✅ +0
Instructions :       ✅ +0
─────────────────────
Thématisation :      ✅ +8 pts
shadcn-vue :         ✅ +3 pts
BD clean :           ✅ +2 pts
─────────────────────
TOTAL :              17-18/20  (85-90%)
```

---

## 📋 COMMANDES UTILES

```bash
# Dev
npm run dev
php artisan serve

# Migrations
php artisan make:migration remove_first_message_from_conversations_table
php artisan migrate
php artisan migrate:fresh --seed

# Tests
php artisan test
php artisan test --filter=ConversationControllerTest

# Build
npm run build

# Logs
tail -f storage/logs/ai.log
```

---

## 🚨 POINTS À RETENIR POUR L'ORAL

### Si le prof demande sur ces points, tu répondras :

**Q : Pourquoi `first_message` dans la migration si jamais utilisée ?**  
R : "C'était une ancienne tentative d'optimisation pour éviter une requête `messages->first()`. J'ai supprimé la colonne car elle reste vide et c'est une mauvaise pratique de la BD."

**Q : Pourquoi `LlmModel` en BD si vous utilisez `config/ai_models.php` ?**  
R : "Exactement, c'était redondant. J'ai maintenant changé tous les controllers pour interroger la BD via Eloquent `LlmModel::where('enabled', true)->get()`. La config n'est qu'un default maintenant."

**Q : Qu'est-ce que StudyBuddy ? C'est très différent de Mini-ChatGPT.**  
R : "Oui, j'ai thématisé l'application pour cibler un public spécifique — les étudiants. StudyBuddy est un assistant d'études personnalisé avec un branding, un ton, des features adaptées à l'apprentissage. C'était une exigence du cahier des charges."

**Q : Pourquoi vous avez supprimé `$conversation->messages()->delete()` dans destroy ?**  
R : "La foreign key avec `cascadeOnDelete()` s'en charge déjà. C'est une bonne pratique de laisser la BD gérer l'intégrité référentielle plutôt que d'avoir une logique applicative redondante."

---

## ✅ FINAL CHECKLIST

- [ ] Thématisation complète (StudyBuddy partout)
- [ ] shadcn-vue minimum 3 composants remplacés
- [ ] Migration `remove_first_message` exécutée
- [ ] Controllers utilisent `LlmModel` ORM
- [ ] `destroy()` sans suppression manuelle
- [ ] Titre généré après 2 messages
- [ ] XSS corrigé (titre en plain text)
- [ ] Tests passent
- [ ] Build réussit
- [ ] RAPPORT.md rempli avec justifications
- [ ] Prêt pour la défense orale

---

**Créé le** : 1er juin 2026  
**Deadline** : 20 juin 2026  
**Temps total estimé** : 7 heures de travail  
**Potentiel de gain** : +5 à +7 points

**Bonne chance ! 🚀**
