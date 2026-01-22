<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private LogService $logService,
    ) {}

    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $data['password'] = Hash::make($data['password']);
            
            $user = $this->userRepository->create($data);

            $this->logService->logAction('CREATE_USER', 'User', $user->id, "User {$user->name} created");

            return $user;
        });
    }

    public function updateUser(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $this->userRepository->update($user, $data);

            $this->logService->logAction('UPDATE_USER', 'User', $user->id, "User {$user->name} updated");

            return $user->fresh();
        });
    }

    public function deleteUser(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            $userName = $user->name;
            
            $deleted = $this->userRepository->delete($user);

            if ($deleted) {
                $this->logService->logAction('DELETE_USER', 'User', $user->id, "User {$userName} deleted");
            }

            return $deleted;
        });
    }

    public function changePassword(User $user, string $newPassword): bool
    {
        return DB::transaction(function () use ($user, $newPassword) {
            $hashedPassword = Hash::make($newPassword);
            
            $updated = $this->userRepository->updatePassword($user, $hashedPassword);

            if ($updated) {
                $this->logService->logAction('CHANGE_PASSWORD', 'User', $user->id, "Password changed for {$user->name}");
            }

            return $updated;
        });
    }

    public function activateUser(User $user): User
    {
        $this->userRepository->activateUser($user);
        $this->logService->logAction('ACTIVATE_USER', 'User', $user->id, "User {$user->name} activated");
        
        return $user->fresh();
    }

    public function deactivateUser(User $user): User
    {
        $this->userRepository->deactivateUser($user);
        $this->logService->logAction('DEACTIVATE_USER', 'User', $user->id, "User {$user->name} deactivated");
        
        return $user->fresh();
    }

    public function getUsersByRole(string $role): Collection
    {
        return $this->userRepository->getUsersByRole($role);
    }

    public function searchUsers(string $search, ?string $role = null): Collection
    {
        return $this->userRepository->searchUsers($search, $role);
    }
}
