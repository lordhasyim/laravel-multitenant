<?php
// database/seeders/tenant/TenantRoleSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TenantRoleSeeder extends Seeder
{
    public function run()
    {
        // Set the guard name to match your permissions
        $guardName = 'api';

        // Get current tenant info
        $tenantId = tenant('id');
        $tenantName = tenant('name');
        $tenantSlug = tenant('slug');

        // Use tenant slug for role customization (most reliable identifier)
        switch ($tenantSlug) {
            case 'alamsegar':
                $this->createAlamsegarRoles($guardName);
                break;

            case 'kretek_jaya':
            case 'kretekjaya':
                $this->createKretekjayaRoles($guardName);
                break;

            default:
                $this->createDefaultRoles($guardName);
                break;
        }

        // Common admin role for all tenants
        $this->createAdminRole($guardName);

        $this->command->info("Roles created for tenant: {$tenantName} (Slug: {$tenantSlug}, ID: {$tenantId})");
    }

    private function createAlamsegarRoles($guardName)
    {
        $managerRole = Role::create(['name' => 'manager', 'guard_name' => $guardName]);
        $operatorRole = Role::create(['name' => 'operator', 'guard_name' => $guardName]);

        $managerRole->givePermissionTo([
            'create-product',
            'view-product',
            'edit-product',
            'delete-product'
        ]);
        $operatorRole->givePermissionTo(['view-product']);
    }

    private function createKretekjayaRoles($guardName)
    {
        $supervisorRole = Role::create(['name' => 'supervisor', 'guard_name' => $guardName]);
        $staffRole = Role::create(['name' => 'staff', 'guard_name' => $guardName]);

        $supervisorRole->givePermissionTo([
            'create-product',
            'view-product',
            'edit-product',
            'delete-product'
        ]);
        $staffRole->givePermissionTo(['view-product']);
    }

    private function createDefaultRoles($guardName)
    {
        $managerRole = Role::create(['name' => 'manager', 'guard_name' => $guardName]);
        $userRole = Role::create(['name' => 'user', 'guard_name' => $guardName]);

        $managerRole->givePermissionTo([
            'create-product',
            'view-product',
            'edit-product',
            'delete-product'
        ]);
        $userRole->givePermissionTo(['view-product']);
    }

    private function createAdminRole($guardName)
    {
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => $guardName]);
        
        // Get all permissions with the same guard
        $allPermissions = Permission::where('guard_name', $guardName)->get();
        $adminRole->givePermissionTo($allPermissions);
    }
}