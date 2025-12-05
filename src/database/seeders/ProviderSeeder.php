<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Provider;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        // Si tu as une factory: dé-commente la ligne suivante
        // Provider::factory()->count(40)->create();

        // Sinon: quelques enregistrements de démo
        Provider::insert([
            [
                'name' => 'Demo Traiteur',
                'category' => 'traiteur',
                'city' => 'Walsall',
                'phone' => '01234 567890',
                'email' => 'demo@traiteur.com',
                'rating' => 4.3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hotel Test',
                'category' => 'hotel',
                'city' => 'Wolverhampton',
                'phone' => '01234 111222',
                'email' => 'hotel@test.com',
                'rating' => 4.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
