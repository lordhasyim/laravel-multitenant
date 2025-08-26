<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class CreateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create {id} {name} {email} {domain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to Create new tenant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id');
        $name = $this->argument('name');
        $email = $this->argument('email');
        $domain = $this->argument('domain');

        // Delete old tenant if exist
        $oldTenant = Tenant::find($id);
        if ($oldTenant) {
            $oldTenant->delete();
            $this->info("Deleted old tenant: $id");
        }

        // Create tenant
        $tenant = Tenant::create([
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'domain' => $domain,
            'db_name' => 'tenant_' . $id,
            'db_host' => '127.0.0.1',
            'db_port' => '3306',
            'db_username' => 'root',
            'db_password' => 'root',
        ]);

        // Create domain
        $tenant->domains()->create(['domain' => $domain]);

        $this->info("Created tenant: {$tenant->name} with domain: {$domain}");
    }
}
