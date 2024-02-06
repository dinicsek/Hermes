<?php

namespace App\Policies;

use App\Models\Enums\UserRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::MANAGER;
    }

    public function view(User $user, Team $team): bool
    {
        return $user->role === UserRole::MANAGER && $team->tournament->user_id === $user->id;
    }

    public function create(?User $user): bool
    {
        return true;
    }

    public function update(User $user, Team $team): bool
    {
        return $user->role === UserRole::MANAGER && $team->tournament->user_id === $user->id;
    }

    public function delete(User $user, Team $team): bool
    {
        return $user->role === UserRole::MANAGER && $team->tournament->user_id === $user->id;
    }

    public function restore(User $user, Team $team): bool
    {
        return $user->role === UserRole::MANAGER && $team->tournament->user_id === $user->id;
    }

    public function forceDelete(User $user, Team $team): bool
    {
        return $user->role === UserRole::MANAGER && $team->tournament->user_id === $user->id;
    }
}
