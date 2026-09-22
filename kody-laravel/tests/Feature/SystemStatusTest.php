<?php

namespace Tests\Feature;

use Tests\TestCase;

class SystemStatusTest extends TestCase
{
    public function test_system_status_returns_successful_database_summary(): void
    {
        $response = $this->get('/api/v1/status');
        $response->dump();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'database',
            'counts' => [
                'users',
                'accounts',
                'clients',
                'suppliers',
                'items',
                'invoices',
            ],
            'timestamp',
        ]);
        $response->assertJson([
            'status' => 'success',
        ]);
    }

    public function test_items_endpoint_returns_data(): void
    {
        $response = $this->get('/api/v1/items');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'data',
            ],
        ]);
    }
}
