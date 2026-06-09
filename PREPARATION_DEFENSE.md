# 🎤 PRÉPARATION DÉFENSE ORALE
## Questions probables et réponses

**Durée** : ~15 minutes  
**Type** : Questions projet + 2 questions théoriques aléatoires

---

## PARTIE 1 — QUESTIONS SUR LE PROJET (10 min)

### Q1 : "Décrivez l'architecture générale de votre application"

**Réponse attendue** (2 min) :

"StudyBuddy est une application web construite avec Laravel + Inertia + Vue 3 (Composition API). 

L'architecture est en couches :
- **Backend (Laravel)** :
  - Controllers : ConversationController, MessageController, SettingsController
  - Models : User, Conversation, Message, CustomInstruction, LlmModel
  - Service Layer : ChatService qui gère les appels à OpenRouter API
  - Policies : ConversationPolicy pour l'autorisation (ownership)

- **Frontend (Vue 3)** :
  - Pages principales : Chat.vue, Ask.vue, Settings.vue
  - Composants réutilisables : MessageList, ConversationList, ModelSelector, ChatHeader
  - Composables : useMarkdown pour rendu markdown
  
- **BD (MySQL)** :
  - users (Jetstream)
  - conversations (titre, modèle utilisé)
  - messages (role, contenu, tokens utilisés)
  - custom_instructions (system prompt par utilisateur)
  - llm_models (GPT-4o mini, Gemini, Claude)"

**Points clés à mentionner** :
- Separation of concerns (Service Layer)
- Composition API Vue 3 (100% du code)
- Policies pour sécurité ownership
- BD bien structurée avec relations

---

### Q2 : "Expliquez comment fonctionne le streaming SSE"

**Réponse attendue** (2 min) :

"Le streaming SSE fonctionne en deux parties :

**1. Backend (PHP/Laravel)** :
- MessageController::streamStore() crée une réponse avec headers SSE
  - `Content-Type: text/event-stream`
  - `Cache-Control: no-cache`
  - `X-Accel-Buffering: no`
- ChatService::streamWithHistory() utilise Guzzle en mode stream
  - Lit le flux OpenRouter API chaque 1024 bytes
  - Parse la ligne JSON SSE
  - Extrait le contenu (`data['choices'][0]['delta']['content']`)
  - Envoie chaque chunk au client via `echo 'data: ...\n\n'`

**2. Frontend (Vue 3)** :
- useStream() (composable @laravel/stream-vue) établit une connexion POST
  - Envoie le message + modèle choisi
- onData callback reçoit chaque chunk en temps réel
  - Gère un buffer `streamBuffer` pour les chunks partiels
  - Met à jour le message assistant token par token
  - Scroll automatique en bas
- onFinish recharge les données depuis la BD pour récupérer les tokens totaux

**Points clés** :
- Buffer gestion des chunks partiels (très important)
- Generator PHP pour économiser la mémoire
- Guzzle avec `'stream' => true` pour lecture progressive"

**Points avancés à ajouter si tu veux impressionner** :
- "J'ai dû gérer l'issue que le titre auto-généré arrive avant le [DONE], donc il faut relire la conversation post-stream"
- "L'accumulation de tokens se fait lors du streaming même s'ils ne sont pas affichés avant la fin"

---

### Q3 : "Pourquoi l'application s'appelle StudyBuddy et pas Mini-ChatGPT ?"

**Réponse attendue** (1.5 min) :

"Le cahier des charges demandait explicitement une application thématisée ciblant un public spécifique, pas une application générique. 

J'ai choisi le thème **StudyBuddy — assistant d'études pour étudiants** parce que :
1. C'est pertinent — le contexte est un cours universitaire
2. Ça définit une identité — on voit tout de suite qui c'est pour
3. Ça influence les choix de design — couleurs, ton, features

Contrairement à Mini-ChatGPT qui est générique, StudyBuddy a :
- Branding cohérent (📚 au lieu de 🤖)
- Copies adaptées ("explications claires", "préparation d'examen")
- System prompt par défaut académique
- Interface orientée pédagogie"

**Si le prof conteste (unlikely)** :
"Ah, vous aviez un thème préféré ? Je peux le changer facilement. Voulez-vous que ce soit PsyBot, CoachFit, ou un autre ?"

---

