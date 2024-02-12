<?php

namespace App\Filament\Manager\Resources\TournamentMatchResource\Pages;

use App\Filament\Manager\Resources\TournamentMatchResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTournamentMatch extends ViewRecord
{
    protected static string $resource = TournamentMatchResource::class;

    protected static ?string $navigationLabel = "Megtekintés";

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
