<?php

namespace Database\Seeders;

use App\Models\User;
use App\Utilities\Constant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'username' => 'super_admin',
                'password' => 'superadmin123',
                'role' => Constant::USER_ROLE_SUPER_ADMIN,
                'status' => Constant::USER_STATUS_ACTIVE,
            ],
            [
                'username' => 'admin',
                'password' => 'admin123',
                'role' => Constant::USER_ROLE_ADMIN,
                'status' => Constant::USER_STATUS_ACTIVE,
            ],
            [
                'username' => 'finance',
                'password' => 'finance123',
                'role' => Constant::USER_ROLE_FINANCE,
                'status' => Constant::USER_STATUS_ACTIVE,
            ],
            [
                'username' => 'warehouse',
                'password' => 'warehouse123',
                'role' => Constant::USER_ROLE_WAREHOUSE,
                'status' => Constant::USER_STATUS_ACTIVE,
            ],
        ];

        foreach ($users as $user) {
            User::create([
                'username' => $user['username'],
                'password' => Hash::make($user['password']),
                'role' => $user['role'],
                'status' => $user['status'],
            ]);
        }
    }
}
