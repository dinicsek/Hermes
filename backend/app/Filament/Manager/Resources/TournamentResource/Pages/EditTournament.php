<?php

namespace App\Filament\Manager\Resources\TournamentResource\Pages;

use App\Filament\Manager\Resources\TournamentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTournament extends EditRecord
{
    protected static string $resource = TournamentResource::class;

    protected static ?string $navigationLabel = "Szerkesztés";

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
