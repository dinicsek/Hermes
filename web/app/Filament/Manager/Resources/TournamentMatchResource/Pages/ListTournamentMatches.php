<?php

namespace App\Filament\Manager\Resources\TournamentMatchResource\Pages;

use App\Filament\Manager\Resources\TournamentMatchResource;
use App\Helpers\TournamentMatchGeneartion\Jobs\GenerateInitialTournamentMatchesJob;
use App\Models\Team;
use App\Models\Tournament;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Resources\Pages\ListRecords;
use Livewire\Attributes\On;

class ListTournamentMatches extends ListRecords
{
    protected static string $resource = TournamentMatchResource::class;

    #[On('match-changed')]
    public function refresh(): void
    {
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generate_initial_matches')
                ->label('Első fordulós meccsek generálása')
                ->color('gray')
                ->form([
                    Select::make('tournament_id')
                        ->label('Tournament')
                        ->options(fn() => Tournament::query()->pluck('name', 'id'))
                        ->native(false)
                        ->selectablePlaceholder(false)
                        ->live()
                        ->required(),
                    Select::make('exclude_team_ids')
                        ->label('Kihagyandó csapatok')
                        ->options(fn(Get $get) => Team::query()->where('tournament_id', $get('tournament_id'))->pluck('name', 'id'))
                        ->native(false)
                        ->selectablePlaceholder(false)
                        ->multiple()
                        ->visible(fn(Get $get) => $get('tournament_id') !== null)
                ])
                ->action(function (array $data) {
                    $tournament = Tournament::find($data['tournament_id']);
                    $excludedTeamIds = $data['exclude_team_ids'] ?? [];
                    GenerateInitialTournamentMatchesJob::dispatch($tournament, $excludedTeamIds, auth()->user());
                }),
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TournamentMatchResource\Widgets\ManageTournamentMatch::class
        ];
    }
}
