<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        $paymentDate = fake()->dateTime();

        return [
            'amount' => fake()->randomFloat(2, 100000, 5000000),
            'payment_method' => fake()->randomElement(['cash', 'transfer', 'card', 'check']),
            'status' => fake()->randomElement(['pending', 'confirmed', 'failed', 'refunded']),
            'payment_date' => $paymentDate,
            'due_date' => $paymentDate->modify('+30 days'),
            'month_year' => $paymentDate->format('Y-m'),
            'reference_code' => strtoupper(fake()->bothify('PAY-###-###')),
        ];
    }

    public function pending(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }

    public function confirmed(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'confirmed',
            ];
        });
    }
}
