<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LlmModel extends Model
{
    protected $fillable = ['name', 'provider', 'model_id', 'description', 'enabled', 'max_tokens', 'config'];

    protected $casts = [
        'enabled' => 'boolean',
        'config' => 'array',
    ];
}
