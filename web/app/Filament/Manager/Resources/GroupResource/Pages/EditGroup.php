<?php

namespace App\Filament\Manager\Resources\GroupResource\Pages;

use App\Filament\Manager\Resources\GroupResource;
use App\Models\TournamentMatch;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;

class EditGroup extends EditRecord
{
    protected static string $resource = GroupResource::class;

    public function afterSave()
    {
        if ($this->record->wasChanged(['tournament_id'])) {
            $this->record->teams()->detach();
            $this->dispatch('refresh_teams');
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_group_matches')->label('Meccsek generálása')->color('gray')->requiresConfirmation()->action(function (EditGroup $livewire) {
                $group = $livewire->getRecord();
                $tournament = $group->tournament;

                $teamIds = $group->load('teams')->teams->pluck('id')->toArray();

                $matches = [];

                for ($i = 0; $i < count($teamIds) - 1; $i++) {
                    for ($j = $i + 1; $j < count($teamIds); $j++) {
                        $matches[] = [$teamIds[$i], $teamIds[$j]];
                    }
                }

                collect($matches)->each(function ($teams) use ($group, $tournament) {
                    TournamentMatch::create([
                        'home_team_id' => $teams[0],
                        'away_team_id' => $teams[1],
                        'round' => $group->round,
                        'tournament_id' => $tournament->id,
                    ]);
                });

                Log::debug('Created matches for group: ' . $group->id . ' ' . $group->name);
                Log::debug('Matches: ' . count($matches));

                Notification::make()->title('A meccsek generálása befejeződött')->success()->body(count($matches) . ' új meccs sikeresen legenerálva.')->send();
            })
        ];
    }
}
