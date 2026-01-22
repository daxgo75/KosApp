<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'staff', 'accountant']);
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->hasRole(['admin', 'staff', 'accountant']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'staff']);
    }

    public function update(User $user, Payment $payment): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('accountant')) {
            return in_array($payment->status, ['pending', 'confirmed']);
        }

        return false;
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->hasRole('admin');
    }

    public function confirm(User $user, Payment $payment): bool
    {
        return $user->hasRole(['admin', 'accountant']) && $payment->status === 'pending';
    }

    public function reject(User $user, Payment $payment): bool
    {
        return $user->hasRole('admin') && $payment->status === 'pending';
    }
}
