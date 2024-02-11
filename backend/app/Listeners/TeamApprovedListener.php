<?php

namespace App\Listeners;

use App\Events\TeamApprovedEvent;
use App\Notifications\TeamApprovedNotification;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Notification;

class TeamApprovedListener
{
    public function __construct()
    {
    }

    public function handle(TeamApprovedEvent $event): void
    {
        $emails = $event->team->emails;

        foreach ($emails as $email) {
            $appLinkingToken = Crypt::encrypt([
                'team_id' => $event->team->id,
                'email' => $email,
            ]);

            Notification::route('mail', $email)
                ->notify(new TeamApprovedNotification($event->team->name, $event->team->tournament->name, $appLinkingToken));
        }
    }
}
