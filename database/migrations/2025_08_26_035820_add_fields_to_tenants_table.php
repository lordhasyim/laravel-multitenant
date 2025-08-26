<?php

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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('email')->after('name');
            $table->string('domain')->unique()->after('email');
            $table->string('db_name')->after('domain');
            $table->string('db_host')->default('127.0.0.1')->after('db_name');
            $table->string('db_port')->default('3306')->after('db_host');
            $table->string('db_username')->default('root')->after('db_port');
            $table->string('db_password')->after('db_username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'name', 'email', 'domain', 'db_name', 
                'db_host', 'db_port', 'db_username', 'db_password'
            ]);
        });
    }
};
