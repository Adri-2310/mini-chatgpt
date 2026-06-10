<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // Permet aux model events (boot hooks) de s'exécuter pendant le seeding
    // Nécessaire pour que User::boot() crée automatiquement CustomInstruction

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Données essentielles (même en production via Coolify)
        $this->call(LlmModelSeeder::class);

        // 2. Données de test et développement (local uniquement)
        // À ne pas exécuter en production
        $this->call(UserSeeder::class);
        $this->call(ConversationSeeder::class);
        $this->call(MessageSeeder::class);
    }
}
