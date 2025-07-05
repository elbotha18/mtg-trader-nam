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
        Schema::table('cards', function (Blueprint $table) {
            $table->boolean('is_borderless')->default(false)->after('is_foil');
            $table->boolean('is_retro_frame')->default(false)->after('is_borderless');
            $table->boolean('is_etched_foil')->default(false)->after('is_retro_frame');
            $table->boolean('is_judge_promo_foil')->default(false)->after('is_etched_foil');
            $table->boolean('is_japanese_language')->default(false)->after('is_judge_promo_foil');
            $table->boolean('is_signed_by_artist')->default(false)->after('is_japanese_language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->dropColumn([
                'is_borderless',
                'is_retro_frame',
                'is_etched_foil',
                'is_judge_promo_foil',
                'is_japanese_language',
                'is_signed_by_artist',
            ]);
        });
    }
};
