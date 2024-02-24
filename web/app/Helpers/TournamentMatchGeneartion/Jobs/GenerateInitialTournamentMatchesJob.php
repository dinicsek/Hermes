<?php

namespace App\Helpers\TournamentMatchGeneartion\Jobs;

use App\Helpers\TournamentMatchGeneartion\InitialTournamentMatchGenerator;
use App\Models\Tournament;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateInitialTournamentMatchesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Tournament $tournament,
        public array      $excludedTeamIds,
        public User       $userToNotify
    )
    {
    }

    public function handle(): void
    {
        Notification::make('initial_tournament_match_generation_started')
            ->title('A meccsek generálása elkezdődött a háttérben')
            ->info()
            ->body('Kapni fogsz egy újabb értesítést, amikor a generálás befejeződött.')
            ->sendToDatabase($this->userToNotify);
        $generator = new InitialTournamentMatchGenerator();
        $generator->generate($this->tournament, $this->excludedTeamIds);
        Notification::make('initial_tournament_match_generation_finished')
            ->title('A meccsek generálása befejeződött')
            ->success()
            ->body('A generálás befejeződött, frissítsd a meccsek oldalt, hogy lásd az új meccseket!')
            ->sendToDatabase($this->userToNotify);
    }
}
