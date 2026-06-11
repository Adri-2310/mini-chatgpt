<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('llm_models')->insertOrIgnore([
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
                'name' => 'Gemini 2.5 Flash',
                'provider' => 'Google',
                'model_id' => 'google/gemini-2.5-flash',
                'description' => 'Modèle Gemini 2.5 Flash optimisé pour la vitesse.',
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

    public function down(): void
    {
        DB::table('llm_models')->where('provider', 'in', ['OpenAI', 'Google', 'Anthropic'])->delete();
    }
};
