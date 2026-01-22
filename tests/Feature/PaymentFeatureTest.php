<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

class PaymentFeatureTest extends TestCase
{
    private User $admin;
    private Tenant $tenant;
    private Payment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->tenant = Tenant::factory()->create();
        $this->payment = Payment::factory()->pending()->create([
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_admin_can_view_payments()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/payments');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message', 'data']);
    }

    public function test_admin_can_confirm_payment()
    {
        $response = $this->actingAs($this->admin)
            ->putJson("/api/payments/{$this->payment->id}", [
                'status' => 'confirmed',
            ]);

        $response->assertStatus(200);
        $this->assertEquals('confirmed', $this->payment->fresh()->status);
    }

    public function test_unauthorized_user_cannot_confirm_payment()
    {
        $user = User::factory()->create(['role' => 'guest']);

        $response = $this->actingAs($user)
            ->putJson("/api/payments/{$this->payment->id}", [
                'status' => 'confirmed',
            ]);

        $response->assertStatus(403);
    }

    public function test_payment_validation_fails_with_invalid_data()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/payments', [
                'tenant_id' => 999,
                'amount' => 'invalid',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['tenant_id', 'amount']);
    }

    public function test_overdue_payments_are_detected()
    {
        $overduePayment = Payment::factory()->create([
            'tenant_id' => $this->tenant->id,
            'status' => 'pending',
            'due_date' => now()->subDays(5),
        ]);

        $overdue = Payment::overdue()->first();

        $this->assertNotNull($overdue);
        $this->assertEquals($overduePayment->id, $overdue->id);
    }
}
