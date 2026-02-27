<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $roles = [
            'admin',
            'manager',
            'reception',
            'kitchen_staff',
            'food_server',
            'inventory_manager',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
        $matrix = [
            'dashboard.view'         => [ 'admin', 'manager', 'reception', 'kitchen_staff', 'food_server', 'inventory_manager'],
            'dashboard.sales_report' => [ 'admin', 'manager'],
            'menu_category.view'   => ['admin', 'manager', 'reception', 'kitchen_staff', 'food_server'],
            'menu_category.create' => ['admin', 'manager'],
            'menu_category.update' => ['admin', 'manager'],
            'menu_category.delete' => ['admin', 'manager'],
            'menu.view'   => ['admin', 'manager', 'reception', 'kitchen_staff', 'food_server'],
            'menu.create' => ['admin', 'manager'],
            'menu.update' => ['admin', 'manager'],
            'menu.delete' => ['admin', 'manager'],  
            'order.view'          => ['admin', 'manager', 'reception', 'kitchen_staff', 'food_server'],
            'order.create'        => ['admin', 'manager', 'reception'],
            'order.update_status' => ['admin', 'manager', 'kitchen_staff', 'food_server'],
            'order.mark_paid'     => ['admin', 'manager', 'reception'],
            'order.view_completed'=> ['admin', 'manager', 'reception'],
            'order.delete'        => ['admin', 'manager'],
            'inventory.view'     => ['admin', 'manager', 'inventory_manager'],
            'inventory.create'   => ['admin', 'inventory_manager'],
            'inventory.update'   => ['admin', 'inventory_manager'],
            'inventory.restock'  => ['admin', 'inventory_manager'],
            'inventory.delete'   => ['admin', 'inventory_manager'],   
            'inventory.forecast' => ['admin', 'manager', 'inventory_manager'],
            'recipe.view'   => ['admin', 'manager', 'inventory_manager'],
            'recipe.update' => ['admin', 'inventory_manager'],
            'user.view'   => ['admin', 'manager'],
            'user.create' => ['admin', 'manager'],
            'user.update' => ['admin', 'manager'],
            'user.delete' => ['admin', 'manager'],
            'site_setting.view'   => ['admin' , 'manager'],
            'site_setting.update' => ['admin' , 'manager'],
            'qr_code.view' => ['admin', 'manager', 'reception'],
        ];
        foreach ($matrix as $permissionName => $assignedRoles) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);

            foreach ($assignedRoles as $roleName) {
                $role = Role::where('name', $roleName)->first();
                if ($role && !$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }
        }
    }
}