<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition()
    {
        $first = $this->faker->firstName;
        $last = $this->faker->lastName;

        // Ghana-style numbers: start with 02, 03, 024 etc.
        $phone = '0' . $this->faker->numerify('2########'); // simple sample

        return [
            'first_name' => $first,
            'last_name' => $last,
            'folder_no' => 'F-' . $this->faker->unique()->numerify('#####'),
            'phone' => $phone,
            'whatsapp' => $phone,
            'room' => $this->faker->randomElement(['1','2','3']),
            'next_of_kin_name' => $this->faker->name,
            'next_of_kin_phone' => '0' . $this->faker->numerify('2########'),
            'id_number' => $this->faker->bothify('GHA-####-####'),
            'hospital_number' => 'H-' . $this->faker->unique()->numerify('#####'),
            'next_review_date' => Carbon::today()->addDays($this->faker->numberBetween(0,14)),
            'address' => $this->faker->address,
            'complaints' => $this->faker->optional(0.6)->sentence(),
        ];
    }
}
