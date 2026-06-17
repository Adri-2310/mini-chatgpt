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
        Schema::create('user_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');

            $table->decimal('total_cost', 10, 6)->default(0);
            $table->unsignedInteger('total_messages')->default(0);
            $table->unsignedInteger('total_conversations')->default(0);
            $table->unsignedBigInteger('total_tokens')->default(0);

            $table->decimal('monthly_cost', 10, 6)->default(0);
            $table->unsignedInteger('monthly_messages')->default(0);

            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('stats_computed_at')->useCurrent();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_stats');
    }
};
