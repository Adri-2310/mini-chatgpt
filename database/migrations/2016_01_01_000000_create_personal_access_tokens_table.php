<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table personal_access_tokens (Laravel Sanctum).
     *
     * Gère les tokens API personnels pour l'authentification SPA et mobile.
     * Relation polymorphique : un token peut appartenir à n'importe quel modèle
     * implémentant HasApiTokens (ici : User).
     */
    public function up(): void
    {
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 80)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Supprime la table personal_access_tokens.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
