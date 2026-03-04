<?php

namespace App\Services\Dashboard\User;

use Spatie\Permission\Models\Role;
use Exception;
use Illuminate\Support\Str;

class RoleService
{
    public function getAllRoles()
    {
        try {
            // dd(Role::all());
            return Role::all();
        } catch (Exception $e) {
            throw new Exception('Error fetching roles: ' . $e->getMessage());
        }
    }

    public function createRole(array $data)
    {
        try {
            return Role::create([
                'name' => $data['name'],
                'guard_name' => 'web',
                'slug' => Str::slug($data['name'], '_'),
                ]);
            if (!empty($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }
            return $role;
        } catch (Exception $e) {
            throw new Exception('Error creating role: ' . $e->getMessage());
        }
    }

    public function updateRole(Role $role, array $data)
    {
        try {
            $role->update([
                'name' => $data['name'],
                'slug' => Str::slug($data['name'], '_'),
                'guard_name' => 'web',
                ]);
            $permissions = $data['permissions'] ?? [];
            $role->syncPermissions($permissions);
            return $role;
        } catch (Exception $e) {
            throw new Exception('Error updating role: ' . $e->getMessage());
        }
    }

    public function deleteRole(Role $role)
    {
        try {
            $role->delete();
        } catch (Exception $e) {
            throw new Exception('Error deleting role: ' . $e->getMessage());
        }
    }
}