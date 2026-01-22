<?php

namespace App\Repositories;

use App\Models\Payment;

class PaymentRepository extends BaseRepository
{
    public function model(): string
    {
        return Payment::class;
    }

    public function findWithRelations(int $id)
    {
        return $this->findOrFail($id, ['tenant', 'room']);
    }

    public function getPending(int $perPage = 15)
    {
        return $this->query()
            ->where('status', 'pending')
            ->with(['tenant', 'room'])
            ->orderBy('due_date', 'asc')
            ->paginate($perPage);
    }

    public function getOverdue()
    {
        return $this->query()
            ->where('status', 'pending')
            ->where('due_date', '<', now())
            ->with(['tenant', 'room'])
            ->get();
    }

    public function getByTenant(int $tenantId, int $perPage = 15)
    {
        return $this->query()
            ->where('tenant_id', $tenantId)
            ->with('room')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getByStatus(string $status, int $perPage = 15)
    {
        return $this->query()
            ->where('status', $status)
            ->with(['tenant', 'room'])
            ->paginate($perPage);
    }
}
