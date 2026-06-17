<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class LlmModel extends Model
{
    protected $fillable = ['name', 'provider', 'model_id', 'description', 'enabled', 'max_tokens', 'config'];

    protected $casts = [
        'enabled' => 'boolean',
        'config' => 'array',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public static function getEnabled()
    {
        return Cache::remember('llm_models_enabled', 3600, function () {
            return static::where('enabled', true)->get();
        });
    }
}
