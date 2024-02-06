<?php

namespace App\Policies;

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TournamentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, Tournament $tournament): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Tournament $tournament): bool
    {
    }

    public function delete(User $user, Tournament $tournament): bool
    {
    }

    public function restore(User $user, Tournament $tournament): bool
    {
    }

    public function forceDelete(User $user, Tournament $tournament): bool
    {
    }
}
