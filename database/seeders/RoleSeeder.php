<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $guard = config('auth.defaults.guard') ?? 'web';
        $roles = ['admin','nurse','chns','clerk'];

        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r, 'guard_name' => $guard]);
        }
    }
}
