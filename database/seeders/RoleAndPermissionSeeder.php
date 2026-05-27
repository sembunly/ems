<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'manage products',
            'manage categories',
            'manage orders',
            'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // admin
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($permissions);

        // seller
        $sellerRole = Role::firstOrCreate(['name' => 'seller']);
        $sellerRole->syncPermissions([
            'manage products',
            'manage categories',
        ]);

        // customer
        Role::firstOrCreate(['name' => 'customer']);

        // visitor
        Role::firstOrCreate(['name' => 'visitor']);
    }
}
