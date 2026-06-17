<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute le support de l'authentification à deux facteurs (2FA) à la table users.
     *
     * Colonnes ajoutées (requises par Laravel Fortify) :
     *   - two_factor_secret         : clé TOTP chiffrée pour l'application authenticator
     *   - two_factor_recovery_codes : codes de secours JSON chiffrés (accès d'urgence)
     *   - two_factor_confirmed_at   : timestamp de confirmation initiale (null = 2FA non activée)
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('two_factor_secret')->nullable()->after('password');
            $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes');
        });
    }

    /**
     * Supprime les colonnes 2FA de la table users.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_confirmed_at',
            ]);
        });
    }
};
