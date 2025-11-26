<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Permissions
        $permissions = [
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view patients',
            'create patients',
            'edit patients',
            'delete patients',
            'view appointments',
            'create appointments',
            'view reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Assign Permissions to Roles

        // Admin gets everything
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        // Midwife
        $midwife = Role::firstOrCreate(['name' => 'midwife']);
        $midwife->givePermissionTo([
            'view patients',
            'create patients',
            'edit patients',
            'view appointments',
            'create appointments',
        ]);

        // CHNS (Community Health Nurse)
        $chns = Role::firstOrCreate(['name' => 'chns']);
        $chns->givePermissionTo([
            'view patients',
            'create patients', // Maybe they can create but not edit?
            'view appointments',
        ]);
    }
}