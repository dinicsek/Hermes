<?php

namespace App\Filament\Manager\Resources\TournamentResource\Pages;

use App\Filament\Manager\Resources\TournamentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTournaments extends ListRecords
{
    protected static string $resource = TournamentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
