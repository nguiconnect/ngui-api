<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stays', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique()->nullable();

            // Localisation
            $table->string('region')->nullable();
            $table->string('department')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();

            // Infos commerciales
            $table->unsignedInteger('starting_price')->nullable(); // ex: 35000
            $table->string('currency', 8)->default('XAF');
            $table->float('rating', 2, 1)->nullable();       // ex: 4.6
            $table->unsignedInteger('rating_count')->nullable(); // ex: 128

            // Capacités
            $table->unsignedInteger('capacity_guests')->nullable(); // capacité totale
            $table->unsignedInteger('capacity_rooms')->nullable()->comment('nombre de chambres');

            // Texte libre
            $table->text('description')->nullable();
            $table->json('services')->nullable(); // ex: ["Petit-déjeuner","Réception"]

            // Contact
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('whatsapp')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stays');
    }
};
