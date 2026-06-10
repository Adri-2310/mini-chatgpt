<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    // Crée les messages pour chaque conversation
    // Alterne entre user et assistant pour simuler une vraie conversation
    // Messages adaptés selon le titre de la conversation
    public function run(): void
    {
        $conversations = Conversation::all();

        $defaultMessageExchanges = [
            [
                'user' => 'Comment faire une pâte carbonara authentique ?',
                'assistant' => 'La carbonara authentique est une recette romaine simple. Voici les étapes : 1) Cuire 400g de spaghetti. 2) Mélanger 3 jaunes d\'œuf avec 100g de fromage Pecorino râpé. 3) Faire cuire 200g de guanciale coupé en dés. 4) Mélanger les pâtes chaudes avec le guanciale, puis ajouter le mélange d\'œuf. Important : ne pas laisser bouillir la sauce pour éviter que les œufs ne cuisent.',
            ],
            [
                'user' => 'Quels sont les ingrédients les plus importants ?',
                'assistant' => 'Les 4 ingrédients essentiels sont : 1) Spaghetti (ou autre pâte de forme similaire). 2) Œufs frais. 3) Fromage Pecorino Romano (pas de Parmesan). 4) Guanciale (jambon de joue italienne). Ces ingrédients ne doivent jamais être remplacés pour respecter l\'authenticité de la recette.',
            ],
            [
                'user' => 'Comment conserver le plat au chaud ?',
                'assistant' => 'La carbonara doit être servie immédiatement après préparation. Voici les conseils : 1) Préchauffer les assiettes. 2) Égoutter les pâtes au dernier moment. 3) Mélanger rapidement tous les ingrédients. 4) Servir sans attendre. Si vous attendez trop longtemps, la sauce risque de se casser.',
            ],
        ];

        foreach ($conversations as $conversation) {
            // Utiliser les messages par défaut pour la plupart
            $messageExchanges = $defaultMessageExchanges;

            // Messages spécifiques pour les conversations du professor
            if ($conversation->title === 'Évaluation : Recettes faciles') {
                $messageExchanges = [
                    [
                        'user' => 'Peux-tu me donner 3 recettes faciles pour un débutant ?',
                        'assistant' => 'Bien sûr ! Voici 3 recettes parfaites pour débuter : 1) Omelette basique - œufs, beurre, sel. 2) Pâtes à la sauce tomate - pâtes, tomate, ail, huile. 3) Salade de poulet - laitue, poulet cuit, vinaigrette. Ces recettes demandent peu d\'ingrédients et de technique.',
                    ],
                ];
            } elseif ($conversation->title === 'Test de chat avec Claude') {
                $messageExchanges = [
                    [
                        'user' => 'Coucou ! Comment tu t\'appelles ?',
                        'assistant' => 'Je suis SaveurIA, un assistant culinaire pour vous aider à trouver et préparer vos recettes préférées. Comment puis-je vous aider aujourd\'hui ?',
                    ],
                ];
            }

            foreach ($messageExchanges as $exchange) {
                Message::create([
                    'conversation_id' => $conversation->id,
                    'role' => 'user',
                    'content' => $exchange['user'],
                    'model' => $conversation->model_used,
                    'tokens_used' => rand(30, 100),
                ]);

                Message::create([
                    'conversation_id' => $conversation->id,
                    'role' => 'assistant',
                    'content' => $exchange['assistant'],
                    'model' => $conversation->model_used,
                    'tokens_used' => rand(100, 300),
                ]);
            }
        }
    }
}
