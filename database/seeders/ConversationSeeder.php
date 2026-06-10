<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConversationSeeder extends Seeder
{
    // Crée les conversations de test pour les utilisateurs
    // Test User : conversations variées sur les recettes
    // Professor : conversations pour tester l'app
    public function run(): void
    {
        $testUser = User::where('email', 'test@example.com')->first();

        if (!$testUser) {
            return;
        }

        // Créer aussi des conversations pour le professor
        $professor = User::where('email', 'professor@example.com')->first();

        $conversations = [
            [
                'title' => 'Recettes faciles pour débutants',
                'model_used' => 'gpt-4o-mini',
            ],
            [
                'title' => 'Pâtes créatives et délicieuses',
                'model_used' => 'claude-3.5-haiku',
            ],
            [
                'title' => 'Cuisine sans gluten',
                'model_used' => 'gemini-2.5-flash',
            ],
            [
                'title' => 'Desserts gourmands',
                'model_used' => 'gpt-4o-mini',
            ],
        ];

        foreach ($conversations as $conversation) {
            Conversation::create([
                'user_id' => $testUser->id,
                'title' => $conversation['title'],
                'model_used' => $conversation['model_used'],
            ]);
        }

        // Conversations pour le professor
        if ($professor) {
            $professorConversations = [
                [
                    'title' => 'Évaluation : Recettes faciles',
                    'model_used' => 'gpt-4o-mini',
                ],
                [
                    'title' => 'Test de chat avec Claude',
                    'model_used' => 'claude-3.5-haiku',
                ],
            ];

            foreach ($professorConversations as $conversation) {
                Conversation::create([
                    'user_id' => $professor->id,
                    'title' => $conversation['title'],
                    'model_used' => $conversation['model_used'],
                ]);
            }
        }
    }
}
