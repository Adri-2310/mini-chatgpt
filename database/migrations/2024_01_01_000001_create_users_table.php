<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table users avec TOUS ses champs définitifs.
     *
     * Entité centrale : un utilisateur possède plusieurs conversations, une
     * instruction personnalisée (1-1) et une ligne de statistiques (1-1).
     *
     * Regroupe ici l'ensemble des champs ajoutés au fil du projet :
     *   - Authentification de base (Fortify)  : name, email, password, remember_token
     *   - Photo de profil (Jetstream)         : profile_photo_path (2048 chars pour URLs longues)
     *   - 2FA (Fortify)                        : two_factor_secret, _recovery_codes, _confirmed_at
     *   - Changement d'email sécurisé          : pending_email, pending_email_sent_at,
     *                                            pending_email_token
     *
     * Index :
     *   - email UNIQUE                         : identifiant de connexion
     *   - pending_email UNIQUE                 : empêche deux comptes de réserver le même email
     *   - pending_email_token UNIQUE           : token de vérification du changement d'email
     *   - (pending_email_sent_at, pending_email) : index composite pour purger
     *     efficacement les demandes de changement expirées (job planifié)
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Changement d'email sécurisé : nouvel email en attente de vérification.
            // pending_email UNIQUE : aucun autre compte ne peut réserver le même email.
            $table->string('pending_email')->nullable()->unique();
            $table->timestamp('pending_email_sent_at')->nullable();
            // Token à usage unique envoyé par mail pour confirmer le changement.
            $table->string('pending_email_token')->nullable()->unique();

            // 2FA Fortify : secret TOTP + codes de secours (chiffrés), date de confirmation.
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();

            $table->rememberToken();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->timestamps();

            // Index composite pour la purge des demandes de changement d'email expirées.
            $table->index(['pending_email_sent_at', 'pending_email']);
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
