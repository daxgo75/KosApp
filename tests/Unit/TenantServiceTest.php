<?php

namespace Tests\Unit;

use App\Models\Tenant;
use App\Services\TenantService;
use Tests\TestCase;

class TenantServiceTest extends TestCase
{
    private TenantService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TenantService::class);
    }

    public function test_create_tenant_with_valid_data()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '08123456789',
            'address' => 'Jln Merdeka 123',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'postal_code' => '12345',
            'identity_type' => 'ktp',
            'identity_number' => '1234567890123456',
            'status' => 'active',
        ];

        $tenant = $this->service->createTenant($data);

        $this->assertNotNull($tenant->id);
        $this->assertEquals('John Doe', $tenant->name);
        $this->assertEquals('active', $tenant->status);
    }

    public function test_update_tenant_information()
    {
        $tenant = Tenant::factory()->create();

        $updated = $this->service->updateTenant($tenant, [
            'status' => 'inactive',
        ]);

        $this->assertEquals('inactive', $updated->status);
    }

    public function test_tenant_scopes_work_correctly()
    {
        Tenant::factory()->create(['status' => 'active']);
        Tenant::factory()->create(['status' => 'inactive']);
        Tenant::factory()->create(['status' => 'active']);

        $activeTenants = Tenant::active()->get();

        $this->assertEquals(2, $activeTenants->count());
    }
}
