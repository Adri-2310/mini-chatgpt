-- ============================================================================
-- SaveurIA - Schéma de Base de Données
-- ============================================================================
-- Généré le: 2026-06-17
-- Version: 1.0 (Refactor complet)
-- Description: Architecture complète de la base de données avec toutes les tables
--              et leurs relations. Voir migrations/ pour les commandes exécutées.
-- ============================================================================

-- ============================================================================
-- 1. INFRASTRUCTURE LARAVEL
-- ============================================================================

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint unsigned NULL,
  `ip_address` varchar(45) NULL,
  `user_agent` text NULL,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `expiration` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `expiration` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `queue` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` text NULL,
  `created_at` int NOT NULL,
  `finished_at` int NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(80) NOT NULL,
  `abilities` text NULL,
  `last_used_at` timestamp NULL,
  `expires_at` timestamp NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `tokenable_type_tokenable_id` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 2. ENTITÉS MÉTIER PRINCIPALES
-- ============================================================================

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL,
  `password` varchar(255) NOT NULL,
  `pending_email` varchar(255) NULL,
  `pending_email_sent_at` timestamp NULL,
  `pending_email_token` varchar(255) NULL,
  `two_factor_secret` text NULL,
  `two_factor_recovery_codes` text NULL,
  `two_factor_confirmed_at` timestamp NULL,
  `remember_token` varchar(100) NULL,
  `profile_photo_path` varchar(2048) NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `pending_email` (`pending_email`),
  UNIQUE KEY `pending_email_token` (`pending_email_token`),
  KEY `pending_email_sent_at_pending_email` (`pending_email_sent_at`,`pending_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Utilisateurs avec authentification Fortify, 2FA, Jetstream profile photo, changement email sécurisé';

CREATE TABLE `conversations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(255) NULL,
  `model_used` varchar(255) NOT NULL DEFAULT 'openai/gpt-4o-mini',
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  `deleted_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `conversations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Conversations IA appartenant à un utilisateur (soft deletes activé)';

CREATE TABLE `llm_models` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `provider` varchar(255) NOT NULL,
  `model_id` varchar(255) NOT NULL,
  `description` text NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `max_tokens` int NOT NULL DEFAULT 4096,
  `config` json NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `model_id` (`model_id`),
  KEY `provider` (`provider`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Référentiel des modèles LLM disponibles (OpenAI, Google, Anthropic) - données de référence';

CREATE TABLE `messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint unsigned NOT NULL,
  `llm_model_id` bigint unsigned NULL,
  `role` enum('user','assistant') NOT NULL,
  `content` longtext NOT NULL,
  `model` varchar(255) NULL COMMENT 'Identifiant brut du modèle envoyé à l''API',
  `tokens_used` int unsigned NULL,
  `cost_usd` decimal(8,6) NULL DEFAULT 0.00,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `conversation_id` (`conversation_id`),
  KEY `llm_model_id` (`llm_model_id`),
  CONSTRAINT `messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_llm_model_id_foreign` FOREIGN KEY (`llm_model_id`) REFERENCES `llm_models` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Messages d''une conversation avec suivi tokens et coûts USD';

CREATE TABLE `custom_instructions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `instructions` longtext NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `custom_instructions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Instruction système personnalisée 1-to-1 par utilisateur';

