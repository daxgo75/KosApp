<?php

namespace App\Traits;

trait HasRoles
{
    public function hasRole($roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        return in_array($this->role ?? 'guest', $roles);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAccountant(): bool
    {
        return $this->role === 'accountant';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }
}
