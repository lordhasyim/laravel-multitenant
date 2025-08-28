<?php
// app/Console/Commands/SyncTenantPermissions.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MasterPermission;
use App\Models\Tenant;
use Spatie\Permission\Models\Permission;

class SyncTenantPermissions extends Command
{
    protected $signature = 'tenants:sync-permissions {--tenant=* : Specific tenant IDs to sync}';
    protected $description = 'Sync master permissions to all or specific tenant databases';

    public function handle()
    {
        $masterPermissions = MasterPermission::all();
        $tenantIds = $this->option('tenant');
        
        if (empty($tenantIds)) {
            $tenants = Tenant::all();
            $this->info('Syncing permissions to all tenants...');
        } else {
            $tenants = Tenant::whereIn('id', $tenantIds)->get();
            $this->info('Syncing permissions to specified tenants: ' . implode(', ', $tenantIds));
        }

        foreach ($tenants as $tenant) {
            $this->info("Syncing permissions for tenant: {$tenant->id} ({$tenant->name})");
            
            try {
                tenancy()->initialize($tenant);
                
                foreach ($masterPermissions as $masterPermission) {
                    Permission::updateOrCreate(
                        ['name' => $masterPermission->name, 'guard_name' => $masterPermission->guard_name],
                        [
                            'name' => $masterPermission->name,
                            'guard_name' => $masterPermission->guard_name,
                        ]
                    );
                }
                
                // Remove permissions that no longer exist in master
                $masterPermissionNames = $masterPermissions->pluck('name')->toArray();
                Permission::whereNotIn('name', $masterPermissionNames)->delete();
                
                $this->info("✓ Synced " . $masterPermissions->count() . " permissions for tenant {$tenant->id}");
                
            } catch (\Exception $e) {
                $this->error("✗ Failed to sync permissions for tenant {$tenant->id}: " . $e->getMessage());
            } finally {
                tenancy()->end();
            }
        }
        
        $this->info('Permission sync completed!');
    }
}