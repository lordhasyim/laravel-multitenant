<?php
// app/Console/Commands/CreateTenantCommand.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use PDO;
use PDOException;

class CreateTenantCommand extends Command
{
    protected $signature = 'tenant:create 
                          {name : The tenant name}
                          {email : The tenant admin email}
                          {--slug= : Custom slug (optional, will be auto-generated if not provided)}
                          {--db-name= : Custom database name (optional)}
                          {--id= : Custom UUID (optional)}
                          {--skip-db-creation : Skip physical database creation}';

    protected $description = 'Create a new tenant with UUID, slug, database creation and migrations';

    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $customSlug = $this->option('slug');
        $customDbName = $this->option('db-name');
        $customId = $this->option('id');
        $skipDbCreation = $this->option('skip-db-creation');

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

        // Check if database name already exists in tenants table
        if (Tenant::where('db_name', $dbName)->exists()) {
            $this->error("Tenant with database name '{$dbName}' already exists!");
            return 1;
        }

        $this->info("Creating tenant: {$name}");
        $this->info("Slug: {$slug}");
        $this->info("Database: {$dbName}");

        // Create physical database if not skipped
        if (!$skipDbCreation) {
            if (!$this->createPhysicalDatabase($dbName)) {
                return 1;
            }
        }

        // Create tenant record
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

            $this->info("✅ Tenant record created successfully!");
            $this->table(['Field', 'Value'], [
                ['ID', $tenant->id],
                ['Name', $tenant->name],
                ['Slug', $tenant->slug],
                ['Email', $tenant->email],
                ['Database', $tenant->getDatabaseName()],
                ['Storage Path', $tenant->getStorageFolder()],
            ]);

            // Run tenant migrations
            if ($this->confirm('Do you want to run tenant migrations?', true)) {
                $this->info("Running tenant migrations...");
                try {
                    Artisan::call('tenants:migrate', ['--tenants' => $tenant->id]);
                    $this->info("✅ Migrations completed successfully!");
                } catch (\Exception $e) {
                    $this->error("❌ Migration failed: " . $e->getMessage());
                }
            }

            // Ask if user wants to seed the tenant
            if ($this->confirm('Do you want to seed tenant data?', false)) {
                $this->info("Seeding tenant data...");
                try {
                    Artisan::call('tenants:seed', ['--tenants' => $tenant->id]);
                    $this->info("✅ Seeding completed successfully!");
                } catch (\Exception $e) {
                    $this->error("❌ Seeding failed: " . $e->getMessage());
                }
            }

            $this->newLine();
            $this->info("🎉 Tenant '{$name}' created successfully!");
            $this->info("You can now use tenant ID: {$tenant->id}");

            return 0;

        } catch (\Exception $e) {
            $this->error("Failed to create tenant: " . $e->getMessage());

            // Cleanup: Drop the database if tenant creation failed
            if (!$skipDbCreation) {
                $this->warn("Cleaning up: Dropping database {$dbName}");
                $this->dropPhysicalDatabase($dbName);
            }

            return 1;
        }
    }

    /**
     * Create the physical database
     */
    private function createPhysicalDatabase(string $dbName): bool
    {
        try {
            $this->info("Creating physical database: {$dbName}");

            // Get connection details from config
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', '3306');
            $username = env('DB_USERNAME', 'root');
            $password = env('DB_PASSWORD', '');

            // Connect to MySQL without specifying a database
            $pdo = new PDO(
                "mysql:host={$host};port={$port};charset=utf8mb4",
                $username,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Check if database already exists
            $stmt = $pdo->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?");
            $stmt->execute([$dbName]);

            if ($stmt->rowCount() > 0) {
                $this->warn("Database {$dbName} already exists. Skipping creation.");
                return true;
            }

            // Create the database
            $pdo->exec("CREATE DATABASE `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->info("✅ Database {$dbName} created successfully!");

            return true;
        } catch (PDOException $e) {
            $this->error("❌ Failed to create database {$dbName}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Drop the physical database (cleanup)
     */
    private function dropPhysicalDatabase(string $dbName): bool
    {
        try {
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', '3306');
            $username = env('DB_USERNAME', 'root');
            $password = env('DB_PASSWORD', '');

            $pdo = new PDO(
                "mysql:host={$host};port={$port};charset=utf8mb4",
                $username,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $pdo->exec("DROP DATABASE IF EXISTS `{$dbName}`");
            $this->info("Database {$dbName} dropped.");

            return true;
        } catch (PDOException $e) {
            $this->error("Failed to drop database {$dbName}: " . $e->getMessage());
            return false;
        }
    }
}