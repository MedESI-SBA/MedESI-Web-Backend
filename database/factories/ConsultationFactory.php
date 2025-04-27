<?php

namespace Database\Factories;

use App\Models\Appointments;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\consultation>
 */
class ConsultationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "notes" => fake()->text(),
            "reorientation" => "reorientation",
            "doctor_id" => Doctor::query()->inRandomOrder()->get()[0]->id,
            "patient_id" => Patient::factory(),
        ];
    }

    public function createdfromAppointment() {
        return $this->state(function (array $attributes) {
            $appointment = Appointments::factory()->scheduled()->create()->id;
            return [
                "appointment_id" => $appointment,
            ];
        });
    }
}
