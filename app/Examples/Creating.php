<?php
// Example: Creating tenants with UUIDs and name-based databases

use App\Models\Tenant;
use Illuminate\Support\Str;

// Method 1: Auto-generate UUID, auto-generate DB name from tenant name
$tenant1 = Tenant::create([
    'name' => 'Alamsegar',
    'email' => 'admin@alamsegar.com',
    // db_name will be auto-generated as 'tenant_alamsegar'
    'db_host' => '127.0.0.1',
    'db_port' => '3306',
    'db_username' => 'root',
    'db_password' => 'password',
]);

// Method 2: Specify custom UUID and custom DB name
$customUuid = Str::uuid()->toString();
$tenant2 = Tenant::create([
    'id' => $customUuid,
    'name' => 'Kretek Jaya',
    'email' => 'admin@kretekjaya.com',
    'db_name' => 'tenant_kretekjaya', // Custom DB name
    'db_host' => '127.0.0.1',
    'db_port' => '3306',
    'db_username' => 'root',
    'db_password' => 'password',
]);

// Method 3: Company with spaces and special characters
$tenant3 = Tenant::create([
    'name' => 'PT. Multi Corp Indonesia',
    'email' => 'admin@multicorp.co.id',
    // Will generate: tenant_pt_multi_corp_indonesia
]);

// Usage examples:
echo $tenant1->getDatabaseName(); // tenant_alamsegar
echo $tenant2->getDatabaseName(); // tenant_kretekjaya  
echo $tenant3->getDatabaseName(); // tenant_pt_multi_corp_indonesia

// You can also generate database names before creating tenants
$dbName = Tenant::generateDatabaseName('My Company Ltd');
echo $dbName; // tenant_my_company_ltd