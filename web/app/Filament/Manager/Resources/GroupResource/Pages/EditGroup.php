<?php

namespace App\Filament\Manager\Resources\GroupResource\Pages;

use App\Filament\Manager\Resources\GroupResource;
use Filament\Resources\Pages\EditRecord;

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
}
