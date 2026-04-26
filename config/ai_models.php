<?php

return [
    'available' => [
        [
            'id' => 'openai/gpt-4o-mini',
            'name' => 'GPT-4o mini',
            'provider' => 'OpenAI',
        ],
        [
            'id' => 'google/gemini-3-flash-preview',
            'name' => 'Gemini 3 Flash Preview',
            'provider' => 'Google',
        ],
        [
            'id' => 'anthropic/claude-3.5-haiku',
            'name' => 'Claude 3.5 Haiku',
            'provider' => 'Anthropic',
        ],
    ],

    'default' => 'anthropic/claude-3.5-haiku',
];
