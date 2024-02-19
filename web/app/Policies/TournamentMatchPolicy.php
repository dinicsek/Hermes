<?php

namespace App\Policies;

use App\Models\Enums\UserRole;
use App\Models\TournamentMatch;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TournamentMatchPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::MANAGER;
    }

    public function view(?User $user, TournamentMatch $match): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::MANAGER;
    }

    public function update(User $user, TournamentMatch $match): bool
    {
        return $user->role === UserRole::MANAGER && $match->tournament->user_id === $user->id;
    }

    public function delete(User $user, TournamentMatch $match): bool
    {
        return $user->role === UserRole::MANAGER && $match->tournament->user_id === $user->id;
    }

    public function restore(User $user, TournamentMatch $match): bool
    {
        return $user->role === UserRole::MANAGER && $match->tournament->user_id === $user->id;
    }

    public function forceDelete(User $user, TournamentMatch $match): bool
    {
        return $user->role === UserRole::MANAGER && $match->tournament->user_id === $user->id;
    }
}
