<?php

namespace App\Listeners;

use App\Events\TeamApprovedEvent;

class TeamApprovedListener
{
    public function __construct()
    {
    }

    public function handle(TeamApprovedEvent $event): void
    {
        //TODO: Send out emails
    }
}
