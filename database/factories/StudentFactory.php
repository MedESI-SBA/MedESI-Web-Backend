<?php

namespace Database\Factories;

use Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'familyName' => fake()->lastName(),
            'firstName' => fake()->firstName(),
            'email' => fake()->email(),
            'password' => Hash::make('password'),
            'age' => fake()->numberBetween(17,25),
            'phoneNumber' => fake()->phoneNumber(),
        ];
    }
}
