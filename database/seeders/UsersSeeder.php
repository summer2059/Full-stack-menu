<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ],
            [
                'name' => 'Reception User',
                'email' => 'reception@example.com',
                'password' => bcrypt('password'),
                'role' => 'reception'
            ],
            [
                'name' => 'Kitchen Chief User',
                'email' => 'kitchen@example.com',
                'password' => bcrypt('password'),
                'role' => 'kitchen_chief'
            ],
            [
                'name' => 'Food Server User',
                'email' => 'server@example.com',
                'password' => bcrypt('password'),
                'role' => 'food_server'
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                ['name' => $userData['name'], 'password' => $userData['password']]
            );
            $user->assignRole($userData['role']);
        }

        $this->command->info('Users seeded successfully!');
    }
}
