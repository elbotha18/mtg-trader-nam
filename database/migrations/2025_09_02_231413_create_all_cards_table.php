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
        Schema::create('all_cards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type_line')->nullable();
            $table->string('lang', 10)->default('en');
            $table->string('set', 20);
            $table->string('collector_number', 20);
            $table->string('image_url', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['name', 'lang', 'set', 'collector_number'], 'unique_card');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('all_cards');
    }
};
