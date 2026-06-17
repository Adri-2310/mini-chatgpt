<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conversation>
 */
class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    // Crée une conversation avec un user automatique
    // Utile pour les tests qui ont besoin de relations
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => 'Nouvelle conversation',
            // Alignement sur les model_id du référentiel llm_models (voir migration create_llm_models_table)
            'model_used' => 'openai/gpt-4o-mini',
        ];
    }
}
