<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        $admin = User::updateOrCreate(
            ['phone' => '1234567890'],
            [
                'name' => 'Admin User',
                'email' => 'admin@realman.com',
                'password' => Hash::make('password'),
                'is_approved' => true,
                'phone_verified' => true,
                'approved_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Create Manager User
        $manager = User::updateOrCreate(
            ['phone' => '1234567891'],
            [
                'name' => 'Manager User',
                'email' => 'manager@realman.com',
                'password' => Hash::make('password'),
                'is_approved' => true,
                'phone_verified' => true,
                'approved_at' => now(),
            ]
        );
        $manager->assignRole('manager');

        // Create Staff User
        $staff = User::updateOrCreate(
            ['phone' => '1234567892'],
            [
                'name' => 'Staff User',
                'email' => 'staff@realman.com',
                'password' => Hash::make('password'),
                'is_approved' => true,
                'phone_verified' => true,
                'approved_at' => now(),
            ]
        );
        $staff->assignRole('staff');
    }
}
