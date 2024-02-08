<?php

namespace App\Events;

use App\Models\Team;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamApprovedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public Team $team)
    {
    }
}
