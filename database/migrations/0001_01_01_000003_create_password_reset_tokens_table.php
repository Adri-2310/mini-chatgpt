<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table password_reset_tokens (Laravel Fortify).
     *
     * Stocke les tokens temporaires pour la réinitialisation de mot de passe.
     * La clé primaire est l'email : un seul token actif par adresse à la fois.
     * Le token est stocké haché (bcrypt) par Fortify avant insertion.
     * Expiration configurée via config/auth.php (passwords.users.expire).
     */
    public function up(): void
    {
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Supprime la table password_reset_tokens.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
