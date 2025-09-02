<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Illuminate\Support\Str;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    // Configure for UUID primary key
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'slug',
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

    protected static function boot()
    {
        parent::boot();

        // Automatically generate UUID and slug when creating a new tenant
        static::creating(function ($tenant) {
            if (empty($tenant->id)) {
                $tenant->id = (string) Str::uuid();
            }

            if (empty($tenant->slug) && !empty($tenant->name)) {
                $tenant->slug = static::generateSlug($tenant->name);
            }

            // Auto-generate database name from slug if not provided
            if (empty($tenant->db_name) && !empty($tenant->slug)) {
                $tenant->db_name = 'tenant_' . $tenant->slug;
            }
        });
    }

    // Override the method that determines which columns are NOT stored in JSON
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'slug',
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

    // Custom method to get formatted database name using slug
    public function getDatabaseName(): string
    {
        // If db_name is explicitly set, use it
        if ($this->db_name) {
            return $this->db_name;
        }

        // Otherwise, generate from slug
        return 'tenant_' . $this->slug;
    }

    // Static method to generate slug from name
    public static function generateSlug(string $name): string
    {
        $slug = Str::slug($name, '_');

        // Ensure uniqueness by checking database
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '_' . $counter;
            $counter++;
        }

        return $slug;
    }

    // Method to generate database name from slug
    public static function generateDatabaseName(string $slug): string
    {
        return 'tenant_' . $slug;
    }

    // Helper methods for folder/file naming using slug
    public function getStorageFolder(): string
    {
        return 'tenants/' . $this->slug;
    }

    public function getUploadPath(string $type = 'general'): string
    {
        return $this->getStorageFolder() . '/' . $type;
    }
}
