<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Message Model
 *
 * Représente un message dans une conversation (utilisateur ou assistant)
 *
 * @property int $id
 * @property int $conversation_id
 * @property string $role 'user' ou 'assistant'
 * @property string $content Contenu du message
 * @property string $model Modèle LLM utilisé (pour les messages assistant)
 * @property int|null $tokens_used Nombre de tokens utilisés pour ce message
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Message extends Model
{
    use HasFactory;

    protected $fillable = ['conversation_id', 'role', 'content', 'model', 'tokens_used'];
    protected $touches = ['conversation'];

    /**
     * Relation: La conversation parent
     *
     * @return BelongsTo
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
