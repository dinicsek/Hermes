<?php

namespace App\Filament\Manager\Resources\TournamentMatchResource\Pages;

use App\Filament\Manager\Resources\TournamentMatchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTournamentMatches extends ListRecords
{
    protected static string $resource = TournamentMatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
