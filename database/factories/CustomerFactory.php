<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name'         => fake()->firstName(),
            'last_name'          => fake()->lastName(),
            'email'              => fake()->unique()->safeEmail(),
            'phone'              => fake()->phoneNumber(),
            'password'           => Hash::make('password'),
            'state'              => 'enabled',
            'verified_email'     => true,
            'accepts_marketing'  => false,
            'tax_exempt'         => false,
            'currency'           => 'USD',
            'orders_count'       => 0,
            'total_spent'        => 0,
        ];
    }

    public function disabled(): static
    {
        return $this->state(['state' => 'disabled']);
    }
}
