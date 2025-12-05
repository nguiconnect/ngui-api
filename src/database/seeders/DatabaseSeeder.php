<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Provider;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Ajoute 20 prestataires fictifs
        Provider::factory()->count(20)->create();
    }
}
