<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\Paginator;

class UserRepository extends BaseRepository
{
    public function model(): string
    {
        return User::class;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->query()->where('email', $email)->first();
    }

    public function getUsersByRole(string $role): Collection
    {
        return $this->query()->where('role', $role)->get();
    }

    public function searchUsers(string $search, ?string $role = null): Collection
    {
        return $this->query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, function ($query, $role) {
                $query->where('role', $role);
            })
            ->get();
    }

    public function getUsersWithPagination(int $perPage = 15, ?string $role = null): Paginator
    {
        return $this->query()
            ->when($role, function ($query, $role) {
                $query->where('role', $role);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function updatePassword(User $user, string $password): bool
    {
        return $user->update(['password' => $password]);
    }

    public function deactivateUser(User $user): bool
    {
        return $user->update(['is_active' => false]);
    }

    public function activateUser(User $user): bool
    {
        return $user->update(['is_active' => true]);
    }
}
