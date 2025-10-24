<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            'admin',
            'reception',
            'kitchen_chief',
            'food_server',
            'inventory_manager'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
        $permissions = [
            'menu_category' => [
                101 => ['name' => 'menu_category.create', 'roles' => ['admin']],
                102 => ['name' => 'menu_category.view', 'roles' => ['admin', 'reception', 'kitchen_chief']],
                103 => ['name' => 'menu_category.update', 'roles' => ['admin']],
                104 => ['name' => 'menu_category.delete', 'roles' => ['admin']],
            ],
            'menu' => [
                201 => ['name' => 'menu.create', 'roles' => ['admin']],
                202 => ['name' => 'menu.view', 'roles' => ['admin', 'reception', 'kitchen_chief', 'food_server']],
                203 => ['name' => 'menu.update', 'roles' => ['admin']],
                204 => ['name' => 'menu.delete', 'roles' => ['admin']],
            ],
            'order' => [
                301 => ['name' => 'order.create', 'roles' => ['admin', 'reception']],
                302 => ['name' => 'order.view', 'roles' => ['admin', 'reception', 'kitchen_chief', 'food_server']],
                303 => ['name' => 'order.update.status', 'roles' => ['admin', 'kitchen_chief', 'food_server']],
                304 => ['name' => 'order.delete', 'roles' => ['admin']],
            ],
            'user' => [
                401 => ['name' => 'user.create', 'roles' => ['admin']],
                402 => ['name' => 'user.view', 'roles' => ['admin']],
                403 => ['name' => 'user.update', 'roles' => ['admin']],
                404 => ['name' => 'user.delete', 'roles' => ['admin']],
            ],
            'inventory' => [
                501 => ['name' => 'inventory.create', 'roles' => ['admin', 'inventory_manager']],
                502 => ['name' => 'inventory.view', 'roles' => ['admin', 'inventory_manager']],
                503 => ['name' => 'inventory.update', 'roles' => ['admin', 'inventory_manager']],
                504 => ['name' => 'inventory.delete', 'roles' => ['admin']],
            ],
            'pos' => [
                601 => ['name' => 'pos.create', 'roles' => ['admin', 'reception']],
                602 => ['name' => 'pos.view', 'roles' => ['admin', 'reception']],
                603 => ['name' => 'pos.update', 'roles' => ['admin']],
                604 => ['name' => 'pos.delete', 'roles' => ['admin']],
            ],
            'site_setting' => [
                701 => ['name' => 'site_setting.update', 'roles' => ['admin']],
                702 => ['name' => 'site_setting.view', 'roles' => ['admin']],
            ],
        ];

        foreach ($permissions as $module => $items) {
            foreach ($items as $code => $perm) {
                $permission = Permission::firstOrCreate(['name' => $perm['name']]);
                foreach ($perm['roles'] as $roleName) {
                    $role = Role::where('name', $roleName)->first();
                    if ($role && !$role->hasPermissionTo($permission)) {
                        $role->givePermissionTo($permission);
                    }
                }
            }
        }

        $this->command->info('✅ All roles and permissions seeded successfully.');
    }
}
