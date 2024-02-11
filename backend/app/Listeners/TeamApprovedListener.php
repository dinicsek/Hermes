<?php

namespace App\Listeners;

use App\Events\TeamApprovedEvent;
use App\Helpers\AppLinking\AppLinkingHelper;

class TeamApprovedListener
{
    public function __construct(public AppLinkingHelper $appLinkingHelper)
    {
    }

    public function handle(TeamApprovedEvent $event): void
    {
        $this->appLinkingHelper->sendAppLinkingNotifications($event->team); // Real time facade
    }
}
