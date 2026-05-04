<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetDatabase extends Command
{
    protected $signature = 'db:reset';
    protected $description = 'Reset database only (migrate:fresh & seed)';

    public function handle()
    {
        $this->info('🔄 Resetting database...');
        $this->call('migrate:fresh');
        $this->call('db:seed');
        $this->info('✨ Database reset completed!');
    }
}
