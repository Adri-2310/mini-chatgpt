<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    // Crée les utilisateurs de test et développement
    // Tous les mots de passe sont "password" pour faciliter les tests
    public function run(): void
    {
        $users = [
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
            ],
            [
                'name' => 'Professor',
                'email' => 'professor@example.com',
                'password' => 'password',
            ],
            [
                'name' => 'Chef Demo',
                'email' => 'chef@example.com',
                'password' => 'password',
            ],
            [
                'name' => 'Utilisateur 1',
                'email' => 'user1@example.com',
                'password' => 'password',
            ],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make($user['password']),
                'email_verified_at' => now(),
            ]);
        }
    }
}
