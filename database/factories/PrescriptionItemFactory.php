<?php

namespace Database\Factories;

use App\Models\Consultation;
use App\Models\Prescription;
use Hash;
use Illuminate\Database\Eloquent\Factories\Factory;


class PrescriptionItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word,
            'dosage' => fake()->randomElement(['10mg', '20mg', '50mg', '100mg', '5ml', '1 tablet', '2 tablets']),
            'frequency' => fake()->randomElement(['Once daily', 'Twice daily', 'Three times a day', 'Every 4 hours', 'As needed']),
            'duration' => fake()->randomElement(['7 days', '14 days', '1 month', '3 months', '6 months', 'Ongoing']),
            'prescription_id' => Prescription::factory(),
        ];
    }
}
