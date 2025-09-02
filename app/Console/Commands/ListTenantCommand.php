<?php
// app/Console/Commands/ListTenantsCommand.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use PDO;

class ListTenantCommand extends Command
{
    protected $signature = 'tenant:list {--check-db : Check if databases actually exist}';
    protected $description = 'List all tenants';

    public function handle()
    {
        $checkDb = $this->option('check-db');
        
        $tenants = Tenant::orderBy('name')->get();

        if ($tenants->isEmpty()) {
            $this->info('No tenants found.');
            return 0;
        }

        $headers = ['ID', 'Name', 'Slug', 'Email', 'Database'];
        if ($checkDb) {
            $headers[] = 'DB Exists';
        }

        $rows = [];
        foreach ($tenants as $tenant) {
            $row = [
                substr($tenant->id, 0, 8) . '...',
                $tenant->name,
                $tenant->slug,
                $tenant->email,
                $tenant->getDatabaseName(),
            ];

            if ($checkDb) {
                $row[] = $this->databaseExists($tenant->getDatabaseName()) ? '✅' : '❌';
            }

            $rows[] = $row;
        }

        $this->table($headers, $rows);
        $this->info("Total tenants: " . $tenants->count());

        return 0;
    }

    private function databaseExists(string $dbName): bool
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
            
            $stmt = $pdo->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?");
            $stmt->execute([$dbName]);
            
            return $stmt->rowCount() > 0;
            
        } catch (\Exception $e) {
            return false;
        }
    }
}