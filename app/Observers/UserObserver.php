<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    public function creating(User $user): void
    {
        if (!isset($user->role)) {
            $user->role = 'staff';
        }
    }

    public function created(User $user): void
    {
        //
    }

    public function updating(User $user): void
    {
        if (Auth::id() === $user->id && $user->isDirty('role')) {
            $user->role = $user->getOriginal('role');
        }
    }

    public function updated(User $user): void
    {
        //
    }

    public function deleting(User $user): void
    {
        if (Auth::id() === $user->id) {
            throw new \Exception('Tidak dapat menghapus akun sendiri');
        }
    }

    public function deleted(User $user): void
    {
        //
    }
}
