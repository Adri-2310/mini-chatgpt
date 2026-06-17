<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Seeder;

/**
 * MessageSeeder
 *
 * Peuple la table `messages` avec des échanges de démonstration réalistes
 * pour chaque conversation créée par ConversationSeeder.
 *
 * Idempotence : vérifie l'absence de messages existants avant d'en insérer
 * pour chaque conversation. Permet de relancer sans créer de doublons.
 *
 * Prérequis : ConversationSeeder (et donc UserSeeder) doit avoir été exécuté.
 *
 * Structure d'un échange : paire [role: user, role: assistant] consécutive.
 * Les tokens_used sont des estimations fixes (non aléatoires) pour garantir
 * la reproductibilité des données de test.
 * cost_usd est laissé à NULL car il est calculé par l'application au moment
 * d'un vrai appel API ; les données de seed ne génèrent pas de coût réel.
 */
class MessageSeeder extends Seeder
{
    /**
     * Échanges par défaut (conversation sur les pâtes carbonara).
     * Utilisés pour toutes les conversations sans scénario spécifique.
     */
    private const EXCHANGES_DEFAULT = [
        [
            'user'      => 'Comment faire une pâte carbonara authentique ?',
            'assistant' => "La vraie carbonara romaine ne contient ni crème ni lardons. "
                . "Voici la recette : (1) Cuire 400 g de spaghetti al dente. "
                . "(2) Fouetter 3 jaunes d'œuf avec 100 g de Pecorino Romano râpé et du poivre noir. "
                . "(3) Faire rissoler 200 g de guanciale en dés jusqu'à ce qu'il soit croustillant. "
                . "(4) Hors du feu, mélanger les pâtes égouttées avec le guanciale, puis incorporer "
                . "le mélange œuf-fromage en remuant vivement. L'émulsion se forme grâce à la chaleur "
                . "résiduelle — ne jamais remettre sur le feu pour éviter que les œufs ne coagulent.",
            'user_tokens'      => 14,
            'assistant_tokens' => 128,
        ],
        [
            'user'      => 'Peut-on remplacer le guanciale par du bacon ?',
            'assistant' => "Techniquement oui, mais c'est un compromis : le guanciale (joue de porc) "
                . "apporte un gras fondant et une saveur légèrement sucrée irremplaçable. La pancetta "
                . "est le substitut le plus proche ; le bacon (fumé) modifie le profil aromatique. "
                . "Pour une carbonara authentique, privilégiez le guanciale disponible en épicerie italienne.",
            'user_tokens'      => 13,
            'assistant_tokens' => 88,
        ],
    ];

