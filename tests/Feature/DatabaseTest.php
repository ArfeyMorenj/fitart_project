<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;

class DatabaseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function database_tables_are_created()
    {
        $tables = [
            'companies', 'users', 'clients', 'invoices', 
            'products', 'items', 'banks', 'chart_of_accounts'
        ];

        foreach ($tables as $table) {
            $this->assertTrue(
                \Schema::hasTable($table),
                "Table {$table} does not exist"
            );
        }
    }

    /** @test */
    public function seeders_populate_data()
    {
        $this->seed();
        
        $this->assertDatabaseCount('users', 3);
        $this->assertDatabaseCount('banks', 6);
        $this->assertDatabaseHas('users', ['username' => 'admin']);
    }

    /** @test */
    public function client_relationships_work()
    {
        $this->seed();
        
        $client = Client::factory()->create();
        $invoice = Invoice::factory()->create(['client_id' => $client->id]);
        
        $this->assertEquals($client->id, $invoice->client->id);
        $this->assertTrue($client->invoices->contains($invoice));
    }
}