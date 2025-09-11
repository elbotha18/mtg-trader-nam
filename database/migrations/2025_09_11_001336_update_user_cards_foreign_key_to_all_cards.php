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
        Schema::table('user_cards', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['card_id']);
            
            // Add new foreign key constraint pointing to all_cards table
            $table->foreign('card_id')->references('id')->on('all_cards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_cards', function (Blueprint $table) {
            // Drop the all_cards foreign key constraint
            $table->dropForeign(['card_id']);
            
            // Restore the original foreign key constraint to cards table
            $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade');
        });
    }
};
