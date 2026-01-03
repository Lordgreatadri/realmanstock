<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            // Dashboard
            'view-dashboard',
            'view-analytics',
            
            // Animals
            'view-animals',
            'create-animals',
            'edit-animals',
            'delete-animals',
            'manage-health-records',
            
            // Orders
            'view-orders',
            'create-orders',
            'edit-orders',
            'delete-orders',
            'process-orders',
            'cancel-orders',
            
            // Customers
            'view-customers',
            'create-customers',
            'edit-customers',
            'delete-customers',
            
            // Inventory
            'view-inventory',
            'create-inventory',
            'edit-inventory',
            'delete-inventory',
            
            // Processing
            'view-processing',
            'create-processing',
            'edit-processing',
            'manage-processing',
            
            // Reports
            'view-reports',
            'export-reports',
            
            // Users
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'approve-users',
            
            // Settings
            'view-settings',
            'edit-settings',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $manager = Role::create(['name' => 'manager']);
        $manager->givePermissionTo([
            'view-dashboard', 'view-analytics',
            'view-animals', 'create-animals', 'edit-animals', 'manage-health-records',
            'view-orders', 'create-orders', 'edit-orders', 'process-orders',
            'view-customers', 'create-customers', 'edit-customers',
            'view-inventory', 'create-inventory', 'edit-inventory',
            'view-processing', 'create-processing', 'edit-processing', 'manage-processing',
            'view-reports', 'export-reports',
        ]);

        $staff = Role::create(['name' => 'staff']);
        $staff->givePermissionTo([
            'view-dashboard',
            'view-animals', 'create-animals', 'edit-animals',
            'view-orders', 'create-orders', 'edit-orders',
            'view-customers', 'create-customers', 'edit-customers',
            'view-inventory',
            'view-processing',
        ]);

        $customer = Role::create(['name' => 'customer']);
        $customer->givePermissionTo([
            'view-orders',
            'create-orders',
        ]);
    }
}
