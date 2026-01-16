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
        Schema::create('event_access_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')
                ->constrained()
                ->cascadeOnDelete();

            // public / restricted
            $table->string('access_mode')->default('public');

            // Listes JSON
            $table->json('allowed_roles')->nullable();    // ["family","chefferie","organizer"]
            $table->json('invited_emails')->nullable();   // ["mail@example.com", ...]
            $table->json('access_codes')->nullable();     // ["CODE123", "XXX", ...]

            // Options d’affichage
            $table->boolean('show_chat')->default(true);
            $table->boolean('show_condolences')->default(true);
            $table->boolean('public_replay')->default(false);

            // Note interne pour l’organisateur
            $table->text('note_organizer')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_access_settings');
    }
};
