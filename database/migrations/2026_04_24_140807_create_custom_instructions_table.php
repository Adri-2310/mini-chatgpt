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
        // Table des instructions personnalisées : chaque user peut avoir 1 seule instruction
        // unique() : un user = une instruction (relation 1-to-1)
        // enabled : permet de désactiver temporairement l'instruction sans la supprimer
        // instructions : nullable pour permettre à l'user de ne pas en avoir
        Schema::create('custom_instructions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->longText('instructions')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_instructions');
    }
};
