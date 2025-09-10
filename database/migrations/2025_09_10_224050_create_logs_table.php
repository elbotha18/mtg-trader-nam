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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action'); // e.g., 'created', 'updated', 'deleted', 'added_card', 'removed_card'
            $table->string('description')->nullable(); // Human-readable description
            $table->morphs('loggable'); // This creates loggable_type and loggable_id columns with index
            $table->json('old_values')->nullable(); // Store old values for updates
            $table->json('new_values')->nullable(); // Store new values for updates
            $table->json('metadata')->nullable(); // Additional context data
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            // Add indexes for better performance (morphs() already creates loggable index)
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
            
            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
