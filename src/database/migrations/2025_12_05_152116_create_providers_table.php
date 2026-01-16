<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('category')->nullable();   // Hôtel, Traiteur, Salle, Sono, etc.

            // Localisation
            $table->string('region')->nullable();
            $table->string('department')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();

            // Infos commerciales
            $table->unsignedInteger('starting_price')->nullable(); // ex: 2500
            $table->string('currency', 8)->default('XAF');
            $table->float('rating', 2, 1)->nullable();       // ex: 4.6
            $table->unsignedInteger('rating_count')->nullable(); // ex: 128

            // Texte libre (si tu en as besoin plus tard)
            $table->text('description')->nullable();
            $table->json('services')->nullable();

            // Contact
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('whatsapp')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('providers');
    }
};
