<?php

namespace App\Filament\Manager\Resources\GroupResource\Pages;

use App\Filament\Manager\Resources\GroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGroups extends ListRecords
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
        ];
    }
}
