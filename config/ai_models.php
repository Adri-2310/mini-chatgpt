<?php

return [
    'available' => [
        [
            'id' => 'openai/gpt-4o-mini',
            'name' => 'GPT-4o mini',
            'provider' => 'OpenAI',
        ],
        [
            'id' => 'google/gemini-2.5-flash',
            'name' => 'Gemini 2.5 Flash',
            'provider' => 'Google',
        ],
        [
            'id' => 'anthropic/claude-3.5-haiku',
            'name' => 'Claude 3.5 Haiku',
            'provider' => 'Anthropic',
        ],
    ],

    'default' => 'anthropic/claude-3.5-haiku',

    // Coûts par million de tokens (basé sur tarification 2024)
    'pricing' => [
        'openai/gpt-4o-mini' => [
            'input' => 0.15,    // $0.15 per 1M input tokens
            'output' => 0.60,   // $0.60 per 1M output tokens
        ],
        'google/gemini-2.5-flash' => [
            'input' => 0.075,   // $0.075 per 1M input tokens
            'output' => 0.30,   // $0.30 per 1M output tokens
        ],
        'anthropic/claude-3.5-haiku' => [
            'input' => 0.80,    // $0.80 per 1M input tokens
            'output' => 4.00,   // $4.00 per 1M output tokens
        ],
    ],
];
