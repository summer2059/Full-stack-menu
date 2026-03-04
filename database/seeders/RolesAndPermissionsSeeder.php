<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | Create Roles
        |--------------------------------------------------------------------------
        */

        $roleNames = [
            'admin',
            'manager',
            'reception',
            'kitchen_staff',
            'food_server',
            'inventory_manager',
        ];

        foreach ($roleNames as $roleName) {

    Role::firstOrCreate(
        [
            'name' => $roleName,
        ],
        [
            'guard_name' => 'web',
            'slug'       => Str::slug($roleName, '_'),
        ]
    );
}

        // Get role IDs (keeping your structure)
        $adminID            = Role::where('name', 'admin')->value('id');
        $managerID          = Role::where('name', 'manager')->value('id');
        $receptionID        = Role::where('name', 'reception')->value('id');
        $kitchenStaffID     = Role::where('name', 'kitchen_staff')->value('id');
        $foodServerID       = Role::where('name', 'food_server')->value('id');
        $inventoryManagerID = Role::where('name', 'inventory_manager')->value('id');

        $allRoles       = [$adminID, $managerID, $receptionID, $kitchenStaffID, $foodServerID, $inventoryManagerID];
        $adminManager   = [$adminID, $managerID];
        $adminInventory = [$adminID, $inventoryManagerID];
        $frontOfHouse   = [$adminID, $managerID, $receptionID];
        $kitchenVisible = [$adminID, $managerID, $receptionID, $kitchenStaffID, $foodServerID];

        /*
        |--------------------------------------------------------------------------
        | Permission Mapping (YOUR STRUCTURE KEPT)
        |--------------------------------------------------------------------------
        */

        $permissions = [

            'dashboard' => [
                101 => ['name' => 'dashboard.view',         'role_id' => $allRoles],
                102 => ['name' => 'dashboard.sales_report', 'role_id' => $adminManager],
            ],

            'menu_category' => [
                201 => ['name' => 'menu_category.view',   'role_id' => $kitchenVisible],
                202 => ['name' => 'menu_category.create', 'role_id' => $adminManager],
                203 => ['name' => 'menu_category.update', 'role_id' => $adminManager],
                204 => ['name' => 'menu_category.delete', 'role_id' => $adminManager],
            ],

            'menu' => [
                211 => ['name' => 'menu.view',   'role_id' => $kitchenVisible],
                212 => ['name' => 'menu.create', 'role_id' => $adminManager],
                213 => ['name' => 'menu.update', 'role_id' => $adminManager],
                214 => ['name' => 'menu.delete', 'role_id' => $adminManager],
            ],

            'order' => [
                301 => ['name' => 'order.view',           'role_id' => $kitchenVisible],
                302 => ['name' => 'order.create',         'role_id' => $frontOfHouse],
                303 => ['name' => 'order.update_status',  'role_id' => [$adminID, $managerID, $kitchenStaffID, $foodServerID]],
                304 => ['name' => 'order.mark_paid',      'role_id' => $frontOfHouse],
                305 => ['name' => 'order.view_completed', 'role_id' => $frontOfHouse],
                306 => ['name' => 'order.delete',         'role_id' => $adminManager],
            ],

            'inventory' => [
                401 => ['name' => 'inventory.view',     'role_id' => [$adminID, $managerID, $inventoryManagerID]],
                402 => ['name' => 'inventory.create',   'role_id' => $adminInventory],
                403 => ['name' => 'inventory.update',   'role_id' => $adminInventory],
                404 => ['name' => 'inventory.restock',  'role_id' => $adminInventory],
                405 => ['name' => 'inventory.delete',   'role_id' => $adminInventory],
                406 => ['name' => 'inventory.forecast', 'role_id' => [$adminID, $managerID, $inventoryManagerID]],
            ],

            'recipe' => [
                501 => ['name' => 'recipe.view',   'role_id' => [$adminID, $managerID, $inventoryManagerID]],
                502 => ['name' => 'recipe.update', 'role_id' => $adminInventory],
            ],

            'user' => [
                601 => ['name' => 'user.view',   'role_id' => $adminManager],
                602 => ['name' => 'user.create', 'role_id' => $adminManager],
                603 => ['name' => 'user.update', 'role_id' => $adminManager],
                604 => ['name' => 'user.delete', 'role_id' => $adminManager],
            ],

            'role' => [
                611 => ['name' => 'role.view',   'role_id' => $adminManager],
                612 => ['name' => 'role.create', 'role_id' => $adminManager],
                613 => ['name' => 'role.update', 'role_id' => $adminManager],
                614 => ['name' => 'role.delete', 'role_id' => $adminManager],
            ],

            'site_setting' => [
                701 => ['name' => 'site_setting.view',   'role_id' => $adminManager],
                702 => ['name' => 'site_setting.update', 'role_id' => $adminManager],
            ],

            'qr_code' => [
                801 => ['name' => 'qr_code.view', 'role_id' => $frontOfHouse],
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Create Permissions + Assign to Roles (FIXED SECTION)
        |--------------------------------------------------------------------------
        */

        foreach ($permissions as $group => $groupPermissions) {
            foreach ($groupPermissions as $code => $permissionData) {

                $permission = Permission::firstOrCreate([
                    'name'       => $permissionData['name'],
                    'guard_name' => 'web',
                ]);

                // Optional: if your permissions table has group & code columns
                if (Schema::hasColumn('permissions', 'group')) {
                    $permission->update([
                        'group' => $group,
                        'code'  => $code,
                    ]);
                }

                foreach ($permissionData['role_id'] as $roleId) {

                    $role = Role::find($roleId);

                    if ($role) {
                        $role->givePermissionTo($permission);
                    }
                }
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}