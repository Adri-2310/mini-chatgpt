<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LlmModelSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('llm_models')->insert([
            [
                'name' => 'GPT-4o mini',
                'provider' => 'OpenAI',
                'model_id' => 'openai/gpt-4o-mini',
                'description' => 'Modèle GPT-4o mini optimisé pour les réponses rapides.',
                'enabled' => true,
                'max_tokens' => 4096,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gemini 3 Flash Preview',
                'provider' => 'Google',
                'model_id' => 'google/gemini-3-flash-preview',
                'description' => 'Modèle Gemini 3 Flash optimisé pour la vitesse.',
                'enabled' => true,
                'max_tokens' => 8000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Claude 3.5 Haiku',
                'provider' => 'Anthropic',
                'model_id' => 'anthropic/claude-3.5-haiku',
                'description' => 'Modèle Claude 3.5 Haiku compact et efficace.',
                'enabled' => true,
                'max_tokens' => 8192,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
