<?php

namespace App\Filament\Manager\Resources\TeamResource\Pages;

use App\Events\TeamApprovedEvent;
use App\Filament\Manager\Resources\TeamResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTeam extends CreateRecord
{
    protected static string $resource = TeamResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['min_members']);
        unset($data['max_members']);
        unset($data['max_approved_teams']);
        unset($data['approved_teams']);

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->record->is_approved) {
            TeamApprovedEvent::dispatch($this->record);
        }
    }
}
