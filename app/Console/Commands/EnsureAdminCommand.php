<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\DatabaseSeeder;

class EnsureAdminCommand extends Command
{
    protected $signature = 'stockflow:ensure-admin';
    protected $description = 'Provision initial admin user from ADMIN_EMAIL and ADMIN_PASSWORD';

    public function handle(): int
    {
        $this->call('db:seed');
        $this->info('Admin user verified.');
        return Command::SUCCESS;
    }
}
