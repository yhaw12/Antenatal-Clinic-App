<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        return [
            'patient_id' => Patient::factory(),
            'date' => Carbon::today()->toDateString(),
            'is_present' => $this->faker->boolean(70),
        ];
    }
}
