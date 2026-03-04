<?php

namespace App\Services\Dashboard\User;

use App\Models\User;
use Exception;

class UserService
{
    public function getAllUsers()
    {
        try {
            return User::with('roles')->get();
        } catch (Exception $e) {
            throw new Exception('Error fetching users: ' . $e->getMessage());
        }
    }

    public function createUser(array $data): User
    {
        try {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => bcrypt($data['password']),
            ]);

            $user->assignRole($data['role']);

            if (!empty($data['permissions'])) {
                $user->syncPermissions($data['permissions']);
            }

            return $user;
        } catch (Exception $e) {
            throw new Exception('Error creating user: ' . $e->getMessage());
        }
    }

    public function updateUser(User $user, array $data): User
    {
        try {
            $user->name  = $data['name'];
            $user->email = $data['email'];

            if (!empty($data['password'])) {
                $user->password = bcrypt($data['password']);
            }

            $user->save();
            $user->syncRoles([$data['role']]);
            $user->syncPermissions($data['permissions'] ?? []);

            return $user;
        } catch (Exception $e) {
            throw new Exception('Error updating user: ' . $e->getMessage());
        }
    }

    public function deleteUser(User $user): void
    {
        try {
            $user->delete();
        } catch (Exception $e) {
            throw new Exception('Error deleting user: ' . $e->getMessage());
        }
    }
}