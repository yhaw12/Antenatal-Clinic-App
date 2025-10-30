<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Alert;
use App\Models\User;
use Illuminate\Support\Str;

class AlertFactory extends Factory
{
    protected $model = Alert::class;

    public function definition()
    {
        return [
            'id' => Str::uuid(),
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['info','warning','critical']),
            'message' => $this->faker->sentence,
            'url' => null,
            'is_read' => false,
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
