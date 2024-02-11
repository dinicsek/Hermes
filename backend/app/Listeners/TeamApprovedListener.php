<?php

namespace App\Listeners;

use App\Events\TeamApprovedEvent;
use Facades\App\Helpers\AppLinking\AppLinking;

class TeamApprovedListener
{
    public function __construct()
    {
    }

    public function handle(TeamApprovedEvent $event): void
    {
        AppLinking::sendAppLinkingNotifications($event->team); // Real time facade
    }
}
