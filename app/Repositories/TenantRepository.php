<?php

namespace App\Repositories;

use App\Models\Tenant;

class TenantRepository extends BaseRepository
{
    public function model(): string
    {
        return Tenant::class;
    }

    public function findWithRelations(int $id)
    {
        return $this->findOrFail($id, ['room', 'payments', 'photos']);
    }

    public function getActive()
    {
        return $this->query()
            ->where('status', 'active')
            ->with('room')
            ->get();
    }

    public function search(string $query, int $perPage = 15)
    {
        return $this->query()
            ->where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->with(['room', 'payments'])
            ->paginate($perPage);
    }

    public function filterByStatus(string $status, int $perPage = 15)
    {
        return $this->query()
            ->where('status', $status)
            ->with('room')
            ->paginate($perPage);
    }
}
