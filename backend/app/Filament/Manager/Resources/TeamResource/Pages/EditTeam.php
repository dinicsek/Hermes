<?php

namespace App\Filament\Manager\Resources\TeamResource\Pages;

use App\Filament\Manager\Resources\TeamResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeam extends EditRecord
{
    protected static string $resource = TeamResource::class;

    protected static ?string $navigationLabel = "Szerkesztés";

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $tournament = $this->record->tournament;

        $data['min_members'] = $tournament->min_team_size;
        $data['max_members'] = $tournament->max_team_size;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['min_members']);
        unset($data['max_members']);

        return $data;
    }
}
