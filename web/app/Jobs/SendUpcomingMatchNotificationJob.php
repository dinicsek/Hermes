<?php

namespace App\Jobs;

use App\Data\TournamentMatchData;
use App\Models\TournamentMatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;

class SendUpcomingMatchNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public TournamentMatchData $currentTournamentMatchData)
    {
    }

    public function handle(): void
    {
        $nextMatches = TournamentMatch::where('sort', '>', $this->currentTournamentMatchData->sort)
            ->orderBy('sort')
            ->take(2)
            ->load('homeTeam', 'awayTeam')
            ->get();

        if (!isset($nextMatches[1])) {
            Log::info('No upcoming matches meet the criteria');
            return;
        }

        $homeTokens = $nextMatches[1]->homeTeam->push_tokens;
        $awayTokens = $nextMatches[1]->awayTeam->push_tokens;

        $messaging = app('firebase.messaging');

        $androidConfig = AndroidConfig::fromArray([
            'priority' => 'high',
            'notification' => [
                'color' => "#ef4444",
                'sound' => 'default',
            ],
        ]);

        $awayTeamName = e($nextMatches[1]->away_team_name);
        $homeMessage = CloudMessage::new()->withNotification([
            'title' => 'Hamarosan meccsetek lesz!',
            'body' => "A következő meccs után már a tiétek következik! {$awayTeamName} ellen fogtok játszani a hazai oldalon.",
        ])->withAndroidConfig($androidConfig);

        $homeTeamName = e($nextMatches[1]->home_team_name);
        $awayMessage = CloudMessage::new()->withNotification([
            'title' => 'Hamarosan meccsetek lesz!',
            'body' => "A következő meccs után már a tiétek következik! {$homeTeamName} ellen fogtok játszani a vendég oldalon.",
        ])->withAndroidConfig($androidConfig);

        $messaging->sendMulticast($homeMessage, $homeTokens);
        $messaging->sendMulticast($awayMessage, $awayTokens);
    }
}