### Q4 : "Parlez de la base de données, relations et intégrité"

**Réponse attendue** (2 min) :

"La BD a 5 tables principales :

| Table | Relations | Contraintes |
|-------|-----------|-------------|
| users | (Jetstream) | PK id |
| conversations | user_id → users | FK cascadeOnDelete |
| messages | conversation_id → conversations | FK cascadeOnDelete |
| custom_instructions | user_id → users (UNIQUE) | FK cascadeOnDelete |
| llm_models | — | INDEX provider |

**Intégrité assurée par** :
- Foreign keys avec `cascadeOnDelete()` — suppression d'une conversation supprime ses messages
- `user_id` UNIQUE sur custom_instructions — un seul instruction par user
- ENUM sur `role` (user/assistant) — seules valeurs valides acceptées
- Indexes sur les colonnes fréquemment interrogées

**Exemple** : Si un user supprime son compte, Jetstream utilise les cascades pour supprimer automatiquement ses conversations, messages, et instructions. Pas de données orphelines."

**Si demandé** : 
"Au début il y avait une colonne `first_message` inutilisée. J'ai créé une migration pour la supprimer car c'est une mauvaise pratique de BD d'avoir des colonnes non-utilisées."

---

### Q5 : "Comment assurer la sécurité que chaque user ne voit que ses conversations ?"

**Réponse attendue** (1.5 min) :

"La sécurité est à 3 niveaux :

**1. Niveau DB** :
- Foreign key `user_id` garantit qu'une conversation appartient à un user

**2. Niveau Controller** :
- Policy d'autorisation `ConversationPolicy`
  - `view()` : vérifie `user_id == auth()->id()`
  - `update()` : vérifie `user_id == auth()->id()`
  - `delete()` : vérifie `user_id == auth()->id()`
  - `addMessage()` : vérifie `user_id == auth()->id()`
- Chaque endpoint appelle `$this->authorize()` avant de traiter

**3. Niveau Requête** :
- Les conversations sont récupérées via `auth()->user()->conversations()`
- Jamais `Conversation::find()` qui ignorerait le user

**Exemple** :
```php
public function view(User $user, Conversation $conversation): bool {
    return $user->id === $conversation->user_id;  // Check ownership
}
```

Même si un attaquant connaît l'ID d'une conversation d'un autre user, il obtient une erreur 403 (forbidden)."

---

## PARTIE 2 — QUESTIONS THÉORIQUES (5 min)

Tu vas tirer 2 questions aléatoires parmi celles-ci. Prépare les 6.

---

### Q-THEO-1 : "Qu'est-ce qu'une relation N-N en Eloquent ? Donnez un exemple"

**Réponse concise** (1.5 min) :

"Une relation N-N (Many-to-Many) relie deux tables via une table pivot.

**Exemple** : Si je voulais ajouter **tags** à mes conversations :

**BD** :
- conversations (id, title, ...)
- tags (id, name)
- conversation_tag (conversation_id, tag_id) — **pivot**

**Eloquent** :
```php
class Conversation {
    public function tags() {
        return $this->belongsToMany(Tag::class);
    }
}

$conversation->tags()->attach($tagId);
$conversation->tags()->detach($tagId);
$conversation->tags; // Tous les tags
```

Dans mon projet, je n'ai pas implémenté N-N, mais les relations sont : User 1-∞ Conversations 1-∞ Messages."

---

### Q-THEO-2 : "Qu'est-ce que Eager Loading ? Pourquoi c'est important ?"

**Réponse concise** (1.5 min) :

"Eager Loading charge les relations associées en UNE seule requête au lieu de N requêtes.

**Sans Eager Loading (N+1 problem)** :
```php
$conversations = Conversation::all();  // 1 requête
foreach ($conversations as $conv) {
    echo $conv->user->name;  // N requêtes (1 par conversation)
}
// TOTAL : 1 + N requêtes ❌
```

**Avec Eager Loading** :
```php
$conversations = Conversation::with('user')->get();  // 1 requête + 1 pour les users
foreach ($conversations as $conv) {
    echo $conv->user->name;  // Déjà chargé
}
// TOTAL : 2 requêtes ✅
```

Dans mon projet, j'utilise `with()` dans le `ConversationController::chat()` pour charger les conversations avec l'utilisateur d'un coup."

