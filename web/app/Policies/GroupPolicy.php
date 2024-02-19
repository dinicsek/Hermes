<?php

namespace App\Policies;

use App\Models\Enums\UserRole;
use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::MANAGER;
    }

    public function view(User $user, Group $group): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::MANAGER;
    }

    public function update(User $user, Group $group): bool
    {
        return $user->role === UserRole::MANAGER && $group->tournament->user_id === $user->id;
    }

    public function delete(User $user, Group $group): bool
    {
        return $user->role === UserRole::MANAGER && $group->tournament->user_id === $user->id;
    }

    public function restore(User $user, Group $group): bool
    {
        return $user->role === UserRole::MANAGER && $group->tournament->user_id === $user->id;

    }

    public function forceDelete(User $user, Group $group): bool
    {
        return $user->role === UserRole::MANAGER && $group->tournament->user_id === $user->id;
    }
}
