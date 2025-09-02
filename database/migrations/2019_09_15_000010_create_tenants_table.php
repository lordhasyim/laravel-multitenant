<?php
// database/migrations/2019_09_15_000010_create_tenants_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique(); // Unique slug for database naming, URLs, etc.
            $table->string('email');
            $table->string('db_name')->unique(); // Ensure unique database names
            $table->string('db_host')->default('127.0.0.1');
            $table->string('db_port')->default('3306');
            $table->string('db_username')->default('root');
            $table->string('db_password');
            $table->timestamps();
            $table->json('data')->nullable();

            // Add indexes for performance
            $table->index('slug');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};