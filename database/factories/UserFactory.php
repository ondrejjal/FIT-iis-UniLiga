<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'password_hash' => bcrypt('password'),
            'name' => fake()->firstName(),
            'surname' => fake()->lastName(),
            'phone_number' => fake()->optional()->phoneNumber(),
        ];
    }
}