CREATE TABLE `user_stats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `total_messages` int unsigned NOT NULL DEFAULT 0,
  `total_conversations` int unsigned NOT NULL DEFAULT 0,
  `total_tokens` bigint unsigned NOT NULL DEFAULT 0,
  `monthly_cost` decimal(10,6) NOT NULL DEFAULT 0.00,
  `monthly_messages` int unsigned NOT NULL DEFAULT 0,
  `last_activity_at` timestamp NULL,
  `stats_computed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `user_stats_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Statistiques agrégées 1-to-1 par utilisateur (cache de calculs)';

-- ============================================================================
-- 3. RELATIONS ET CONTRAINTES
-- ============================================================================
--
-- Diagramme des relations :
--
--   users (1) ──< (N) conversations (1) ──< (N) messages
--     │                                          │
--     │ (1-1)                                    │ (N-1, nullable, nullOnDelete)
--     ├── custom_instructions                    └──> llm_models (1) ──< (N) messages
--     └── user_stats (1-1)
--
-- Cascade behavior :
--   - users → conversations : cascadeOnDelete (supprimer un user = supprimer ses conversations)
--   - conversations → messages : cascadeOnDelete (supprimer une conversation = supprimer ses messages)
--   - llm_models → messages : nullOnDelete (retirer un modèle = conserver les messages avec llm_model_id = NULL)
--   - users → custom_instructions : cascadeOnDelete (supprimer un user = supprimer son instruction)
--   - users → user_stats : cascadeOnDelete (supprimer un user = supprimer ses stats)

-- ============================================================================
-- 4. INDEX IMPORTANTS
-- ============================================================================
--
-- Sessions :
--   - user_id + last_activity : nettoyage des sessions expirées par utilisateur
--
-- Cache :
--   - expiration : purge des entrées expirées
--
-- Jobs/Job Batches :
--   - queue : routage des jobs
--
-- Users :
--   - email (UNIQUE) : identifiant de connexion
--   - pending_email (UNIQUE) : empêche deux comptes de réserver le même email
--   - pending_email_token (UNIQUE) : token de vérification changement email
--   - (pending_email_sent_at, pending_email) : purge des demandes expirées
--
-- Conversations :
--   - user_id : index automatique (FK)
--
-- LLM Models :
--   - name (UNIQUE) : libellé lisible
--   - model_id (UNIQUE) : identifiant technique API
--   - provider : filtrage par fournisseur
--
-- Messages :
--   - conversation_id : index automatique (FK)
--   - llm_model_id : index automatique (FK nullable)
--   - role (ENUM) : PAS d'index (2 valeurs = non sélectif 50%)

-- ============================================================================
-- 5. DONNÉES DE RÉFÉRENCE (LLM MODELS)
-- ============================================================================
--
-- La table llm_models est peuplée DIRECTEMENT par la migration (pas seeder).
-- Raison : données de référence essentielles au fonctionnement de l'app.
-- Modèles initiaux :
--
--   1. GPT-4o mini (OpenAI)         - model_id: openai/gpt-4o-mini
--   2. Gemini 2.5 Flash (Google)    - model_id: google/gemini-2.5-flash
--   3. Claude 3.5 Haiku (Anthropic) - model_id: anthropic/claude-3.5-haiku

INSERT INTO `llm_models` (name, provider, model_id, description, enabled, max_tokens, created_at, updated_at) VALUES
('GPT-4o mini', 'OpenAI', 'openai/gpt-4o-mini', 'Modèle GPT-4o mini d\'OpenAI, optimisé pour les réponses rapides et économiques.', 1, 4096, NOW(), NOW()),
('Gemini 2.5 Flash', 'Google', 'google/gemini-2.5-flash', 'Modèle Gemini 2.5 Flash de Google, optimisé pour la vitesse et le volume.', 1, 8000, NOW(), NOW()),
('Claude 3.5 Haiku', 'Anthropic', 'anthropic/claude-3.5-haiku', 'Modèle Claude 3.5 Haiku d\'Anthropic, compact et efficace pour les tâches courantes.', 1, 8192, NOW(), NOW());

-- ============================================================================
-- 6. PARTICULARITÉS DE CONCEPTION
-- ============================================================================
--
-- Soft deletes :
--   - Uniquement sur `conversations` : corbeille restaurable, historique préservé
--   - Les messages ne sont PAS soft-deleted (disparaissent avec leur conversation)
--
-- Timestamps :
--   - Toutes les tables métier ont created_at / updated_at
--   - Infrastructure Laravel a des timestamps particuliers (sessions.last_activity, etc.)
--
-- Types de colonnes :
--   - ENUM pour messages.role (constraint d'intégrité au niveau DB, valeurs fermées)
--   - DECIMAL(8,6) pour cost_usd (jusqu'à 99.999999 USD/message)
--   - DECIMAL(10,6) pour monthly_cost (jusqu'à 9999.999999 USD/mois)
--   - BIGINT pour total_tokens (peut dépasser 4 milliards)
--   - VARCHAR(2048) pour profile_photo_path (support URLs longues)
--
-- Changement d'email sécurisé (users) :
--   - pending_email : nouvel email en attente
--   - pending_email_sent_at : horodatage de la demande
--   - pending_email_token : token à usage unique
--   - 3 colonnes UNIQUE séparées : garantit aucune collision
--   - Index composite (pending_email_sent_at, pending_email) : purge efficace des demandes expirées
--
-- Absence de total_cost dans user_stats :
--   - Par conception : total_cost se dérive à la demande depuis messages.cost_usd
--   - Seuls monthly_cost/monthly_messages sont matérialisés (recalcul périodique)

-- ============================================================================
-- 7. MIGRATIONS EXÉCUTÉES
-- ============================================================================
--
-- 1. 2024_01_01_000000_create_laravel_infrastructure_tables
--    → sessions, cache, cache_locks, jobs, job_batches, password_reset_tokens, personal_access_tokens
--
-- 2. 2024_01_01_000001_create_users_table
--    → users avec tous les champs (Fortify, 2FA, Jetstream, changement email)
--
-- 3. 2024_01_01_000002_create_conversations_table
--    → conversations avec soft deletes
--
-- 4. 2024_01_01_000003_create_llm_models_table
--    → llm_models avec peuplement des 3 modèles
--
-- 5. 2024_01_01_000004_create_messages_table
--    → messages avec toutes colonnes (tokens, coûts, llm_model_id)
--
-- 6. 2024_01_01_000005_create_custom_instructions_table
--    → custom_instructions (1-to-1 user)
--
-- 7. 2024_01_01_000006_create_user_stats_table
--    → user_stats (1-to-1 user)
--
-- Commande pour recréer la base :
--   php artisan migrate:fresh --seed

-- ============================================================================
-- 8. NOTES DE DÉVELOPPEMENT
-- ============================================================================
--
-- - Architecture : Entièrement refactorisée le 2026-06-17
-- - Avant : 23 migrations fragmentées + bugs critiques
-- - Après : 7 migrations propres + consolidées
-- - Production : Prêt pour déploiement le 2026-06-22
-- - Seeders : 4 seeders applicatifs (UserSeeder, ConversationSeeder, MessageSeeder, CustomInstructionSeeder)
-- - LLM Models : Peuplés directement par migration (données de référence système)

-- ============================================================================
-- FIN DU SCHÉMA
-- ============================================================================
