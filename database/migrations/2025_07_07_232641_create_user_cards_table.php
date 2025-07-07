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
        Schema::create('user_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('card_id')->constrained()->onDelete('cascade');
            $table->boolean('is_private')->default(false);
            $table->boolean('is_foil')->default(false);
            $table->boolean('is_borderless')->default(false);
            $table->boolean('is_retro_frame')->default(false);
            $table->boolean('is_etched_foil')->default(false);
            $table->boolean('is_judge_promo_foil')->default(false);
            $table->boolean('is_japanese_language')->default(false);
            $table->boolean('is_signed_by_artist')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_cards');
    }
};
