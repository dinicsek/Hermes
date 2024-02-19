<?php

namespace App\Policies;

use App\Models\Enums\UserRole;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TournamentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::MANAGER;
    }

    public function view(User $user, Tournament $tournament): bool
    {
        return $user->role === UserRole::MANAGER && $tournament->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::MANAGER;
    }

    public function update(User $user, Tournament $tournament): bool
    {
        return $user->role === UserRole::MANAGER && $tournament->user_id === $user->id;
    }

    public function delete(User $user, Tournament $tournament): bool
    {
        return $user->role === UserRole::MANAGER && $tournament->user_id === $user->id;
    }

    public function restore(User $user, Tournament $tournament): bool
    {
        return $user->role === UserRole::MANAGER && $tournament->user_id === $user->id;
    }

    public function forceDelete(User $user, Tournament $tournament): bool
    {
        return $user->role === UserRole::MANAGER && $tournament->user_id === $user->id;
    }
}
