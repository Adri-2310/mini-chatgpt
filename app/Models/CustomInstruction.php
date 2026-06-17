<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CustomInstruction Model
 *
 * Représente les instructions personnalisées d'un utilisateur pour l'IA
 * Ces instructions sont ajoutées au system prompt de chaque requête IA
 *
 * @property int $id
 * @property int $user_id
 * @property string $instructions Texte contenant les instructions personnalisées
 * @property bool $enabled Si les instructions sont actives ou non
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class CustomInstruction extends Model
{
    protected $fillable = ['user_id', 'instructions', 'enabled'];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    /**
     * Relation: L'utilisateur propriétaire des instructions
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
