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
        Schema::table('messages', function (Blueprint $table) {
            // Ajouter la FK vers llm_models pour la relation (Message 0..* -- 1 LlmModel)
            $table->foreignId('llm_model_id')
                ->nullable()
                ->after('conversation_id')
                ->constrained('llm_models')
                ->nullOnDelete();

            // Indexer pour les performances
            $table->index('llm_model_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['llm_model_id']);
            $table->dropColumn('llm_model_id');
        });
    }
};
