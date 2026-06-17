<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table sessions (SESSION_DRIVER=database dans .env).
     *
     * Stocke les sessions HTTP côté serveur. Chaque navigateur connecté
     * possède une entrée identifiée par un ID aléatoire.
     *
     * Colonnes notables :
     *   - user_id      : nullable — sessions anonymes possibles (visiteurs non connectés)
     *   - ip_address   : 45 chars pour supporter IPv6 (::1 → 2001:db8::1)
     *   - last_activity : timestamp Unix — utilisé pour nettoyer les sessions expirées
     */
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Supprime la table sessions.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
