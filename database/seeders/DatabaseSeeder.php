<?php

namespace Database\Seeders;

use App\Models\Alert;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       $this->call(RoleSeeder::class);
       $this->call(PermissionSeeder::class);
         Patient::factory()->count(50)->create();

         Appointment::factory()->count(80)->create();
         Alert::factory()->count(25)->create();
    }
}
