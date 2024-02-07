<?php

namespace App\Filament\Manager\Resources\TournamentResource\Pages;

use App\Filament\Manager\Resources\TournamentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTournament extends ViewRecord
{
    protected static string $resource = TournamentResource::class;

    protected static ?string $navigationLabel = "Megtekintés";

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
