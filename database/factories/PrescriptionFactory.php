<?php

namespace Database\Factories;

use App\Models\Consultation;
use Hash;
use Illuminate\Database\Eloquent\Factories\Factory;


class PrescriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'issueDate' => fake()->date(),
            'consultation_id' => Consultation::factory(),
        ];
    }
}
