<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Contracts\TenantDatabaseManager;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\DatabaseConfig;

class Tenant extends BaseTenant implements TenantWithDatabase
{
     use HasDatabase, HasDomains;

    protected $fillable = [
        'id',
        'name',
        'email',
        'domain',
        'db_name',
        'db_host',
        'db_port',
        'db_username',
        'db_password',
    ];

    protected $hidden = [
        'db_password'
    ];

    // Override the method that determines which columns are NOT stored in JSON
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'email', 
            'domain',
            'db_name',
            'db_host',
            'db_port', 
            'db_username',
            'db_password',
            'created_at',
            'updated_at',
        ];
    }

   // Custom method to get formatted database name
    public function getDatabaseName(): string
    {
        return $this->db_name ?? 'tenant_' . $this->id;
    }

    // Override the database name for tenancy
    // public function getTenantKeyName(): string
    // {
    //     return 'id';
    // }

    // Override how the database name is determined
    // public function getConnectionName(): string
    // {
    //     return 'tenant';
    // }
}
