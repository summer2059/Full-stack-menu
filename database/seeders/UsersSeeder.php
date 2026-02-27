<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Admin',
                'email'    => 'admin@restaurant.com',
                'password' => bcrypt('password'),
                'role'     => 'admin',
            ],
            [
                'name'     => 'Manager',
                'email'    => 'manager@restaurant.com',
                'password' => bcrypt('password'),
                'role'     => 'manager',
            ],
            [
                'name'     => 'Reception',
                'email'    => 'reception@restaurant.com',
                'password' => bcrypt('password'),
                'role'     => 'reception',
            ],
            [
                'name'     => 'Kitchen Staff',
                'email'    => 'kitchen@restaurant.com',
                'password' => bcrypt('password'),
                'role'     => 'kitchen_staff',
            ],
            [
                'name'     => 'Food Server',
                'email'    => 'server@restaurant.com',
                'password' => bcrypt('password'),
                'role'     => 'food_server',
            ],
            [
                'name'     => 'Inventory Manager',
                'email'    => 'inventory@restaurant.com',
                'password' => bcrypt('password'),
                'role'     => 'inventory_manager',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => $data['password'],
                ]
            );
            $user->syncRoles([$data['role']]);
        }
    }
}