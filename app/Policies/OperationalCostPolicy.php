<?php

namespace App\Policies;

use App\Models\OperationalCost;
use App\Models\User;

class OperationalCostPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'accountant']);
    }

    public function view(User $user, OperationalCost $cost): bool
    {
        return $user->hasRole(['admin', 'accountant']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('accountant');
    }

    public function update(User $user, OperationalCost $cost): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('accountant')) {
            return $cost->status === 'recorded' && $cost->created_by === $user->id;
        }

        return false;
    }

    public function delete(User $user, OperationalCost $cost): bool
    {
        return $user->hasRole('admin');
    }

    public function approve(User $user, OperationalCost $cost): bool
    {
        return $user->hasRole('admin') && $cost->status === 'recorded';
    }

    public function reject(User $user, OperationalCost $cost): bool
    {
        return $user->hasRole('admin') && $cost->status === 'recorded';
    }
}
