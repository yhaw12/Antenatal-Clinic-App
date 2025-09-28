<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition()
    {
        $date = Carbon::today()->addDays($this->faker->numberBetween(0,7));
        $time = $this->faker->time('H:i');

        return [
            'patient_id' => Patient::factory(),
            'date' => $date->toDateString(),
            'time' => $time,
            'status' => 'scheduled',
            'notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }
}
