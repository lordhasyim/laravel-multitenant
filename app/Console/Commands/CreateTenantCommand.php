<?php
// app/Console/Commands/CreateTenantCommand.php
// Laravel 12 will auto-discover this command - no registration needed

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Str;

class CreateTenantCommand extends Command
{
    protected $signature = 'tenant:create 
                          {name : The tenant name}
                          {email : The tenant admin email}
                          {--slug= : Custom slug (optional, will be auto-generated if not provided)}
                          {--db-name= : Custom database name (optional)}
                          {--id= : Custom UUID (optional)}';

    protected $description = 'Create a new tenant with UUID, slug, and name-based database';

    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $customSlug = $this->option('slug');
        $customDbName = $this->option('db-name');
        $customId = $this->option('id');

        // Generate or validate slug
        if ($customSlug) {
            // Validate custom slug
            if (Tenant::where('slug', $customSlug)->exists()) {
                $this->error("Tenant with slug '{$customSlug}' already exists!");
                return 1;
            }
            $slug = $customSlug;
        } else {
            $slug = Tenant::generateSlug($name);
        }

        // Generate database name
        $dbName = $customDbName ?? Tenant::generateDatabaseName($slug);
        
        // Check if database name already exists
        if (Tenant::where('db_name', $dbName)->exists()) {
            $this->error("Tenant with database name '{$dbName}' already exists!");
            return 1;
        }

        // Create tenant
        $tenantData = [
            'name' => $name,
            'slug' => $slug,
            'email' => $email,
            'db_name' => $dbName,
            'db_host' => env('DB_HOST', '127.0.0.1'),
            'db_port' => env('DB_PORT', '3306'),
            'db_username' => env('DB_USERNAME', 'root'),
            'db_password' => env('DB_PASSWORD', ''),
        ];

        if ($customId) {
            if (!Str::isUuid($customId)) {
                $this->error("Invalid UUID format!");
                return 1;
            }
            $tenantData['id'] = $customId;
        }

        try {
            $tenant = Tenant::create($tenantData);
            
            $this->info("Tenant created successfully!");
            $this->table(['Field', 'Value'], [
                ['ID', $tenant->id],
                ['Name', $tenant->name],
                ['Slug', $tenant->slug],
                ['Email', $tenant->email],
                ['Database', $tenant->getDatabaseName()],
                ['Storage Path', $tenant->getStorageFolder()],
            ]);

            // Ask if user wants to run migrations
            if ($this->confirm('Do you want to run tenant migrations?', true)) {
                $this->call('tenants:migrate', ['--tenants' => $tenant->id]);
            }

            // Ask if user wants to seed the tenant
            if ($this->confirm('Do you want to seed tenant data?', true)) {
                $this->call('tenants:seed', ['--tenants' => $tenant->id]);
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("Failed to create tenant: " . $e->getMessage());
            return 1;
        }
    }
}