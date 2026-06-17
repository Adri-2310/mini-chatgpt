<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table users.
     *
     * Entité centrale de l'application : un utilisateur peut posséder plusieurs
     * conversations et une instruction personnalisée.
     *
     * Colonnes notables :
     *   - email_verified_at  : null tant que l'email n'est pas vérifié (Fortify)
     *   - profile_photo_path : chemin vers la photo de profil (2048 chars pour les URLs longues)
     *   - remember_token     : token de session persistante ("se souvenir de moi")
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Supprime la table users.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
