<?php

namespace App\Console\Commands;

use App\Helpers\AppLinking\AppLinkingHelper;
use App\Models\Team;
use App\Notifications\AppLinkingUpdatedNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendAppLinkingUpdatedCommand extends Command
{
    protected $signature = 'send:app-linking-updated';

    protected $description = 'Command description';

    public function handle(AppLinkingHelper $appLinkingHelper): void
    {
        $teams = Team::all();

        foreach ($teams as $team) {
            $emails = $team->emails;

            foreach ($emails as $email) {
                $appLinkingToken = $appLinkingHelper->generateAppLinkingToken($team->id, $email);

                Notification::route('mail', $email)
                    ->notify(new AppLinkingUpdatedNotification($appLinkingToken));
            }
        }

        $this->info('App linking updated notifications sent successfully');
    }
}
