<?php

namespace Database\Seeders;

use App\Models\CustomInstruction;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomInstructionSeeder extends Seeder
{
    public function run(): void
    {
        // Alice Dupont - Cuisinière amateur
        $alice = User::where('email', 'alice@example.com')->first();
        if ($alice) {
            CustomInstruction::where('user_id', $alice->id)->update([
                'instructions' => "Je suis une cuisinière amateur en France qui adore expérimenter en cuisine. Je cherche des recettes savoureuses mais faciles à réaliser. J'aime les plats réconfortants, les produits locaux, et je n'ai pas peur d'essayer de nouvelles techniques. Adapte tes réponses à un niveau cuisinier débutant-intermédiaire, avec des explications claires et des astuces pratiques.",
                'enabled' => true,
            ]);
        }

        // Professeur Martin - Évaluateur
        $professor = User::where('email', 'professor@example.com')->first();
        if ($professor) {
            CustomInstruction::where('user_id', $professor->id)->update([
                'instructions' => "Je suis un professeur qui évalue la qualité et la rigueur des recettes. Je cherche de l'exactitude, de la précision, et une bonne structure dans les explications. Les recettes doivent être vérifiées et scientifiquement justifiées. Fournis des détails techniques, des raisons culinaires, et évite les approximations. C'est pour un projet académique d'évaluation.",
                'enabled' => true,
            ]);
        }

        // Chef Éric Bernard - Professionnel
        $chef = User::where('email', 'chef@example.com')->first();
        if ($chef) {
            CustomInstruction::where('user_id', $chef->id)->update([
                'instructions' => "Je suis un chef professionnel avec expérience en gastronomie française et internationale. Je cherche des techniques avancées, des innovations culinaires, et des conseils sur la présentation et l'équilibre des saveurs. Traite-moi comme un collègue du métier — je comprends les termes techniques. Propose des idées créatives et des approches raffinées.",
                'enabled' => true,
            ]);
        }

        // Bob Leroy - Mangeur équilibré
        $bob = User::where('email', 'bob@example.com')->first();
        if ($bob) {
            CustomInstruction::where('user_id', $bob->id)->update([
                'instructions' => "Je suis quelqu'un qui veut manger sainement sans sacrifier le plaisir. Je suis busy et je cherche des recettes rapides (moins de 20 min), nutritives et délicieuses. J'aime les saveurs, les épices, et je veux comprendre pourquoi une recette est bonne pour la santé. Propose-moi des alternatives quand c'est possible (version express, version complète).",
                'enabled' => true,
            ]);
        }
    }
}
