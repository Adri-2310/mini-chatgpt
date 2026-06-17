<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * ConversationSeeder
 *
 * Peuple la table `conversations` avec des données de test réalistes
 * adaptées au contexte culinaire de SaveurIA.
 *
 * Idempotence : `firstOrCreate` sur (user_id, title) évite les doublons
 * si le seeder est relancé.
 *
 * Prérequis : UserSeeder doit avoir été exécuté avant ce seeder.
 *
 * Note sur model_used : la valeur stockée est l'identifiant court du modèle
 * tel qu'il apparaît dans la colonne `model_used` de la table `conversations`
 * (ex : 'openai/gpt-4o-mini'). Elle doit correspondre à un `model_id` valide
 * dans la table `llm_models` peuplée par LlmModelSeeder.
 */
class ConversationSeeder extends Seeder
{
    /**
     * Identifiants LLM correspondant aux model_id de la table llm_models.
     */
    private const MODEL_GPT4O_MINI    = 'openai/gpt-4o-mini';
    private const MODEL_CLAUDE_HAIKU  = 'anthropic/claude-3.5-haiku';
    private const MODEL_GEMINI_FLASH  = 'google/gemini-2.5-flash';

    /**
     * Crée les conversations de démonstration pour chaque utilisateur test.
     *
     * @return void
     */
    public function run(): void
    {
        $alice     = User::where('email', 'alice@example.com')->first();
        $professor = User::where('email', 'professor@example.com')->first();
        $chef      = User::where('email', 'chef@example.com')->first();

        if (! $alice) {
            $this->command->warn('UserSeeder doit être exécuté avant ConversationSeeder. Aucune conversation créée.');
            return;
        }

        // --- Conversations d'Alice (utilisatrice principale) ---
        $aliceConversations = [
            ['title' => 'Recettes faciles pour débutants',  'model_used' => self::MODEL_GPT4O_MINI],
            ['title' => 'Pâtes créatives et délicieuses',   'model_used' => self::MODEL_CLAUDE_HAIKU],
            ['title' => 'Cuisine sans gluten',              'model_used' => self::MODEL_GEMINI_FLASH],
            ['title' => 'Desserts gourmands de saison',     'model_used' => self::MODEL_GPT4O_MINI],
        ];

        foreach ($aliceConversations as $data) {
            Conversation::firstOrCreate(
                ['user_id' => $alice->id, 'title' => $data['title']],
                ['model_used' => $data['model_used']]
            );
        }

        // --- Conversations du professeur (évaluation) ---
        if ($professor) {
            $professorConversations = [
                ['title' => 'Évaluation : Recettes faciles pour débutants', 'model_used' => self::MODEL_GPT4O_MINI],
                ['title' => 'Test de l\'assistant SaveurIA',                'model_used' => self::MODEL_CLAUDE_HAIKU],
            ];

            foreach ($professorConversations as $data) {
                Conversation::firstOrCreate(
                    ['user_id' => $professor->id, 'title' => $data['title']],
                    ['model_used' => $data['model_used']]
                );
            }
        }

        // --- Conversations du chef (usage avancé) ---
        if ($chef) {
            $chefConversations = [
                ['title' => 'Techniques de pâtisserie avancées', 'model_used' => self::MODEL_GEMINI_FLASH],
                ['title' => 'Accords mets et vins',              'model_used' => self::MODEL_GPT4O_MINI],
            ];

            foreach ($chefConversations as $data) {
                Conversation::firstOrCreate(
                    ['user_id' => $chef->id, 'title' => $data['title']],
                    ['model_used' => $data['model_used']]
                );
            }
        }

        // --- Conversations de Bob (utilisateur test supplémentaire) ---
        $bob = User::where('email', 'bob@example.com')->first();
        if ($bob) {
            $bobConversations = [
                ['title' => 'Idées de repas rapides',            'model_used' => self::MODEL_GPT4O_MINI],
                ['title' => 'Nutrition et santé',               'model_used' => self::MODEL_CLAUDE_HAIKU],
                ['title' => 'Cuisines du monde',                'model_used' => self::MODEL_GEMINI_FLASH],
            ];

            foreach ($bobConversations as $data) {
                Conversation::firstOrCreate(
                    ['user_id' => $bob->id, 'title' => $data['title']],
                    ['model_used' => $data['model_used']]
                );
            }
        }
    }
}
