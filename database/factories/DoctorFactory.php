<?php

namespace Database\Factories;

use Date;
use Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor>
 */
class DoctorFactory extends Factory
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
            'phoneNumber' => fake()->phoneNumber(),
            'lastAvailableAt' => Date::now()->toString(),
        ];
    }
}