    /**
     * Échanges spécifiques indexés par titre de conversation.
     */
    private const EXCHANGES_BY_TITLE = [
        'Évaluation : Recettes faciles pour débutants' => [
            [
                'user'      => 'Peux-tu me donner 3 recettes faciles pour un débutant absolu ?',
                'assistant' => "Bien sûr ! Voici 3 recettes accessibles sans matériel spécial : "
                    . "(1) Omelette — 3 œufs, 10 g de beurre, sel, poivre : 5 minutes. "
                    . "(2) Pâtes sauce tomate — pâtes cuites al dente, sauce tomate du commerce, "
                    . "1 gousse d'ail, basilic frais, filet d'huile d'olive. "
                    . "(3) Salade de poulet rôti — blanc de poulet cuit, laitue, tomates cerises, "
                    . "vinaigrette maison (huile + vinaigre + moutarde). "
                    . "Chaque recette demande moins de 15 minutes et initie aux gestes fondamentaux.",
                'user_tokens'      => 16,
                'assistant_tokens' => 110,
            ],
            [
                'user'      => 'Quel matériel minimum dois-je avoir dans ma cuisine ?',
                'assistant' => "Le kit de survie culinaire : (1) une poêle antiadhésive 24 cm, "
                    . "(2) une casserole 2 L, (3) un couteau de chef 20 cm + planche à découper, "
                    . "(4) une passoire, (5) un bol de préparation. "
                    . "Avec cet équipement vous pouvez réaliser plus de 80 % des recettes du quotidien.",
                'user_tokens'      => 12,
                'assistant_tokens' => 76,
            ],
        ],
        "Test de l'assistant SaveurIA" => [
            [
                'user'      => 'Bonjour ! Que sais-tu faire exactement ?',
                'assistant' => "Bonjour ! Je suis SaveurIA, votre assistant culinaire. "
                    . "Je peux vous aider à : trouver des recettes selon vos ingrédients disponibles, "
                    . "adapter une recette (sans gluten, végétalien, allergies…), "
                    . "expliquer des techniques culinaires, "
                    . "suggérer des accords mets-vins, "
                    . "et estimer les valeurs nutritionnelles d'un plat. "
                    . "Par où souhaitez-vous commencer ?",
                'user_tokens'      => 11,
                'assistant_tokens' => 84,
            ],
        ],
        'Techniques de pâtisserie avancées' => [
            [
                'user'      => 'Comment réussir un caramel à sec sans cristallisation ?',
                'assistant' => "La cristallisation survient quand des cristaux de saccharose s'accrochent "
                    . "aux parois. Pour l'éviter : (1) utilisez une casserole propre et sèche, "
                    . "(2) ne remuez jamais le sucre — faites pivoter la casserole à la place, "
                    . "(3) ajoutez quelques gouttes de jus de citron (acide inverti le sucre), "
                    . "(4) déglacez avec la crème chaude (pas froide) pour éviter le choc thermique. "
                    . "Cible : 175–180 °C pour un caramel ambré profond.",
                'user_tokens'      => 13,
                'assistant_tokens' => 104,
            ],
        ],
        'Accords mets et vins' => [
            [
                'user'      => 'Quel vin servir avec un saumon grillé au beurre blanc ?',
                'assistant' => "Le beurre blanc (sauce acide et grasse) appelle un blanc sec avec "
                    . "de la vivacité et du gras. Premiers choix : Muscadet sur lie (Loire) — "
                    . "son acidité tranche le gras ; Chablis premier cru — minéralité et fraîcheur ; "
                    . "Pinot Gris d'Alsace vendanges tardives légères — rondeur qui complète le beurre. "
                    . "Température de service : 10–12 °C.",
                'user_tokens'      => 15,
                'assistant_tokens' => 92,
            ],
        ],
        'Idées de repas rapides' => [
            [
                'user'      => 'Je suis pressé ce midi, tu as des recettes rapides ?',
                'assistant' => "Bien sûr ! Voici 3 recettes rapides (moins de 15 minutes) : "
                    . "(1) Pâtes aglio e olio — spaghetti, ail, piment, huile d'olive, persil. "
                    . "(2) Omelette garnie — œufs, fromage, jambon, tomates. "
                    . "(3) Salade de poulet rôti — poulet du commerce, laitue, avocat, vinaigrette. "
                    . "Prêtes en un rien de temps !",
                'user_tokens'      => 12,
                'assistant_tokens' => 95,
            ],
            [
                'user'      => 'Et pour le soir, un repas équilibré mais pas trop long ?',
                'assistant' => "Parfait ! Voici une option équilibrée et rapide : "
                    . "Filet de poulet aux légumes (20 min) — poulet coupé en dés, brocoli, carottes, "
                    . "sauce soja légère. Accompagné de riz blanc ou pâtes. "
                    . "Cela couvre protéines, légumes et féculents. C'est savoureux, sain et gourmand !",
                'user_tokens'      => 16,
                'assistant_tokens' => 82,
            ],
        ],
        'Nutrition et santé' => [
            [
                'user'      => 'Comment manger sainement sans sacrifier le goût ?',
                'assistant' => "C'est la vraie question ! Voici les principes : "
                    . "(1) Utiliser des épices et herbes aromatiques (ail, persil, thym, curcuma). "
                    . "(2) Cuire en vapeur ou rôtir plutôt que frire. "
                    . "(3) Choisir des ingrédients de qualité — un bon tomate locale > tomate insipide. "
                    . "(4) Équilibrer protéines (viande, poisson, légumineuses), légumes colorés, et bons gras (olive). "
                    . "Résultat : des plats délicieux et nutritifs !",
                'user_tokens'      => 17,
                'assistant_tokens' => 104,
            ],
        ],
        'Cuisines du monde' => [
            [
                'user'      => 'Parle-moi de la cuisine asiatique, surtout thaï et vietnamienne',
                'assistant' => "La cuisine asiatique est un festival de saveurs ! "
                    . "Thaïlandaise : équilibre sucré-salé-acide-épicé. Pad Thai, curry rouge, Tom Yum. "
                    . "Clés : citronnelle, sauce de poisson, lait de coco. "
                    . "Vietnamienne : légère et fraîche. Pho, rouleaux de printemps, salade de papaye verte. "
                    . "Clés : herbes fraîches (menthe, coriandre), nuoc mam, vinaigre de riz. "
                    . "Toutes deux misent sur la fraîcheur et l'équilibre.",
                'user_tokens'      => 20,
                'assistant_tokens' => 112,
            ],
        ],
    ];

    /**
     * Insère les messages de démonstration pour chaque conversation.
     *
     * @return void
     */
    public function run(): void
    {
        $conversations = Conversation::all();

        if ($conversations->isEmpty()) {
            $this->command->warn('Aucune conversation trouvée. ConversationSeeder doit être exécuté avant MessageSeeder.');
            return;
        }

        foreach ($conversations as $conversation) {
            // Idempotence : on ne repeuple pas une conversation qui a déjà des messages.
            if ($conversation->messages()->exists()) {
                continue;
            }

            $exchanges = self::EXCHANGES_BY_TITLE[$conversation->title] ?? self::EXCHANGES_DEFAULT;

            $llmModel = \App\Models\LlmModel::where('model_id', $conversation->model_used)->first();

            foreach ($exchanges as $exchange) {
                Message::create([
                    'conversation_id' => $conversation->id,
                    'role'            => 'user',
                    'content'         => $exchange['user'],
                    'model'           => $conversation->model_used,
                    'llm_model_id'    => $llmModel?->id,
                    'tokens_used'     => $exchange['user_tokens'],
                    'cost_usd'        => $this->calculateCost($conversation->model_used, $exchange['user_tokens']),
                ]);

                Message::create([
                    'conversation_id' => $conversation->id,
                    'role'            => 'assistant',
                    'content'         => $exchange['assistant'],
                    'model'           => $conversation->model_used,
                    'llm_model_id'    => $llmModel?->id,
                    'tokens_used'     => $exchange['assistant_tokens'],
                    'cost_usd'        => $this->calculateCost($conversation->model_used, $exchange['assistant_tokens']),
                ]);
            }
        }
    }

    private function calculateCost(string $model, int $tokens): float
    {
        $costPerMillionTokens = match ($model) {
            'openai/gpt-4o-mini' => 0.075,
            'google/gemini-2.5-flash' => 0.0375,
            'anthropic/claude-3.5-haiku' => 0.80,
            default => 0.05,
        };

        return ($tokens / 1_000_000) * $costPerMillionTokens;
    }
}
