# 📊 Structure de la Base de Données

## Architecture

```
users (1)
  ├── conversations (N)
  │   └── messages (N)
  ├── custom_instructions (1)
  └── (relation teams)

llm_models (static)
```

## Tables

### `users`
Utilisateurs de l'application. Chaque user peut avoir :
- Plusieurs conversations
- Une instruction personnalisée
- Des messages via les conversations

### `conversations`
Conversations entre l'utilisateur et l'IA. Chaque conversation :
- Appartient à 1 user
- Contient plusieurs messages
- Stocke le modèle LLM utilisé

### `messages`
Messages individuels dans une conversation. Chaque message :
- Appartient à 1 conversation
- A un rôle : `user` ou `assistant`
- Stocke les tokens utilisés

### `custom_instructions`
Instructions personnalisées (1 par user) qui guident le comportement de l'IA.

### `llm_models`
Catalogue des modèles LLM disponibles (GPT, Claude, Gemini).
**⚠️ Données essentielles** - Ne pas supprimer !

## Seeders

### En développement/local
```bash
php artisan migrate:fresh --seed
```

Crée :
- 4 utilisateurs de test
  - `test@example.com` / `password` → données complètes
  - `professor@example.com` / `password` → pour tester
  - `chef@example.com` / `password` → utilisateur supplémentaire
  - `user1@example.com` / `password` → utilisateur supplémentaire
- Conversations et messages de test
- Modèles LLM de base

### En production (Coolify)
```bash
php artisan migrate
php artisan db:seed --class=LlmModelSeeder
```

⚠️ **Important** : Ne pas utiliser `--seed` en production (UserSeeder, ConversationSeeder, MessageSeeder ne doivent pas s'exécuter).

## Cascades de suppression

- `users` → `conversations` → `messages` (suppression en cascade)
- `users` → `custom_instructions` (suppression en cascade)

Si un user est supprimé, toutes ses données associées le sont aussi.

## Bonnes pratiques

1. **Ne jamais modifier** `llm_models` à la main → passe par les seeders
2. **Migrations en montant** (ne pas utiliser `refresh` en production)
3. **Ajouter un seeder** pour toute nouvelle donnée statique
4. **Commenter les migrations** pour expliquer les changements majeurs
