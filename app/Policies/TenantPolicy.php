<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;

class TenantPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'staff']);
    }

    public function view(User $user, Tenant $tenant): bool
    {
        return $user->hasRole(['admin', 'staff']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'staff']);
    }

    public function update(User $user, Tenant $tenant): bool
    {
        return $user->hasRole('admin') || 
               ($user->hasRole('staff') && !$this->isSensitiveChange($tenant));
    }

    public function delete(User $user, Tenant $tenant): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, Tenant $tenant): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Tenant $tenant): bool
    {
        return $user->hasRole('admin');
    }

    private function isSensitiveChange(Tenant $tenant): bool
    {
        return $tenant->isDirty(['email', 'phone', 'status']);
    }
}
