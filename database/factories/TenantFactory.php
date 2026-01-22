<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TenantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'identity_type' => fake()->randomElement(['ktp', 'sim', 'passport']),
            'identity_number' => fake()->unique()->numerify('####################'),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'province' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'birth_date' => fake()->dateOfBirth(),
            'status' => fake()->randomElement(['active', 'inactive', 'suspended']),
        ];
    }
}