---

### Q-THEO-3 : "Qu'est-ce qu'une migration Laravel ? À quoi ça sert ?"

**Réponse concise** (1.5 min) :

"Une migration est un fichier PHP qui décrit une modification de schéma BD, versionée dans Git.

**Avantages** :
- Historique de toutes les modifications BD
- Reversible avec `rollback`
- Partageable avec l'équipe (Git)
- Reproductible sur tout environnement (dev, prod)

**Exemple** :
```php
php artisan make:migration create_conversations_table

public function up() {
    Schema::create('conversations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('title');
        $table->timestamps();
    });
}

public function down() {
    Schema::dropIfExists('conversations');
}

php artisan migrate          // Applique
php artisan migrate:rollback // Annule
```

Je suis passé par ce processus à chaque création de table : conversations, messages, custom_instructions."

---

### Q-THEO-4 : "WebSockets vs SSE pour le temps réel. Quelle est la différence ?"

**Réponse concise** (2 min) :

"Les deux permettent du temps réel mais avec des différences fondamentales :

| Aspect | WebSocket | SSE |
|--------|-----------|-----|
| Bidirectionnel | ✅ OUI | ❌ Unidirectionnel (serveur→client) |
| Complexité | ⭐⭐⭐ (Élevée) | ⭐ (Simple, HTTP natif) |
| Cas d'usage | Chat, gaming, sondages | Notifications, streaming, updates |
| Fallback | Polling | Reconnexion auto |

**Mon projet utilise SSE** car :
- Streaming unidirectionnel suffisant (serveur → navigateur)
- Plus simple à implémenter dans Laravel
- Compatible avec Inertia native
- Pas de serveur WebSocket complexe

**Exemple SSE** :
```php
response()->stream(function() {
    echo "data: " . json_encode(['content' => 'hello']) . "\n\n";
    flush();
});
```

**Exemple WebSocket** (plus complexe, besoin de Pusher/Soketi) :
```javascript
ws = new WebSocket('ws://localhost:8080');
ws.onmessage = (msg) => console.log(msg.data);
ws.send('hello'); // Bidirectionnel
```"

---

### Q-THEO-5 : "Composition API vs Options API Vue 3. Pourquoi tu as choisi Composition API ?"

**Réponse concise** (1.5 min) :

"Composition API organise le code par **logique métier**, Options API par **type** (data/methods/computed).

| Aspect | Composition API | Options API |
|--------|-----------------|-------------|
| Organisation | Par feature | Par type |
| Réutilisabilité | Composables | Mixins (outdated) |
| TypeScript | ✅ Excellent | ⚠️ Médiocre |
| Bundle size | ✅ Smaller | ⭐⭐ Plus gros |

**Exemple** :
```vue
<!-- Options API (ancien) -->
export default {
  data() { return { count: 0 } },
  computed: { doubleCount() { return this.count * 2 } },
  methods: { increment() { this.count++ } }
}

<!-- Composition API (nouveau) -->
<script setup>
const count = ref(0);
const doubleCount = computed(() => count.value * 2);
const increment = () => count.value++;
</script>
```

**Mon choix** : Le cahier imposait Composition API, et c'est mieux pour :
- Code lisible et maintenable
- Composables réutilisables (ex: `useMarkdown.js`)
- Tree-shaking du bundler
- 100% de mon code est en `<script setup>`"

---

### Q-THEO-6 : "ORM vs Raw SQL. Pourquoi utiliser Eloquent plutôt que du SQL brut ?"

**Réponse concise** (1.5 min) :

"Eloquent (ORM de Laravel) abstrait la BD et offre une API élégante en PHP.

| Aspect | Eloquent | Raw SQL |
|--------|----------|---------|
| Sécurité (SQL injection) | ✅ Prévient auto | ⚠️ Risk si mal fait |
| Readability | ✅ Fluent, lisible | ❌ Verbeux |
| Relationships | ✅ Automatique | ❌ Joins manuels |
| Portabilité | ✅ Change de DB facile | ❌ Syntax DB-spécifique |

**Exemple** :
```php
// Raw SQL - risqué et verbeux
$convs = DB::select('SELECT * FROM conversations WHERE user_id = ? AND title LIKE ?', 
    [$userId, "%$search%"]);

// Eloquent - sûr et lisible
$convs = Conversation::where('user_id', $userId)
    ->where('title', 'like', "%$search%")
    ->with('messages')
    ->get();
```

