<?php

namespace Database\Factories;

use App\Enums\AppoitmentCreator;
use App\Enums\AppoitmentStatus;
use App\Models\Appointments;
use App\Models\Doctor;
use App\Models\Patient;
use DateInterval;
use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Utils\getFreeTime;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointments>
 */
class AppointmentsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $days = rand(1, 10);
        return [
            "notes" => fake()->text(),
            "status" => AppoitmentStatus::REQUESTED,
            "createdBy" => AppoitmentCreator::PATIENT,
            "patient_id" => Patient::factory(),
            "requestedDate" => new DateTime()->modify("+$days days")->format("Y-m-d"),
        ];
    }

    public function scheduled(): static
    {

        return $this->state(function (array $attributes) {


            return [
                "status" => AppoitmentStatus::SCHEDULED,
                "doctor_id" => Doctor::query()->inRandomOrder()->get()[0]->id,
            ];
        })->afterMaking(function (Appointments $appointment) {
            $baseDateTime = new DateTime($appointment->requestedDate);

            $randomHour = rand(8, 15);
            $randomMinute = rand(0, 59);

            $interval = new DateInterval(sprintf('PT%uH%uM', $randomHour, $randomMinute));
            $baseDateTime->add($interval);


            $appointment->dateTime = $baseDateTime->format("Y-m-d H:i");
        });
    }
}
