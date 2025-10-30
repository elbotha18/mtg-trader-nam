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
        // Add composite index to user_cards to speed up whereHas queries filtering by is_private
        Schema::table('user_cards', function (Blueprint $table) {
            $table->index(['card_id', 'is_private'], 'user_cards_card_id_is_private_index');
        });

        // Add single-column indexes on frequently-filtered columns in all_cards
        Schema::table('all_cards', function (Blueprint $table) {
            $table->index('collector_number', 'all_cards_collector_number_index');
            $table->index('set', 'all_cards_set_index');
            $table->index('type_line', 'all_cards_type_line_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_cards', function (Blueprint $table) {
            $table->dropIndex('user_cards_card_id_is_private_index');
        });

        Schema::table('all_cards', function (Blueprint $table) {
            $table->dropIndex('all_cards_collector_number_index');
            $table->dropIndex('all_cards_set_index');
            $table->dropIndex('all_cards_type_line_index');
        });
    }
};
