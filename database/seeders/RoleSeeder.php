<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed default application roles and representative users.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Kos',
                'email' => 'admin@kos.com',
                'role' => 'admin',
                'password' => 'password',
            ],
            [
                'name' => 'Accountant Kos',
                'email' => 'accountant@kos.com',
                'role' => 'accountant',
                'password' => 'password',
            ],
            [
                'name' => 'Staff Kos',
                'email' => 'staff@kos.com',
                'role' => 'staff',
                'password' => 'password',
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'role' => $data['role'],
                    'password' => bcrypt($data['password']),
                ]
            );
        }
    }
}
