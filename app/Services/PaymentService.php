<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        private LogService $logService,
    ) {}

    public function createPayment(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            if (!isset($data['reference_code'])) {
                $data['reference_code'] = $this->generateReferenceCode();
            }

            $payment = Payment::create($data);

            if ($payment->status === 'confirmed') {
                $this->updateRoomStatus($payment->room_id, 'occupied');
            }

            $this->logService->logPaymentAction('PAYMENT_CREATED', $payment->id, details: "Amount: {$payment->amount}");

            return $payment;
        });
    }

    public function confirmPayment(Payment $payment): Payment
    {
        return DB::transaction(function () use ($payment) {
            $payment->update(['status' => 'confirmed']);
            $this->updateRoomStatus($payment->room_id, 'occupied');

            $this->logService->logPaymentAction('PAYMENT_CONFIRMED', $payment->id);

            return $payment;
        });
    }

    public function cancelPayment(Payment $payment, string $reason = null): Payment
    {
        return DB::transaction(function () use ($payment, $reason) {
            $payment->update([
                'status' => 'failed',
                'notes' => $reason ? "Cancelled: {$reason}" : $payment->notes,
            ]);

            $this->checkAndUpdateRoomStatus($payment->room_id);

            $this->logService->logPaymentAction('PAYMENT_CANCELLED', $payment->id, details: $reason);

            return $payment;
        });
    }

    private function checkAndUpdateRoomStatus(int $roomId): void
    {
        $hasActivePayments = Payment::where('room_id', $roomId)
            ->where('status', 'confirmed')
            ->whereDate('month_year', '>=', now()->startOfMonth())
            ->exists();

        if (!$hasActivePayments) {
            $this->updateRoomStatus($roomId, 'available');
        }
    }

    /**
     * Get overdue payments
     */
    public function getOverduePayments()
    {
        return Payment::overdue()
            ->with(['tenant', 'room'])
            ->get();
    }

    /**
     * Calculate total income for a period
     */
    public function calculateIncome(Carbon $startDate, Carbon $endDate): float
    {
        return Payment::confirmed()
            ->byPeriod($startDate, $endDate)
            ->sum('amount');
    }

    /**
     * Update room status
     */
    private function updateRoomStatus(int $roomId, string $status): void
    {
        Room::findOrFail($roomId)->update(['status' => $status]);
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStatistics(Carbon $startDate, Carbon $endDate): array
    {
        $payments = Payment::byPeriod($startDate, $endDate)->get();

        return [
            'total_payments' => $payments->count(),
            'confirmed_payments' => $payments->where('status', 'confirmed')->count(),
            'pending_payments' => $payments->where('status', 'pending')->count(),
            'total_amount' => $payments->where('status', 'confirmed')->sum('amount'),
            'pending_amount' => $payments->where('status', 'pending')->sum('amount'),
        ];
    }

    private function generateReferenceCode(): string
    {
        return 'PAY-' . now()->format('Ymd') . '-' . strtoupper(uniqid());
    }
}
