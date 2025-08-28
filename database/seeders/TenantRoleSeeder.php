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
        
        // Get current tenant info to customize roles
        $tenantId = tenant('id');
        
        if ($tenantId == 1) {
            // Alamsegar roles
            $managerRole = Role::create(['name' => 'manager', 'guard_name' => $guardName]);
            $operatorRole = Role::create(['name' => 'operator', 'guard_name' => $guardName]);
            
            $managerRole->givePermissionTo([
                'create-product', 'view-product', 'edit-product', 'delete-product'
            ]);
            $operatorRole->givePermissionTo(['view-product']);
            
        } elseif ($tenantId == 2) {
            // Kretekjaya roles
            $supervisorRole = Role::create(['name' => 'supervisor', 'guard_name' => $guardName]);
            $staffRole = Role::create(['name' => 'staff', 'guard_name' => $guardName]);
            
            $supervisorRole->givePermissionTo([
                'create-product', 'view-product', 'edit-product', 'delete-product'
            ]);
            $staffRole->givePermissionTo(['view-product']);
        }
        
        // Common admin role for all tenants
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => $guardName]);
        
        // Get all permissions with the same guard
        $allPermissions = Permission::where('guard_name', $guardName)->get();
        $adminRole->givePermissionTo($allPermissions);
    }
}