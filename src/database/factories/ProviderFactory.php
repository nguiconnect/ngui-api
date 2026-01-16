<?php

namespace Database\Factories;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProviderFactory extends Factory
{
    protected $model = Provider::class;

    public function definition(): array
    {
        // Données "Grassfield" réalistes (Ouest + Nord-Ouest)
        $locations = [
            // OUEST
            ['region' => 'Ouest', 'department' => 'Mifi', 'city' => 'Bafoussam'],
            ['region' => 'Ouest', 'department' => 'Menoua', 'city' => 'Dschang'],
            ['region' => 'Ouest', 'department' => 'Noun', 'city' => 'Foumban'],
            ['region' => 'Ouest', 'department' => 'Ndé', 'city' => 'Bangangté'],
            ['region' => 'Ouest', 'department' => 'Hauts-Plateaux', 'city' => 'Bafang'],
            ['region' => 'Ouest', 'department' => 'Bamboutos', 'city' => 'Mbouda'],
            ['region' => 'Ouest', 'department' => 'Koung-Khi', 'city' => 'Bandjoun'],

            // NORD-OUEST
            ['region' => 'Nord-Ouest', 'department' => 'Mezam', 'city' => 'Bamenda'],
            ['region' => 'Nord-Ouest', 'department' => 'Bui', 'city' => 'Kumbo'],
            ['region' => 'Nord-Ouest', 'department' => 'Ngo-Ketunjia', 'city' => 'Ndop'],
            ['region' => 'Nord-Ouest', 'department' => 'Donga-Mantung', 'city' => 'Nkambe'],
            ['region' => 'Nord-Ouest', 'department' => 'Menchum', 'city' => 'Wum'],
            ['region' => 'Nord-Ouest', 'department' => 'Boyo', 'city' => 'Fundong'],
            ['region' => 'Nord-Ouest', 'department' => 'Momo', 'city' => 'Mbengwi'],
        ];

        $loc = $this->faker->randomElement($locations);

        // Catégories utilisées par ton front (ProvidersListPage.jsx)
        $category = $this->faker->randomElement([
            'sound_dj',
            'caterer_full',
            'hall_indoor',
            'hall_outdoor',
            'car_rental',
            'decoration',
            'funeral_service',
            'other',
        ]);

        // Prix réalistes (XAF) selon catégorie
        $startingPrice = match ($category) {
            'caterer_full'    => $this->faker->numberBetween(30000, 180000),
            'sound_dj'        => $this->faker->numberBetween(80000, 600000),
            'hall_indoor'     => $this->faker->numberBetween(150000, 1500000),
            'hall_outdoor'    => $this->faker->numberBetween(80000, 800000),
            'car_rental'      => $this->faker->numberBetween(25000, 250000),
            'decoration'      => $this->faker->numberBetween(50000, 900000),
            'funeral_service' => $this->faker->numberBetween(150000, 2500000),
            default           => $this->faker->numberBetween(20000, 350000),
        };

        // Rating compatible avec float(2,1) => max 9.9 (on garde 3.5 à 5.0)
        $rating = $this->faker->optional(0.9)->randomFloat(1, 3.5, 5.0);
        $ratingCount = $rating ? $this->faker->numberBetween(8, 420) : null;

        $phone = $this->faker->optional(0.9)->e164PhoneNumber(); // ex +237...
        $email = $this->faker->optional(0.85)->safeEmail();
        $whatsapp = ($phone && $this->faker->boolean(70)) ? $phone : null;

        // Mini descriptions "vraies"
        $descByCat = [
            'caterer_full'    => "Menus traditionnels & modernes, service pour cortèges et cérémonies. Options diaspora possibles.",
            'sound_dj'        => "Sono pro, DJ, micro sans fil, playlist cérémonie. Installation rapide.",
            'hall_indoor'     => "Salle couverte, chaises/tables, accès facile, parking. Idéal veillées & grandes réunions.",
            'hall_outdoor'    => "Espace plein air, tente/chapiteau possible, adapté aux grands événements.",
            'car_rental'      => "Location véhicules (berlines, 4x4, minibus). Chauffeur possible sur demande.",
            'decoration'      => "Décoration complète (fleurs, tissus, scène, table d’honneur). Thèmes personnalisés.",
            'funeral_service' => "Accompagnement funéraire (logistique, coordination, transport, assistance famille).",
            'other'           => "Prestations complémentaires (sécurité, photographie, éclairage, service divers).",
        ];

        // Services JSON (dans ta table tu as "services" en JSON)
        // On y met des infos utiles, même si le front ne les lit pas encore (ça sert pour plus tard).
        $services = [
            'diaspora_friendly' => $this->faker->boolean(45),
            'payment_methods'   => $this->faker->randomElements(['momo', 'orange_money', 'bank_transfer', 'cash'], $this->faker->numberBetween(1, 3)),
            'response_time'     => $this->faker->randomElement(['< 2h', '< 6h', '< 24h', '1-2 jours']),
            // Image placeholder stable par "seed" (utile si plus tard tu exposes image_url depuis le back)
            'image_url'         => "https://picsum.photos/seed/ngui-provider-" . $this->faker->unique()->numberBetween(1, 99999) . "/900/600",
        ];

        return [
            'name'          => $this->faker->company() . ' ' . $this->faker->randomElement(['Services', 'Prestige', 'Premium', 'Events', 'Plus', 'Express']),
            'category'      => $category,

            'region'        => $loc['region'],
            'department'    => $loc['department'],
            'city'          => $loc['city'],
            'address'       => $this->faker->optional(0.6)->streetAddress(),

            'starting_price' => $startingPrice,
            'currency'      => 'XAF',
            'rating'        => $rating,
            'rating_count'  => $ratingCount,

            'description'   => $descByCat[$category] ?? null,
            'services'      => json_encode($services, JSON_UNESCAPED_UNICODE),

            'phone'         => $phone,
            'email'         => $email,
            'whatsapp'      => $whatsapp,

            'created_at'    => now(),
            'updated_at'    => now(),
        ];
    }
}
