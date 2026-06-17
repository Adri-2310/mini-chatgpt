<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * UserSeeder
 *
 * Peuple la table `users` avec des comptes de test et de démonstration.
 *
 * Idempotence : `firstOrCreate` garantit qu'aucun doublon n'est créé
 * si le seeder est relancé (php artisan db:seed ou migrate:fresh --seed).
 *
 * Note : le hook `User::boot()` crée automatiquement un enregistrement
 * `CustomInstruction` associé à chaque nouvel utilisateur (via `created`).
 * Ce comportement est intentionnel et attendu ici.
 *
 * Mots de passe : tous définis à "password" pour faciliter les tests locaux.
 * NE JAMAIS utiliser ces comptes en production.
 */
class UserSeeder extends Seeder
{
    /**
     * Utilisateurs de test et de démonstration.
     *
     * @return void
     */
    public function run(): void
    {
        $users = [
            [
                'name'  => 'Alice Dupont',
                'email' => 'alice@example.com',
                'role'  => 'Utilisatrice principale (scénarios de recettes)',
            ],
            [
                'name'  => 'Professeur Martin',
                'email' => 'professor@example.com',
                'role'  => 'Évaluateur (validation du projet)',
            ],
            [
                'name'  => 'Chef Éric Bernard',
                'email' => 'chef@example.com',
                'role'  => 'Démo culinaire avancée',
            ],
            [
                'name'  => 'Bob Leroy',
                'email' => 'bob@example.com',
                'role'  => 'Utilisateur secondaire (tests)',
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name'              => $userData['name'],
                    'password'          => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
