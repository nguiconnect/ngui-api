<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            // Pour ton UI React
            $table->string('image_url')->nullable()->after('address');
            $table->boolean('diaspora_friendly')->default(false)->after('image_url');

            $table->json('payment_methods')->nullable()->after('services');

            $table->boolean('is_available_for_weekend')->default(true)->after('whatsapp');

            $table->unsignedInteger('capacity_guests')->nullable()->after('is_available_for_weekend');
            $table->unsignedInteger('capacity_events')->nullable()->after('capacity_guests');
        });
    }

    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropColumn([
                'image_url',
                'diaspora_friendly',
                'payment_methods',
                'is_available_for_weekend',
                'capacity_guests',
                'capacity_events',
            ]);
        });
    }
};
