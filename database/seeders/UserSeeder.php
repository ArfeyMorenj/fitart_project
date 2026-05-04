<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 5 role utama sesuai userflow
        $users = [
            [
                'username' => 'superadmin',
                'name' => 'Super Admin',
                'email' => 'superadmin@fitart.co.id',
                'password' => Hash::make('superadmin123'),
                'role' => 'super_admin',
                'is_active' => true,
            ],
            [
                'username' => 'financeadmin',
                'name' => 'Finance Admin',
                'email' => 'financeadmin@fitart.co.id',
                'password' => Hash::make('financeadmin123'),
                'role' => 'finance_admin',
                'is_active' => true,
            ],
            [
                'username' => 'arcollector',
                'name' => 'AR Collector',
                'email' => 'arcollector@fitart.co.id',
                'password' => Hash::make('arcollector123'),
                'role' => 'ar_collector',
                'is_active' => true,
            ],
            [
                'username' => 'salesoperator',
                'name' => 'Sales Operator',
                'email' => 'salesoperator@fitart.co.id',
                'password' => Hash::make('salesoperator123'),
                'role' => 'sales_operator',
                'is_active' => true,
            ],
            [
                'username' => 'manager',
                'name' => 'Manager',
                'email' => 'manager@fitart.co.id',
                'password' => Hash::make('manager123'),
                'role' => 'manager',
                'is_active' => true,
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(['email' => $u['email']], $u);
        }

        // kompatibilitas: admin@fitart.co.id tetap ada, tapi role DISET jadi super_admin
        User::updateOrCreate(
            ['email' => 'admin@fitart.co.id'],
            [
                'username' => 'admin',
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );
    }
}