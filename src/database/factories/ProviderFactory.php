<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProviderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'     => fake()->company(),
            'category' => fake()->randomElement(['hotel','traiteur','décorateur','sonorisation','location-salle']),
            'city'     => fake()->city(),
            'phone'    => fake()->phoneNumber(),
            'email'    => fake()->companyEmail(),
            'rating'   => fake()->optional(0.7)->randomFloat(1, 3, 5),
        ];
    }
}
