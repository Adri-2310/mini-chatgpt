<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStats extends Model
{
    protected $table = 'user_stats';

    protected $fillable = [
        'user_id',
        'total_messages',
        'total_conversations',
        'total_tokens',
        'monthly_cost',
        'monthly_messages',
        'last_activity_at',
        'stats_computed_at',
    ];

    protected $casts = [
        'monthly_cost' => 'float',
        'total_cost' => 'float',
        'last_activity_at' => 'datetime',
        'stats_computed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
