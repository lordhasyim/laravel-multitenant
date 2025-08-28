<?php
// database/seeders/MasterPermissionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterPermission;

class MasterPermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // Product permissions
            ['name' => 'create-product', 'category' => 'products', 'description' => 'Can create new products'],
            ['name' => 'view-product', 'category' => 'products', 'description' => 'Can view products'],
            ['name' => 'edit-product', 'category' => 'products', 'description' => 'Can edit existing products'],
            ['name' => 'delete-product', 'category' => 'products', 'description' => 'Can delete products'],
            
            // User permissions
            ['name' => 'create-user', 'category' => 'users', 'description' => 'Can create new users'],
            ['name' => 'view-user', 'category' => 'users', 'description' => 'Can view users'],
            ['name' => 'edit-user', 'category' => 'users', 'description' => 'Can edit existing users'],
            ['name' => 'delete-user', 'category' => 'users', 'description' => 'Can delete users'],
        ];

        foreach ($permissions as $permission) {
            MasterPermission::updateOrCreate(
                ['name' => $permission['name'], 'guard_name' => 'api'],
                $permission
            );
        }
    }
}