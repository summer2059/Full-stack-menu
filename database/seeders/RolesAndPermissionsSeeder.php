<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ----- Permissions -----
        $permissions = [
            'manage menu',
            'create order',
            'view order',
            'update order status',
            'view reports'
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // ----- Roles -----
        $roles = [
            'admin' => Permission::all()->pluck('name')->toArray(),
            'reception' => ['create order', 'view order'],
            'kitchen_chief' => ['view order', 'update order status'],
            'food_server' => ['view order', 'update order status'],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }

        $this->command->info('Roles and permissions seeded successfully!');
    }
}
