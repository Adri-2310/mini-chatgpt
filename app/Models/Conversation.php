<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Conversation Model
 *
 * Représente une conversation entre un utilisateur et l'IA
 *
 * @property int $id
 * @property int $user_id
 * @property string $title Titre de la conversation
 * @property string $model_used Modèle LLM utilisé pour la première réponse
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'model_used'];

    /**
     * Relation: L'utilisateur propriétaire de la conversation
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation: Tous les messages de la conversation
     *
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
