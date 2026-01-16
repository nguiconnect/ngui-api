<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Provider;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        // ⚠️ On repart propre : supprime tous les prestataires + reset auto-increment
        // (utile pour éviter les doublons à chaque seed)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Provider::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ======================
        // 1) 3 prestataires "stars"
        // ======================
        $stars = [
            [
                'name' => 'Traiteur Royal Chefferies',
                'category' => 'caterer_full',
                'region' => 'Ouest',
                'department' => 'Hauts-Plateaux',
                'city' => 'Bafang',
                'address' => 'Centre-ville, axe principal',
                'starting_price' => 65000,
                'currency' => 'XAF',
                'rating' => 4.9,
                'rating_count' => 212,
                'description' => "Traiteur premium pour funérailles, mariages et grandes cérémonies. Option diaspora (WhatsApp + suivi).",
                'services' => json_encode([
                    'diaspora_friendly' => true,
                    'payment_methods' => ['momo', 'orange_money', 'bank_transfer', 'cash'],
                    'response_time' => '< 6h',
                    'image_url' => 'https://picsum.photos/seed/ngui-star-traiteur/900/600',
                ], JSON_UNESCAPED_UNICODE),
                'phone' => '+237690112233',
                'email' => 'contact@royalchefferies.cm',
                'whatsapp' => '+237690112233',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sono Prestige Grassfield',
                'category' => 'sound_dj',
                'region' => 'Nord-Ouest',
                'department' => 'Mezam',
                'city' => 'Bamenda',
                'address' => 'Commercial Avenue',
                'starting_price' => 180000,
                'currency' => 'XAF',
                'rating' => 4.8,
                'rating_count' => 167,
                'description' => "Sono pro + DJ + micros sans fil. Installation rapide pour veillées et retours.",
                'services' => json_encode([
                    'diaspora_friendly' => true,
                    'payment_methods' => ['momo', 'cash'],
                    'response_time' => '< 2h',
                    'image_url' => 'https://picsum.photos/seed/ngui-star-sono/900/600',
                ], JSON_UNESCAPED_UNICODE),
                'phone' => '+237655778899',
                'email' => 'booking@sonoprestige.cm',
                'whatsapp' => '+237655778899',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Manor Events Hall (Indoor)',
                'category' => 'hall_indoor',
                'region' => 'Ouest',
                'department' => 'Mifi',
                'city' => 'Bafoussam',
                'address' => 'Quartier administratif',
                'starting_price' => 450000,
                'currency' => 'XAF',
                'rating' => 4.7,
                'rating_count' => 98,
                'description' => "Grande salle couverte, tables/chaises, parking. Idéal grands rassemblements.",
                'services' => json_encode([
                    'diaspora_friendly' => false,
                    'payment_methods' => ['bank_transfer', 'cash'],
                    'response_time' => '< 24h',
                    'image_url' => 'https://picsum.photos/seed/ngui-star-hall/900/600',
                ], JSON_UNESCAPED_UNICODE),
                'phone' => '+237699445566',
                'email' => 'info@manorevents.cm',
                'whatsapp' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Provider::insert($stars);

        // ======================
        // 2) 40 prestataires réalistes
        // ======================
        Provider::factory()->count(40)->create();
    }
}
