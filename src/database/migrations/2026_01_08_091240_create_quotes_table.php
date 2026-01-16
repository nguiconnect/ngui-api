<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();

            // Le prestataire concerné
            $table->unsignedBigInteger('provider_id');

            // Coordonnées du demandeur
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            // Détails de la demande (texte libre)
            $table->text('details')->nullable();

            // Statut MVP
            $table->string('status')->default('pending'); // pending | contacted | closed etc.

            $table->timestamps();

            // (Optionnel) si tu as une table providers, tu peux activer la contrainte FK :
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
