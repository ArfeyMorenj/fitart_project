<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupDevelopment extends Command
{
    protected $signature = 'dev:setup';
    protected $description = 'Setup development database with sample data';

    public function handle()
    {
        $this->info('🚀 Setting up development database...');
        
        // Confirm
        if (!$this->confirm('This will reset your database. Continue?')) {
            return;
        }

        // Reset database
        $this->call('migrate:fresh');
        $this->info('✅ Database migrated');

        // Seed data
        $this->call('db:seed');
        $this->info('✅ Database seeded');

        // Create sample data
        $this->info('📝 Creating sample data...');
        \App\Models\Client::factory(20)->create();
        \App\Models\Product::factory(10)->create();
        
        $this->info('✅ Sample data created');
        $this->newLine();
        $this->info('🎉 Development database ready!');
        $this->table(
            ['Username', 'Password', 'Role'],
            [
                ['admin', 'admin123', 'admin'],
                ['manager', 'manager123', 'manager'],
                ['user', 'user123', 'user'],
            ]
        );
    }
}