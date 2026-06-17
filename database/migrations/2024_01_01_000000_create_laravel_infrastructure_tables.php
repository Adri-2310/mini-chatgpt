<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée toute l'infrastructure technique de Laravel en une seule migration.
     *
     * Ces tables n'ont AUCUNE dépendance applicative (pas de FK vers users) et
     * sont regroupées car elles forment le socle technique du framework :
     *
     *   - sessions               : sessions HTTP serveur (SESSION_DRIVER=database)
     *   - cache / cache_locks     : cache clé/valeur + verrous atomiques (CACHE_STORE=database)
     *   - jobs / job_batches      : file d'attente asynchrone (QUEUE_CONNECTION=database)
     *   - password_reset_tokens   : tokens de réinitialisation de mot de passe (Fortify)
     *   - personal_access_tokens  : tokens API (Sanctum)
     *
     * Choix d'index :
     *   - sessions.user_id + last_activity : nettoyage des sessions expirées par utilisateur
     *   - cache.expiration / jobs.queue    : index requis par les drivers Laravel
     *   - tokens : UNIQUE pour empêcher toute collision de token
     */
    public function up(): void
    {
        // --- Sessions HTTP -----------------------------------------------------
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            // user_id nullable : sessions anonymes possibles (visiteurs non connectés)
            $table->foreignId('user_id')->nullable()->index();
            // 45 chars pour supporter IPv6
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // --- Cache -------------------------------------------------------------
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration')->index();
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration')->index();
        });

        // --- Queue -------------------------------------------------------------
        // Timestamps en entiers Unix (et non TIMESTAMP SQL) : convention Laravel Queue.
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->text('options')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        // --- Réinitialisation de mot de passe (Fortify) ------------------------
        // Clé primaire = email : un seul token actif par adresse. Token haché par Fortify.
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // --- Tokens API (Sanctum) ----------------------------------------------
        // Relation polymorphique : un token appartient à tout modèle HasApiTokens (User).
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
     * Supprime toutes les tables d'infrastructure (ordre inverse de création).
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
    }
};
