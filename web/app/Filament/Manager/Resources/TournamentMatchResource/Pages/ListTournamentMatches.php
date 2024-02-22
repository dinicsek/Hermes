<?php

namespace App\Filament\Manager\Resources\TournamentMatchResource\Pages;

use App\Filament\Manager\Resources\TournamentMatchResource;
use Filament\Actions;
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
