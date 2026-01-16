<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->string('title');               // Funérailles – Fam. Ndongo
            $table->string('type')->nullable();    // funeral | wedding | dowry | other

            // Localisation
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('location_name')->nullable(); // Salle, village, etc.

            // Dates
            $table->dateTime('start_at');          // début de l’évènement (ou de la diffusion)
            $table->dateTime('end_at')->nullable();

            // En direct / diffusion
            $table->boolean('is_live')->default(false);
            $table->string('live_url')->nullable(); // lien YouTube, Zoom, Ngui Connect…

            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
