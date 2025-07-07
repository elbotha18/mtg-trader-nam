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
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'is_private',
                'is_foil',
                'is_borderless',
                'is_retro_frame',
                'is_etched_foil',
                'is_judge_promo_foil',
                'is_japanese_language',
                'is_signed_by_artist'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->boolean('is_private')->default(false)->after('user_id');
            $table->boolean('is_foil')->default(false)->after('is_private');
            $table->boolean('is_borderless')->default(false)->after('is_foil');
            $table->boolean('is_retro_frame')->default(false)->after('is_borderless');
            $table->boolean('is_etched_foil')->default(false)->after('is_retro_frame');
            $table->boolean('is_judge_promo_foil')->default(false)->after('is_etched_foil');
            $table->boolean('is_japanese_language')->default(false)->after('is_judge_promo_foil');
            $table->boolean('is_signed_by_artist')->default(false)->after('is_japanese_language');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
