<?php

namespace App\Helpers\AppLinking;

use App\Models\Team;
use App\Notifications\AppLinkingNotification;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Notification;

class AppLinking
{
    public function sendAppLinkingNotifications(Team $team): void
    {
        $emails = $team->emails;

        foreach ($emails as $email) {
            $appLinkingToken = $this->generateAppLinkingToken($team->id, $email);

            Notification::route('mail', $email)
                ->notify(new AppLinkingNotification($team->name, $team->tournament->name, $appLinkingToken));
        }
    }

    public function generateAppLinkingToken($teamId, $email): string
    {
        return Crypt::encrypt([
            'team_id' => $teamId,
            'email' => $email,
        ]);
    }
}
