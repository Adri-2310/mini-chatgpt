<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée les tables jobs et job_batches (QUEUE_CONNECTION=database dans .env).
     *
     * - jobs       : file d'attente des tâches asynchrones (envoi d'emails, etc.)
     * - job_batches : suivi des lots de jobs (Bus::batch())
     *
     * Les deux tables sont regroupées ici car elles constituent ensemble
     * l'infrastructure de queue et sont toujours activées ou désactivées ensemble.
     * Les timestamps sont des entiers Unix (non des TIMESTAMP SQL) : convention Laravel Queue.
     */
    public function up(): void
    {
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
    }

    /**
     * Supprime les tables jobs et job_batches.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
    }
};
