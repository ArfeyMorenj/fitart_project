<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    public function definition()
    {
        return [
            'code' => 'CLI' . str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'status' => 'NON GROUP',
            'name' => fake()->company(),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'phone' => fake()->phoneNumber(),
            'npwp' => fake()->numerify('##.###.###.#-###.###'),
            'credit_term_days' => fake()->randomElement([0, 30, 60, 90]),
            'is_active' => true,
        ];
    }
}