**Mon projet** : J'utilise Eloquent partout :
- `LlmModel::where('enabled', true)->get()`
- `Conversation::find($id)->with('messages')`
- Policies utilisent les models naturellement"

---

## 🎯 STRUCTURE RÉPONSE IDÉALE

```
1. Écouter LA QUESTION COMPLÈTE (laisser finir le prof)
2. Respirer (pause 2 sec)
3. Répondre structuré :
   - Phrase d'introduction (quoi)
   - Explication (pourquoi/comment)
   - Exemple concret (preuve)
   - Conclusion (lien au projet)
4. Arrêter (ne pas over-parler)
5. "Des questions ?"
```

---

## 🚫 À ÉVITER

- ❌ "Je sais pas" — Dire plutôt "C'est une bonne question, je dirais que..."
- ❌ Over-parler — Répondre en 1-2 min max par question
- ❌ Jargon sans explication — Expliquer les termes techniques
- ❌ "Je dois vérifier dans le code" — Si tu ne sais pas, admet-le
- ❌ Mentir — Le prof connaît Laravel/Vue, il va te chopper

---

## ✅ À FAIRE

- ✅ Parler calmement, avec confiance
- ✅ Utiliser le projet comme référence ("Dans mon code...")
- ✅ Mentionner les trade-offs ("J'ai choisi SSE plutôt que WebSocket parce que...")
- ✅ Admettre les limitations ("Je n'ai pas implémenté N-N car pas d'exigence, mais c'était possible")
- ✅ Montrer la maîtrise sans arrogance

---

## 🔥 POINTS FORTS À VALORISER

Glisse-les dans tes réponses naturellement :

1. **Architecture propre** : "J'ai séparé la logique dans une Service Layer (ChatService)"
2. **Sécurité** : "Chaque opération passe par une Policy d'autorisation"
3. **Performance** : "J'utilise Eager Loading avec `with()` pour éviter N+1"
4. **Streaming** : "J'ai géré le buffer des chunks partiels SSE"
5. **Tests** : "J'ai des tests Feature et Unit qui couvrent les cas d'autorisation"
6. **Git** : "74 commits avec progression logique"

---

## 🎬 SIMULATIONS À FAIRE

**Avant l'examen** :

1. Enregistre-toi répondant à chaque question (audio ou vidéo)
2. Écoute-toi — ajuste ton tempo, ta clarté
3. Demande à un copain de te poser les questions en random
4. Fais 3-4 runs complets (durée 15-20 min)

**Points de contrôle** :
- Parles-tu trop vite ou trop lentement ?
- Dis-tu "euh" / "uh" souvent ? (minimiser)
- Tes explications sont-elles claires pour quelqu'un qui connaît pas ton projet ?
- Peux-tu répondre sans regarder le code ?

---

## 📋 PREP CHECKLIST

- [ ] Connaître l'architecture par cœur (puisse la dessiner au tableau)
- [ ] Préparer réponses aux 6 questions théoriques
- [ ] Faire 3 simulations orales
- [ ] Connaitre mes chiffres : 74 commits, 58 composants, 20 tests
- [ ] Dormir bien la veille
- [ ] Arriver 10 min en avance
- [ ] Avoir le lien GitHub à disposition

---

## 📲 JOUR DE L'EXAMEN

**15 min avant** :
- [ ] Appelle sur le lien Zoom/présentiel
- [ ] Vérifie caméra, micro, connection internet
- [ ] Salue le prof professionnellement
- [ ] Demande "Vous êtes prêt ?" avant de commencer

**Durant** :
- [ ] Écoute les questions entièrement
- [ ] Pense avant de répondre (ok de dire "C'est une bonne question, un moment...")
- [ ] Parle calmement, articule
- [ ] Regarde la caméra (pas le code)

**Après** :
- [ ] Remercie le prof
- [ ] Dis au revoir
- [ ] Ferme proprement

---

**Créé le** : 1er juin 2026  
**À mémoriser** : 6 questions théoriques  
**Durée prep** : 2-3 jours (30 min/jour)  

**Tu vas bien le passer ! 💪🎤**
