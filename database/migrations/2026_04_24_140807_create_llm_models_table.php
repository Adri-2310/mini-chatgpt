<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Table des modèles LLM disponibles (GPT, Claude, Gemini, etc.)
        // Remplie par LlmModelSeeder - données de base qui ne changent pas
        // enabled : permet de désactiver un modèle sans le supprimer de la BD
        Schema::create('llm_models', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('provider');
            $table->string('model_id');
            $table->text('description')->nullable();
            $table->boolean('enabled')->default(true);
            $table->integer('max_tokens')->default(4096);
            $table->json('config')->nullable();
            $table->timestamps();
            $table->index('provider');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('llm_models');
    }
};
