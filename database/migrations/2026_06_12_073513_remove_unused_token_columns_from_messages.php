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
            $table->dropColumn(['input_tokens', 'output_tokens', 'cost_usd']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->integer('input_tokens')->nullable();
            $table->integer('output_tokens')->nullable();
            $table->decimal('cost_usd', 8, 6)->nullable()->default(0);
        });
    }
};
