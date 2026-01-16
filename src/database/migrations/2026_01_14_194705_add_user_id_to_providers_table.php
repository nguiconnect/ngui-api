<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            if (!Schema::hasColumn('providers', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('users')
                    ->nullOnDelete();

                // Un provider account ne peut "claim" qu’un seul provider
                $table->unique('user_id');
            }
        });

        // Backfill soft (utile si tu étais en Option A avec ids égaux)
        // On ne le fait QUE pour les users role=provider et providers.id == users.id
        $providerUserIds = DB::table('users')
            ->where('role', 'provider')
            ->pluck('id')
            ->all();

        if (!empty($providerUserIds)) {
            DB::table('providers')
                ->whereNull('user_id')
                ->whereIn('id', $providerUserIds)
                ->update(['user_id' => DB::raw('id')]);
        }
    }

    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            if (Schema::hasColumn('providers', 'user_id')) {
                // drop FK puis unique puis colonne
                try {
                    $table->dropForeign(['user_id']);
                } catch (\Throwable $e) {
                }
                try {
                    $table->dropUnique(['user_id']);
                } catch (\Throwable $e) {
                }
                $table->dropColumn('user_id');
            }
        });
    }
};
