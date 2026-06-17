<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée les tables cache et cache_locks (CACHE_STORE=database dans .env).
     *
     * - cache       : stockage clé/valeur avec expiration (timestamp Unix)
     * - cache_locks : verrous atomiques pour éviter les race conditions (Cache::lock())
     *
     * Les deux tables sont regroupées ici car elles constituent ensemble
     * l'infrastructure de cache et sont toujours activées ou désactivées ensemble.
     */
    public function up(): void
    {
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
    }

    /**
     * Supprime les tables cache et cache_locks.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
    }
};
